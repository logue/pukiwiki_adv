<?php
// $Id: recent.inc.php,v 1.26.9 2012/03/31 18:22:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2008 PukiWiki Plus! Team
//   2002-2007 PukiWiki Developers Team
//   2002      Y.MASUI http://masui.net/pukiwiki/ masui@masui.net
// License: GPL version 2
//
// Recent plugin -- Show RecentChanges list
//   * Usually used at 'MenuBar' page
//   * Also used at special-page, without no #recnet at 'MenuBar'

// Default number of 'Show latest N changes'
define('PLUGIN_RECENT_DEFAULT_LINES', 10);

// Limit number of executions
define('PLUGIN_RECENT_EXEC_LIMIT', 3); // N times per one output

// ----

define('PLUGIN_RECENT_USAGE', '#recent(number-to-show)');
use PukiWiki\Factory;
use PukiWiki\Recent;

function plugin_recent_convert()
{
	global $vars, $date_format, $link_compact, $page_title; // , $_recent_plugin_frame;
	static $exec_count = 1;
	global $cache;

	if ( empty($vars['page']) ) return null;
	$recent_lines = PLUGIN_RECENT_DEFAULT_LINES;
	$args = func_get_args();
	if (! empty($args)) {
		if (isset($args[1]) || ! is_numeric($args[0])) {
			return PLUGIN_RECENT_USAGE . '<br />';
		}
		$recent_lines = & $args[0];
	}

	if ($exec_count++ > PLUGIN_RECENT_EXEC_LIMIT) {
		return '<div class="alert alert-warning">#recent(): You called me too much.</div>' . "\n";
	}

	$date = '';
	$items = array();


	$lines = Recent::get();
	if ($lines !== null){
		$count = (count($lines) < $recent_lines) ? count($lines) : $recent_lines;
		$i = 0;
		foreach ($lines as $page => $time) {
			$wiki = Factory::Wiki($page);
			if (! $wiki->isReadable()) continue;
			//if (! $wiki->isHidden()) continue;
			if ($i > $count) break;

			$s_page = htmlsc($page);
			$_date = get_date($date_format, $time);

			if (!IS_MOBILE){
				if ($date !== $_date) {
					// End of the day
					if (!empty($date)) $items[] = '</ul></li>';

					// New day
					$date = $_date;
					$items[] = '<il><strong>' . $date . '</strong>';
					$items[] = '<ul class="plugin-recent-list">';
				}

				if($page === $vars['page']) {
					// No need to link to the page you just read, or notify where you just read
					$items[] = ' <li>' . $s_page . '</li>';
				} else {
					$passage = !$link_compact ? ' ' . $wiki->passage(false,true) : '';
					$items[] = ' <li><a href="' . $wiki->uri() . '" title="' . $s_page . $passage . '">' . $s_page . '</a></li>';
				}
			}else{
				if ($date !== $_date) {
					// New day
					$date = $_date;
					$items[] = '<li data-role="list-divider">' . $date . '</li>';
				}
				if($page === $vars['page']) {
					// No need to link to the page you just read, or notify where you just read
					$items[] = ' <li data-theme="e">' . $s_page . '</li>';
				} else {
					$passage = !$link_compact ? ' ' . '<span class="ui-li-count">'.$wiki->passage(false,false).'</span>' : '';
					$items[] = ' <li><a href="' . $wiki->uri() . '" data-transition="slide">' . $s_page . $passage.'</a></li>';
				}
			}
			$i++;
		}
		unset($lines,$i);
	}
	if ($date !== '') $items[] = '</ul>';
	// End of the day

	$_recent_title = sprintf(T_('recent(%d)'),$count);
	if (!IS_MOBILE) {
		return '<h5>'.$_recent_title.'</h5>'. "\n" .
				'<div class="hslice" id="webslice">'. "\n" .
				'<span class="entry-title" style="display:none;">'.$page_title.'</span>' . "\n" .
				'<div class="entry-content">' . "\n" . 
				'<ul class="list-unstyled">' . "\n" .join("\n",$items). "\n" . '</ul>' . "\n" . '</div>' . "\n" . '</div>';
	}else{
		return '<ul data-role="listview" data-dividertheme="b">'."\n".
			'<li data-theme="a">'.$_recent_title.'</li>'."\n".
			join("\n",$items).'</ul>' ."\n";
	}
}
/* End of file recent.inc.php */
/* Location: ./wiki-common/plugin/recent.inc.php */
