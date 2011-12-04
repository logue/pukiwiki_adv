<?php
// $Id: recent.inc.php,v 1.26.7 2011/12/01 20:32:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
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

// Recent html cache
//define('PLUGIN_RECENT_CACHE', CACHE_DIR . 'plugin-recent.txt');

function plugin_recent_convert()
{
	global $vars, $date_format, $show_passage, $page_title; // , $_recent_plugin_frame;
	static $exec_count = 1;
	global $memcache;

	$recent_lines = PLUGIN_RECENT_DEFAULT_LINES;
	$args = func_get_args();
	if (! empty($args)) {
		if (isset($args[1]) || ! is_numeric($args[0])) {
			return PLUGIN_RECENT_USAGE . '<br />';
		}
		$recent_lines = & $args[0];
	}

	if ($exec_count++ > PLUGIN_RECENT_EXEC_LIMIT) {
		return '#recent(): You called me too much' . '<br />';
	}

	$auth_key = auth::get_user_info();
	$date = '';
	$items = array();

	if ($memcache === null){
		if (! file_exists(CACHE_DIR.PKWK_MAXSHOW_CACHE)) {
			put_lastmodified();
			return '#recent(): Now generating recent data. Please reload.' . '<br />';
		}
/*
		if (file_exists(PLUGIN_RECENT_CACHE)) {
			$time_recent = filemtime(CACHE_DIR.PKWK_MAXSHOW_CACHE);
			$time_recent_cache = filemtime(PLUGIN_RECENT_CACHE);
			if ($time_recent <= $time_recent_cache) {
				return get_file_contents(PLUGIN_RECENT_CACHE);
			}
		}
*/
		$lines = file_head(CACHE_DIR.PKWK_MAXSHOW_CACHE, $recent_lines);
		if ($lines == FALSE) {
			return '#recent(): File can not open' . '<br />';
		}

		foreach ($lines as $line) {
			list($time, $page) = explode("\t", rtrim($line));

			if (! auth::is_page_readable($page,$auth_key['key'],$auth_key['group'])) continue;

			$_date = get_date($date_format, $time);
			if ($date != $_date) {
				// End of the day
				if ($date != '') $items[] = '</ul>';

				// New day
				$date = $_date;
				$items[] = '<strong>' . $date . '</strong>';
				$items[] = '<ul class="recent_list">';
			}

			$s_page = htmlsc($page);

			if($page === $vars['page']) {
				// No need to link to the page you just read, or notify where you just read
				$items[] = ' <li>' . $s_page . '</li>';
			} else {
				$passage = $show_passage ? ' ' . get_passage($time) : '';
				$items[] = ' <li><a href="' . get_page_uri($page) . '"' . 
					' title="' . $s_page . $passage . '">' . $s_page . '</a></li>';
			}
		}
		$count = count($lines);
	}else{
		$recent_cache_name = substr(PKWK_MAXSHOW_CACHE,0,strrpos(PKWK_MAXSHOW_CACHE, '.'));
/*
		if (file_exists(PLUGIN_RECENT_CACHE)) {
			$time_recent = $memcache->get(MEMCACHE_PREFIX.'timestamp-'.$recent_cache_name);
			$time_recent_cache = filemtime(PLUGIN_RECENT_CACHE);
			if ($time_recent <= $time_recent_cache) {
				return get_file_contents(PLUGIN_RECENT_CACHE);
			}
		}
*/
		$lines = $memcache->get(MEMCACHE_PREFIX.$recent_cache_name);
		if ($lines !== FALSE){
			
			$count = (count($lines) < $recent_lines) ? count($lines) : $recent_lines;
			$i = 0;
			foreach($lines as $page => $time){
				if (! auth::is_page_readable($page,$auth_key['key'],$auth_key['group'])) continue;
				if ($i > $count) break;

				$_date = get_date($date_format, $time);
				if ($date != $_date) {
					// End of the day
					if ($date != '') $items[] = '</ul>';

					// New day
					$date = $_date;
					$items[] = '<strong>' . $date . '</strong>';
					$items[] = '<ul class="recent_list">';
				}

				$s_page = htmlsc($page);

				if($page === $vars['page']) {
					// No need to link to the page you just read, or notify where you just read
					$items[] = ' <li>' . $s_page . '</li>';
				} else {
					$passage = $show_passage ? ' ' . get_passage($time) : '';
					$items[] = ' <li><a href="' . get_page_uri($page) . '"' . 
						' title="' . $s_page . $passage . '">' . $s_page . '</a></li>';
				}
				$i++;
			}
			unset($lines,$i);
		}else{
			$count = 0;
			put_lastmodified();
			return '#recent(): Now generating recent data. Please reload.' . '<br />';
		}
	}

	$_recent_title = sprintf(T_('recent(%d)'),$count);
	$_recent_plugin_frame = '<h5>'.$_recent_title.'</h5>'.
				'<div class="hslice" id="webslice">'.
				'<span class="entry-title" style="display:none;">'.$page_title.'</span>'.
				'<div class="entry-content">';

	// End of the day
	if ($date != '') $items[] = '</ul>';
	
	// Last "\n"
	$items[] = '';
	
	return $_recent_plugin_frame . join("\n",$items) . '</div></div>';
}
?>
