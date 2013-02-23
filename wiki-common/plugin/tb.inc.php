<?php
// $Id: tb.inc.php,v 1.19.38 2011/02/07 22:34:00 Logue Exp $
/*
 * PukiWiki/TrackBack: TrackBack Ping receiver and viewer
 * (C) 2010-2011 PukiWiki Advance Developers Team
 * (C) 2007 PukiWiki Plus! Team
 * (C) 2003-2005 PukiWiki Developers Team
 * (C) 2003,2005-2008 Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * License: GPL
 *
 * plugin_tb_convert()          block plugin
 * plugin_tb_action()           action plugin
 * plugin_tb_inline()           inline plugin
 * plugin_tb_save($url, $tb_id) Save or update TrackBack Ping data
 * plugin_tb_return($rc, $msg)  Return TrackBack ping via HTTP/XML
 * plugin_tb_mode_rss($tb_id)   ?__mode=rss
 * plugin_tb_mode_view($tb_id)  ?__mode=view
 * plugin_tb_recent($line)
 */
use PukiWiki\Auth\Auth;
// Trackback site check.(Is foreign site linked my site?)
defined('PLUGIN_TB_SITE_CHECK') or define('PLUGIN_TB_SITE_CHECK', TRUE);
// If trackback error, 'HTTP/1.0 400 Bad Request'
defined('PLUGIN_TB_HTTP_ERROR') or define('PLUGIN_TB_HTTP_ERROR', FALSE);

define('PLUGIN_TB_OK',      0);
define('PLUGIN_TB_ERROR',   1);

use Zend\Http\ClientStatic;
use Zend\Http\Request;
use Zend\Http\Client;

function plugin_tb_init()
{
	$msg = array(
		'_tb_msg' => array(
			'title_tb'					=> T_('TrackBack'),
			'title_tb_list'				=> T_('List of TrackBack'),
			'msg_tb_list'				=> T_('TrackBack list to %s'),
			'msg_tb_url'				=> T_('TrackBack URL'),
			'msg_weblog'				=> T_('Blog:'),
			'msg_tracked'				=> T_('Date:'),
			'msg_date'					=> T_('F j, Y, g:i A'),
			'msg_recent'				=> T_('RECENT TRACKBACK'),
			'err_disabled'				=> T_('TrackBack feature disabled.'),
			'err_notbs'					=> T_('No TrackBack entrys.'),
			'err_noparam'				=> T_('URL parameter is not set.'),
			'err_noping_id'				=> T_('TrackBack Ping ID is not set.'),
			'err_directory'				=> sprintf(T_('TrackBack directory <var>%s</var> is not found.'),TRACKBACK_DIR),
			'err_nowritable'			=> sprintf(T_('TrackBack directory <var>%s</var> is not writable.'),TRACKBACK_DIR),
			'err_invalid'				=> T_('TrackBack ID is invalid.'),
			'err_invalid_url'			=> T_('The URL is fictitious.'),
			'err_write_prohibited'		=> T_('Writing is prohibited.')
		)
	);
	set_plugin_messages($msg);
}

function plugin_tb_convert()
{
	global $vars,$trackback;

	if (! $trackback) return;

	$argv = func_get_args();
	$argc = func_num_args();

	$field = array('cmd','line');
	for($i=0; $i<$argc; $i++) {
		$$field[$i] = htmlsc($argv[$i], ENT_QUOTES);
	}

	if (empty($cmd)) $cmd = 'list';
	if (empty($line)) $line = 0;

	switch ( $cmd ) {
	case 'recent':
		return plugin_tb_recent($vars['page'],$line);
	// case 'list':
	default:
		return plugin_tb_mode_view_set($vars['page']);
	}
}

function plugin_tb_action()
{
	global $vars, $trackback,$_tb_msg;

	if (isset($vars['url'])) {
		// Receive and save a TrackBack Ping (both GET and POST)
		$url   = $vars['url'];
		$tb_id = isset($vars['tb_id']) ? $vars['tb_id'] : '';
		plugin_tb_save($url, $tb_id); // Send a response (and exit)

	} else {
		if ($trackback && isset($vars['__mode']) && isset($vars['tb_id'])) {
			// Show TrackBacks received (and exit)
			switch ($vars['__mode']) {
			case 'rss' : plugin_tb_mode_rss($vars['tb_id']);  break;
			// case 'view': plugin_tb_mode_view($vars['tb_id']); break;
			case 'view': return plugin_tb_mode_view($vars['tb_id']);
			}
		}

		// Show List of pages that TrackBacks reached
		$pages = Auth::get_existpages(TRACKBACK_DIR, '.txt');
		if (! empty($pages)) {
			return array(
				'msg'=>$_tb_msg['title_tb'],
				'body'=>page_list($pages, 'read', FALSE)
			);
		} else {
			return array('msg'=>$_tb_msg['title_tb'], 'body'=>$_tb_msg['err_notbs']);
		}
	}
}

