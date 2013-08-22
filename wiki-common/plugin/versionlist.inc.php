<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: versionlist.inc.php,v 1.17.4 2011/02/05 12:47:00 Logue Exp $
// Copyright (C)
//	 2011      PukiWiki Advance Developers Team
//	 2005-2007 PukiWiki Plus! Team
//	 2002-2006,2011 PukiWiki Developers Team
//	 2002      S.YOSHIMURA GPL2 yosimura@excellence.ac.jp
// License: GPL v2
//
// Listing cvs revisions of files
use PukiWiki\Auth\Auth;
function plugin_versionlist_action()
{
	// if (PKWK_SAFE_MODE) die_message('PKWK_SAFE_MODE prohibits this');
	if (Auth::check_role('safemode')) die_message('PKWK_SAFE_MODE prohibits this');

	return array(
		'msg' => _('version list'),
		'body' => plugin_versionlist_convert()
	);
}

function plugin_versionlist_convert()
{
	// if (PKWK_SAFE_MODE) return ''; // Show nothi
	if (Auth::check_role('safemode')) return ''; // Show nothi

	// Directories to scan
	$scan['.'       ] = NULL;
	$scan[LIB_DIR   ] = NULL;
	$scan[DATA_HOME ] = NULL;
	$scan[PLUGIN_DIR] = NULL;
	$scan[SKIN_DIR  ] = NULL;
	
	$row = $matches = array();
	foreach (array_keys($scan) as $sdir) {
		if (! $dir = @dir($sdir)) continue;
		if ($sdir == '.') $sdir = '';
		while(FALSE !== ($file = $dir->read())) {
			if (! preg_match('/\.(?:php|css|js)$/i', $file)) continue;
			$path       = $sdir . $file;
			$row[$path] = array();
			$data       = join('', file($path));
			if (preg_match('#\$' . 'Id: .+ (\d+(?:\.\d+)*) (\d{4}[/-]\d{2}[/-]\d{2} \d{2}:\d{2}:\d{2}[^ ]*) (.+) Exp \$#', $data, $matches)) {
				$row[$path]['rev']  = $matches[1];	// "1", "1.23" or "1.23.45.6"
				$row[$path]['date'] = $matches[2];
				$row[$path]['author'] = $matches[3];
			}
		}
		$dir->close();
	}
	
//	ksort($comments);

	$retval = array();
	$retval[] = <<<EOD
<table class="table">
	<thead>
		<tr>
			<th>File</th>
			<th>Revision</th>
			<th>Date</th>
			<th>Author</th>
		</tr>
	</thead>
	<tbody>
EOD;
	foreach (array_keys($row) as $path) {
		$file = htmlsc($path);
		$rev  = isset($row[$path]['rev'])  ? htmlsc($row[$path]['rev'])  : '';
		$date = isset($row[$path]['date']) ? htmlsc($row[$path]['date']) : '';
		$author = isset($row[$path]['author']) ? htmlsc($row[$path]['author']) : '?';
		switch($author){
			case 'henoheno':
				$color = 'blue';
				break;
			case 'upk':
			case 'miko':
				$color = 'red';
				break;
			case 'Logue':
				$color = 'green';
				break;
			default:
				$color = 'black';
			break;
		}
		$retval[] = <<<EOD
		<tr style="color:$color;">
			<td>$file</td>
			<td style="text-align:right">$rev</td>
			<td>$date</td>
			<td>$author</td>
		</tr>
EOD;
		unset($row[$path]);
	}

	$retval[] = <<<EOD
	</tbody>
</table>
EOD;

	return implode("\n", $retval);
}
/* End of file versionlist.inc.php */
/* Location: ./wiki-common/plugin/versionlist.inc.php */