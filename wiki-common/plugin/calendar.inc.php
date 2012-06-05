<?php
// $Id: calendar2.inc.php,v 1.24 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C) 2002-2005, 2007, 2011 PukiWiki Developers Team

// Calendar plugin
//
// Usage:
//	#calendar({[pagename|*],[yyyymm],[off]})
//	off: Don't view today's

// Notice: This plugin based on official calendar2.inc.php r1.24 and Plus!'s minicalendar.inc.php.
//         Adv. is rejected to original calendar.inc.php.

defined('PLUGIN_CALENDAR_PAGENAME_FORMAT') or define('PLUGIN_CALENDAR_PAGENAME_FORMAT', '%04d-%02d-%02d');	// YYYY-MM-DD

function plugin_calendar_init(){
	$messages = array(
		'_calendar_msg' => array(
			'_calendar_title_format'	=> T_('M, Y'),	// Apr, 2011
			'_page_title'				=> T_('%1$s %2$s, %3$d Calendar'),	// FrontPage April, 2011 Calendar
			'_edit'						=> T_('[edit]'),
			'_empty'					=> T_('%s is empty.')
		),
		'_calendar_viewer_msg' => array(
			'_title_format'	=> T_('%1s, %2s %3s %4s')	// Sat, 12 Mar 2011
		)
	);
	set_plugin_messages($messages);
}