function plugin_tb_inline()
{
	global $vars, $trackback;

	if (! $trackback) return '';

	$argv = func_get_args();
	$argc = func_num_args();

	$field = array('page');
	for($i=0; $i<$argc; $i++) {
		$$field[$i] = htmlsc($argv[$i], ENT_QUOTES);
	}
	if (empty($page)) $page = $vars['page'];

	$tb_id = tb_get_id($page);

	return get_script_absuri() . '?tb_id=' . $tb_id;
}

// Save or update TrackBack Ping data
function plugin_tb_save($url, $tb_id)
{
	global $vars, $trackback, $use_spam_check, $_tb_msg;
	static $fields = array( /* UTIME, */ 'url', 'title', 'excerpt', 'blog_name');

	$die = '';
	if (! $trackback) $die .= $_tb_msg['err_disabled'];
	if ($url   == '') $die .= $_tb_msg['err_noparam'];
	if ($tb_id == '') $die .= $_tb_msg['err_noping_id'];
	if ($die != '') plugin_tb_return(PLUGIN_TB_ERROR, $die);

	if (! file_exists(TRACKBACK_DIR)) plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_directory']);
	if (! is_writable(TRACKBACK_DIR)) plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_nowritable']);

	$page = tb_id2page($tb_id);
	if ($page === FALSE) plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_invalid']);

	// URL validation (maybe worse of processing time limit)
	if (!is_url($url)) plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_invalid_url']);

	if (PLUGIN_TB_SITE_CHECK === TRUE) {
		/*
		$result = pkwk_http_request($url);
		if ($result['rc'] !== 200) plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_invalid_url']);
		 */
		$response = ClientStatic::get($target);
		if (!$response->isSuccess()){
			plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_invalid_url']);
		}
		$urlbase = get_script_absuri();
		$matches = array();
		if (preg_match_all('#' . preg_quote($urlbase, '#') . '#i', $result['data'], $matches) == 0) {
			honeypot_write();
			if (PLUGIN_TB_HTTP_ERROR === TRUE && is_sapi_clicgi() === FALSE) {
				header('HTTP/1.0 403 Forbidden');
				exit;
			}
			plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_write_prohibited']);
		}
	} else {
		/*
		$result = pkwk_http_request($url, 'HEAD');
		if ($result['rc'] !== 200) plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_invalid_url']);
		 */
		$request = new Request();
		$request->setUri($url);
		$request->setMethod('HEAD');
		$client = new Client();
		$response = $client->dispatch($request);
		if (! $response->isSuccess()) {
			plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_invalid_url']);
		}
	}

	// Update TrackBack Ping data
	$filename = tb_get_filename($page);
	$data     = tb_get($filename);

	$matches = array();
	$items = array(UTIME);
	foreach ($fields as $key) {
		$value = isset($vars[$key]) ? $vars[$key] : '';
		if (preg_match('/[,"' . "\n\r" . ']/', $value)) {
			$value = '"' . str_replace('"', '""', $value) . '"';
		}
		$items[$key] = $value;

		// minimum checking from SPAM
		if (preg_match_all('/a\s+href=/i', $items[$key], $matches) >= 1) {
			honeypot_write();
			if (PLUGIN_TB_HTTP_ERROR === TRUE && is_sapi_clicgi() === FALSE) {
				header('HTTP/1.0 400 Bad Request');
				exit;
			}
			plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_write_prohibited']);
		}
	}

	// minimum checking from SPAM #2
	foreach(array('title', 'excerpt', 'blog_name') as $key) {
		if (preg_match_all('#http\://#i', $items[$key], $matches) >= 1) {
			honeypot_write();
			if (PLUGIN_TB_HTTP_ERROR === TRUE && is_sapi_clicgi() === FALSE) {
				header('HTTP/1.0 400 Bad Request');
				exit;
			}
			plugin_tb_return(PLUGIN_TB_ERROR, $_tb_msg['err_write_prohibited']);
		}
	}

	// Blocking SPAM
	if ($use_spam_check['trackback'] && SpamCheck($items['url'])) plugin_tb_return(1, $_tb_msg['err_write_prohibited']);

	$data[rawurldecode($items['url'])] = $items;

	pkwk_touch_file($filename);
	$fp = fopen($filename, 'w');
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	foreach ($data as $line) {
		$line = preg_replace('/[\r\n]/s', '', $line); // One line, one ping
		fwrite($fp, join(',', $line) . "\n");
	}
	flock($fp, LOCK_UN);
	fclose($fp);

	plugin_tb_return(PLUGIN_TB_OK); // Return OK
}

// Show a response code of the ping via HTTP/XML (then exit)
function plugin_tb_return($rc, $msg = '')
{
	if ($rc == PLUGIN_TB_OK) {
		$rc = 0; // for PLUGIN_TB_OK
	} else {
		$rc = 1; // for PLUGIN_TB_ERROR
	}

	pkwk_common_headers();
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="iso-8859-1"?>';
	echo '<response>';
	echo '	<error>' . $rc . '</error>';
	if ($rc) echo '	<message>' . $msg . '</message>';
	echo '</response>';
	exit;
}