function plugin_calendar_convert()
{
	global $vars, $post, $get, $pkwk_dtd ,$_labels, $WikiName, $BracketName;
	//global $_calendar_plugin_edit, $_calendar_plugin_empty;
	global $_calendar_msg, $_calendar_viewer_msg;

	/* from Plus! */
	$today_view = TRUE;
	$today_args = 'view';

	$date_str = get_date('Ym');
	$base     = strip_bracket($vars['page']);

	$today_view = TRUE;
	if (func_num_args() > 0) {
		$args = func_get_args();
		foreach ($args as $arg) {
			if (is_numeric($arg) && strlen($arg) == 6) {
				$date_str = $arg;
			} else if ($arg == 'off') {
				$today_view = FALSE;
			} else if ($arg == 'past' || $arg == 'pastex' || $arg == 'future' || $arg == 'futureex' || $arg == 'view' ||$arg == 'viewex') {
				/* from Plus! */
				$today_args = $arg;
			} else {
				$base = strip_bracket($arg);
			}
		}
	}
	if ($base == '*') {
		$base   = '';
		$prefix = '';
	} else {
		$prefix = $base . '/';
	}
	$s_base   = htmlsc($base);
	$s_prefix = htmlsc($prefix);

	$yr  = substr($date_str, 0, 4);
	$mon = substr($date_str, 4, 2);

	if ($yr != get_date('Y') || $mon != get_date('m')) {
		$now_day = 1;
		$other_month = 1;
	} else {
		$now_day = get_date('d');
		$other_month = 0;
	}

	$today = getdate(mktime(0,0,0,$mon,$now_day,$yr));

	$m_num = $today['mon'];
	$d_num = $today['mday'];
	$year  = $today['year'];

	$f_today = getdate(mktime(0,0,0,$m_num,1,$year));
	$wday = $f_today['wday'];
	$day  = 1;

	$m_name = format_date($today[0] ,false, $_calendar_msg['_calendar_title_format']);

	$y = substr($date_str, 0, 4) + 0;
	$m = substr($date_str, 4, 2) + 0;
	
	$format = '%04d%02d';
	$prev_link = get_cmd_uri('calendar','','',
		array(
			'file'=>$base,
			'mode'=>$today_args,
			'date'=>(($m == 1) ? sprintf($format, $y - 1, 12) : sprintf($format, $y, $m - 1))
		)
	);
	$next_link = get_cmd_uri('calendar','','',
		array(
			'file'=>$base,
			'mode'=>$today_args,
			'date'=>(($m == 12) ? sprintf($format, $y + 1, 1) : sprintf($format, $y, $m + 1))
		)
	);
	$this_date_str = sprintf($format,$y,$m);
	$page_YM = sprintf('%04d-%02d',$y,$m);

	$ret = '';
	if ($today_view === TRUE) {
		$ret .= '<div class="clearfix">'."\n".	// 外枠
			'<div class="style_calendar_viewer">'."\n";	// カレンダーのdivタグ（$today_view有効時のみ出力）
	}
	
	$ret .= <<<EOD
<table class="style_table style_calendar" summary="calendar">
	<thead>
		<tr>
			<td class="style_td style_calendar_top" colspan="7">
				<nav>
					<ul class="style_calendar_navi">
						<li class="style_calendar_prev"><a href="$prev_link">&lt;&lt;</a></li>
						<li class="style_calendar_title"><strong>$m_name</strong></li>
						<li class="style_calendar_next"><a href="$next_link">&gt;&gt;</a></li>
					</ul>
				</nav>
EOD;

	if ($vars['cmd'] == 'calendar' || $vars['cmd'] == 'calendar_viewer') {
		$base_link = get_page_uri($base);
	}else{
		$base_link = get_cmd_uri('calendar','','',array('file'=>$base,'mode'=>$today_args,'date'=>sprintf($format,$y,$m)));
	}
	
	if ($prefix) $ret .= "\n" .
		'				[<a href="' . $base_link . '">' . $s_base . '</a>]';

	$ret .= "\n" .
		'			</td>' . "\n" .
		'		</tr>'  . "\n" .
		'	</thead>'."\n".
		'	<tbody>'."\n".
		'		<tr>'   . "\n";

	for ($i = 0; $i < 7; $i++){
		if ($i == 0){
			$class = 'week_sun';
		}else if($i == 6){
			$class = 'week_sat';
		}else{
			$class = 'week_day';
		}
		$ret .= '			<th class="style_th style_calendar_week"><abbr title="'.$_labels['week'][$i][1].'" class="'.$class.'">' . $_labels['week'][$i][0] . '</abbr></th>' . "\n";
	}
	unset($i,$class);

	$ret .= '		</tr>' . "\n" .
		'		<tr>'  . "\n";
	// Blank
	for ($i = 0; $i < $wday; $i++)
		$ret .= '			<td class="style_td_blank"></td>' . "\n";

	while (checkdate($m_num, $day, $year)) {
		$dt     = sprintf(PLUGIN_CALENDAR_PAGENAME_FORMAT, $year, $m_num, $day);
		$page   = $prefix . $dt;
		$s_page = htmlsc($page);

		if ($wday == 0 && $day > 1)
			$ret .=
			'		</tr>' . "\n" .
			'		<tr>' . "\n";
		
		/* from Plus! */
		$h_today = public_holiday($year, $m_num, $day);
		$hday = $h_today['rc'];

		$style = 'style_calendar_day'; // Weekday
		if (! $other_month && ($day == $today['mday']) && ($m_num == $today['mon']) && ($year == $today['year'])){
			// Today
			$style = 'style_calendar_today';
		} else if ($hday !== 0) {
			// Holiday
			$style = 'style_calendar_holiday';
		} else if ($wday == 0) {
			// Sunday 
			$style = 'style_calendar_sun';
		} else if ($wday == 6) {
			// Saturday
			$style = 'style_calendar_sat';
		}

		if (is_page($page)) {
			$link = '<a href="' . get_page_uri($page) . '" title="' . $s_page . '"><strong>' . $day . '</strong></a>';
		} else {
			if (PKWK_READONLY) {
				$link = $day;
			} else {
				$link = '<a href="' . get_cmd_uri('edit',$page,'',array('refer'=>$base)) . '" title="' . $s_page . '" rel="nofollow">' . $day . '</a>';
			}
		}

		$ret .= '			<td class="style_td ' . $style . '">' . $link .'</td>' . "\n";
		++$day;
		$wday = ++$wday % 7;
	}

	if ($wday > 0)
		while ($wday++ < 7) // Blank
			$ret .= '			<td class="style_td_blank"></td>' . "\n";

	$ret .= '		</tr>'   . "\n" .
		'	</tbody>'."\n".
		'</table>' . "\n";

	if ($today_view) {
		$ret .= '</div>'."\n";	// カレンダーのdivタグを閉じる
		$ret .= (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section class="style_calendar_post">' : '<div class="style_calendar_post">')."\n";
		if ($today_args == '') {
			$str = (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<article id="'.$tpage.'" class="style_calendar_post">' : '<div id="'.$tpage.'" class="style_calendar_post">')."\n";
			global $pkwk_dtd;
			$tpage = $prefix . sprintf(PLUGIN_CALENDAR_PANENAME_FORMAT, $today['year'], $today['mon'], $today['mday']);
			if (is_page($tpage)) {
				$_page = $vars['page'];
				$get['page'] = $post['page'] = $vars['page'] = $tpage;
				$source = get_source($tpage);
				preg_replace('/^#navi/','/\/\/#navi/',$source);
				$str .= convert_html($source);
				$str .= '<hr /><a href="' . get_cmd_uri('edit', $tpage).'">' . $_calendar_msg['_edit'] . '</a>';
				$get['page'] = $post['page'] = $vars['page'] = $_page;
			} else {
				$str .= sprintf($_calendar_msg['_empty'],
					make_pagelink($prefix . sprintf(PLUGIN_CALENDAR_PANENAME_FORMAT,$today['year'], $today['mon'], $today['mday'])));
			}
			$str .= (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</article>' : '</div>')."\n";
		} else {
			$aryargs = array(rawurldecode($base), $page_YM, $today_args);
			if (exist_plugin('calendar_viewer')) {
				T_bindtextdomain('calendar_viewer', LANG_DIR);
				T_bind_textdomain_codeset('calendar_viewer', SOURCE_ENCODING);
				T_textdomain('calendar_viewer');
				$str = call_user_func_array('plugin_calendar_viewer_convert',$aryargs);
				T_textdomain('calendar');
			}
		}
		$ret .= $str . "\n";
		$ret .= (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>' : '</div>')."\n";
		$ret .= '</div>' . "\n";
	}

	return $ret;
}

function plugin_calendar_action()
{
	global $vars;
	global $_calendar_msg, $_labels;

	$page = strip_bracket($vars['page']);
	$vars['page'] = ($vars['file']) ? $vars['file'] : '*';

	$date = $vars['date'] ? $vars['date'] : get_date('Ym');
	$mode = $vars['mode'] ? $vars['mode'] : 'view';

	$year = substr($date, 0, 4);
	$month = preg_replace('/^0/','',substr($date, 4, 2));

	$aryargs = array($vars['page'], $date);
	// $s_page  = '<a href="'.get_page_uri($vars['page']).'">'.htmlsc($vars['page']).'</a>';
	$s_page  = htmlsc($vars['page']);

	$vars['page'] = $page;

	return array(
		'msg'=>sprintf($_calendar_msg['_page_title'], $s_page, $_labels['month'][$month][1], $year),
		'body'=>call_user_func_array('plugin_calendar_convert', $aryargs)
	);
}
/* End of file calendar.inc.php */
/* Location: ./wiki-common/plugin/calendar.inc.php */