// Show pings for the page via RSS (?__mode=rss)
function plugin_tb_mode_rss($tb_id)
{
	global $vars, $entity_pattern, $language;

	$page = tb_id2page($tb_id);
	if ($page === FALSE) return FALSE;

	$items = '';
	foreach (tb_get(tb_get_filename($page)) as $arr) {
		// _utime_, title, excerpt, _blog_name_
		array_shift($arr); // Cut utime
		list ($url, $title, $excerpt) = array_map(
			create_function('$a', 'return htmlsc($a);'), $arr);
		$items .= <<<EOD
	<item>
		<title>$title</title>
		<link>$url</link>
		<description>$excerpt</description>
	</item>
EOD;
	}

	$title = htmlsc($page);
	$link  = get_page_absuri($page);
	$vars['page'] = $page;
	$excerpt = strip_htmltag(convert_html(get_source($page)));
	$excerpt = preg_replace("/&$entity_pattern;/", '', $excerpt);
	$excerpt = mb_strimwidth(preg_replace("/[\r\n]/", ' ', $excerpt), 0, 255, '...');
	$lang    = $language;

	$rc = <<<EOD
<?xml version="1.0" encoding="utf-8" ?>
<response>
	<error>0</error>
	<rss version="0.91">
	<channel>
		<title>$title</title>
		<link>$link</link>
		<description>$excerpt</description>
		<language>$lang</language>$items
	</channel>
	</rss>
</response>
EOD;

	// ToDo: response encoding must equal request encoding.(from trackback reference.)
	pkwk_common_headers();
	header('Content-Type: text/xml');
	echo mb_convert_encoding($rc, 'UTF-8', SOURCE_ENCODING);
	exit;
}

// Show pings for the page via XHTML (?__mode=view)
function plugin_tb_mode_view($tb_id)
{
	global $vars,$_tb_msg;

	$page = tb_id2page($tb_id);
	if ($page === FALSE) return FALSE;

	$vars['page'] = $page; // topicpath
// TrackBack list to aaaaa
// aaaa への TrackBack 一覧

	return array(
		'msg'=>sprintf($_tb_msg['msg_tb_list'], $page),
		'body'=>plugin_tb_mode_view_set($page),
	);
}

function plugin_tb_mode_view_set($page)
{
	global $vars, $_tb_msg;

	$tb_id = tb_get_id($page);

	$body = '<div><fieldset><legend>'.$_tb_msg['msg_tb_url'].'</legend>'.
		'<p>'. get_script_absuri() . '?tb_id=' . $tb_id.'</p>'.
		'</fieldset></div>'."\n";

	$data = tb_get(tb_get_filename($page));

	// Sort: The first is the latest
	usort($data, create_function('$a,$b', 'return $b[0] - $a[0];'));

	foreach ($data as $x) {
		if (count($x) != 5) continue; // Ignore incorrect record

		list ($time, $url, $title, $excerpt, $blog_name) = $x;
		if ($title == '') $title = 'no title';

		$time = get_date($_tb_date, $time);

		$body .= '<div><fieldset class="trackback_info">'.
			 '<legend><a class="ext" href="' . $url . '" rel="nofollow">' . $title .
			 '<img src="'.IMAGE_URI.'plus/ext.png" alt="" title="" class="ext" onclick="return open_uri(\'' .
			 $url . '\', \'_blank\');" /></a></legend>' . "\n".

			 '<p>' . $excerpt . "</p>\n".

			 '<div style="text-align:right">' .
			 '<strong>'.$_tb_msg['msg_tracked'].'</strong>'.$time.'&nbsp;&nbsp;'.
			 '<strong>'.$_tb_msg['msg_weblog'].'</strong>'.$blog_name.
			 '</div>'."\n".

			 '</fieldset></div>'."\n";
	}

	$body .= '<p style="text-align:right">' .
		'<a href="' . get_cmd_uri('tb','','','__mode=view') . '" class="pkwk-icon_linktext cmd-trackback">' .
		$_tb_msg['title_tb_list'] .
		'</a>'. "</p>\n";

	return $body;
}

function plugin_tb_recent($page,$line)
{
	global $_tb_msg;
	$body = '';

	$tb_id = tb_get_id($page);
	$data = tb_get(tb_get_filename($page));
	$ctr = count($data);
	if ($ctr == 0) return '';

	if ($ctr > 1) {
		// Sort: The first is the latest
		usort($data, create_function('$a,$b', 'return $b[0] - $a[0];'));
	}

	$body .= '<h5>' . $_tb_msg['msg_recent'] . "</h5>\n";
	$body .= "<div>\n<ul class=\"recent_list\">\n";
	$i = 0;
	foreach ($data as $x) {
		if (count($x) != 5) continue; // Ignore incorrect record

		list ($time, $url, $title, $excerpt, $blog_name) = $x;
		if ($title == '') $title = 'no title';

		$body .= '<li><a href="' . $url . '" title="' .
			$blog_name . ' ' . get_passage($time) .
			'" rel="nofollow">' . $title . '</a></li>'."\n";
		$i++;
		if ($line == 0) continue;
		if ($i >= $line) break;
	}

	if ($i == 0) return '';

	$body .= "</ul>\n</div>\n";

	return $body;
}

/* End of file tb.inc.php */
/* Location: ./wiki-common/plugin/tb.inc.php */
