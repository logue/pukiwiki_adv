<?php
/**
 * bluff プラグイン
 *
 * @copyright   Copyright &copy; 2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: bluff.inc.php,v 0.1 200/04/04 05:22:00 upk Exp $
 *
 */

defined('BLUFF_CANVAS_NAME_PREF') or define('BLUFF_CANVAS_NAME_PREF', 'bluff_graph');

function plugin_bluff_init()
{
        $msg = array(
                '_bluff_msg' => array(
                        'msg_no_data'		=> _('No Data'),
			'msg_no_attach_file'	=> _('attach file not found.'),
                )
        );
        set_plugin_messages($msg);
}

function plugin_bluff_convert()
{
	global $vars, $head_tags;
	static $set_bluff = true;
	static $canvas_no = 0;

	$argv = func_get_args();
	$argc = func_num_args();
	//$inline_data = $argv[ --$argc ];
	//array_pop($argv);
	$parm = bluff_set_parm($argv);

        $title = $fields = array();
	if (isset($parm['page']) || isset($parm['label']) || isset($parm['file'])) {
		$page = (isset($parm['page'])) ? $parm['page'] : $vars['page'];
		if (isset($parm['label'])) {
			list($title, $fields) = bluff_get_page_data($page, $parm['label']);
		} else
		if (isset($parm['file'])) {
			list($title, $fields) = bluff_get_attach_data($page, $parm);
		} else {
			list($title, $fields) = bluff_get_inline_data($argv[ --$argc ]);
		}
	} else {
		list($title, $fields) = bluff_get_inline_data($argv[ --$argc ]);
	}

	$canvas_no++;
	$canvas_id = BLUFF_CANVAS_NAME_PREF.$canvas_no;

	if (isset($parm['align'])) {
		$div_s = '<div class="bluff_margin" style="float:'.$parm['align'].'">';
		$div_e = '</div>';
	} else {
		$div_s = $div_e = '';
	}

	$retval  = $div_s . '<canvas id="'.$canvas_id.'"></canvas>' . $div_e;
	$retval .= bluff_tag_clear($parm['clear']);
	$retval .= '<script type="text/javascript">';

	if ($set_bluff) {
		$set_bluff = false;
		// $head_tags[] = ' <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />';
		$head_tags[] = ' <script type="text/javascript" src="'.SKIN_URI.'js/plugin/bluff/js-class.js"></script>';
		$head_tags[] = ' <script type="text/javascript" src="'.SKIN_URI.'js/plugin/bluff/bluff-min.js"></script>';
		$head_tags[] = ' <script type="text/javascript" src="'.SKIN_URI.'js/plugin/bluff/excanvas.js"></script>';
		// アイディア
		// http://www.remus.dti.ne.jp/~a-satomi/nikki/tmp/multiExtJS/test.html
		$retval .= 'var array_onload = new Array();';
		$retval .= 'window.onload = function() { for (var i in array_onload) array_onload[i](); };';
	}

	$retval .= 'array_onload[array_onload.length] = '.$canvas_id.';';
	$retval .= 'function '.$canvas_id.'() {';
	// var g = new Bluff.Line('bluff_graph1', 400);
	$retval .= 'var g = new Bluff.'.$parm['type'].'(\''.$canvas_id.'\', '.$parm['size'].');';
	// g.theme_37signals();
	$retval .= 'g.theme_'.$parm['theme'].'();';
	$retval .= 'g.title = \''.$parm['title'].'\';';
	$retval .= bluff_make_parameter($parm);
	$retval .= bluff_make_data($title, $fields, $parm);

	$retval .= 'g.draw();};</script>';
	return $retval;
}

function bluff_set_parm($argv)
{
	global $_bluff_msg;

	// initialize
 	$parm = array();
	$parm['type'] = 'Line';
	$parm['theme'] = 'pastel';
	$parm['size'] = 640;
	//$parm['align'] = 'left';
	$parm['clear'] = '';
	// $parm['label_step'] = 5;

	$parm['hide_dots'] = $parm['hide_legend'] = $parm['hide_lines'] = false;
	$parm['hide_line_markers'] = $parm['hide_line_numbers'] = false;
	$parm['hide_title'] = false;
	$parm['no_data_message'] = $_bluff_msg['msg_no_data'];

	foreach($argv as $arg) {
		$val = split('=', $arg);
		$val[1] = (empty($val[1])) ? htmlspecialchars($val[0]) : htmlspecialchars($val[1]);

		switch($val[0]) {
		// boolean fields
		case 'sort':
			$parm[$val[0]] = false;
			break;
		case 'hide_dots':		// 線(Line)グラフのみ, Net のみ
		case 'hide_legend':		// 線(Line)グラフのみ, Net のみ
		case 'hide_lines':		// 線(Line)グラフのみ
		case 'hide_line_markers':	// 線(Line)グラフのみ
		case 'hide_line_numbers':	// 線(Line)グラフのみ
		case 'hide_title':		// 線(Line)グラフのみ
			$parm[$val[0]] = true;
			break;
		// 別名がある項目
		case 'top':
			$parm['top_margin'] = $val[1];
			break;
		case 'right':
			$parm['right_margin'] = $val[1];
			break;
		case 'bottom':
			$parm['bottom_margin'] = $val[1];
			break;
		case 'left':
			$parm['left_margin'] = $val[1];
			break;
		case 'min':
			$parm['minimum_value'] = $val[1];
			break;
		case 'max':
			$parm['maximum_value'] = $val[1];
			break;
		case 'no_data':
			$parm['no_data_message'] = $val[1];
			break;
		case 'x':
			$parm['x_axis_label'] = $val[1];
			break;
		case 'y':
			$parm['y_axis_increment'] = $val[1];
			break;
		// 円(Pie)グラフのみ
		case 'zero':
			$parm['zero_degree'] = $val[1];
			break;
		// align, clear
		case 'r':
		//case 'right':
			$parm['align'] = 'right';
			break;
		case 'n':
		//case 'none':
			$parm['align'] = 'none';
			break;
		case 'l':
		//case 'left':
			$parm['align'] = 'left';
			break;
		case 'c':
		case 'clear';
			$parm['clear'] = 'clear';
			break;
		case 'cl':
		case 'clearl':
			$parm['clear'] = 'clearl';
			break;
		case 'cr':
		case 'clearr':
			$parm['clear'] = 'clearr';
			break;
		default:
			$parm[$val[0]] = $val[1];
		}
	}

	// 省略時
	if (!isset($parm['title'])) {
		$parm['title'] = 'My Graph';
	}

	// parameter check
	$type = array(  'AccumulatorBar',
			'Area',
			'Bar',
			'Line',
			'Mini.Bar',
			'Mini.Pie',
			'Mini.SideBar',
			'Net',
			'Pie',
			'SideBar',
			'SideStackedBar',
			'Spider',
			'StackedArea',
			'StackedBar'
		);

	if (!in_array($parm['type'], $type)) {
		$parm['type'] = 'Line';
	}

	$theme = array( 'keynote',
			'37signals',
			'rails_keynote',
			'odeo',
			'pastel',
			'greyscale'
		);

	if (!in_array($parm['theme'], $theme)) {
		$parm['theme'] = 'pastel';
	}

	return $parm;
}

function bluff_tag_clear($x)
{
	switch ($x) {
	case 'clearl': return '<div style="clear:left;display:block;"></div>';
	case 'clearr': return '<div style="clear:right;display:block;"></div>';
	case 'clear' : return '<div style="clear:both;"></div>';
	}
	return '';
}

function bluff_make_parameter(& $parm)
{
	$retval = '';

	// boolean fields
	$bool_f = array('sort');
        foreach($bool_f as $x) {
                if (!isset($parm[$x])) continue;
                $retval .= 'g.'.$x.' = false;';
        }
	$bool_t = array('hide_dots','hide_legend','hide_lines','hide_line_markers','hide_line_numbers','hide_title');
	foreach($bool_t as $x) {
		if (empty($parm[$x])) continue;
		$retval .= 'g.'.$x.' = true;';
	}

	// normal fields
	$field = array( 'baseline_value','baseline_color','font_color',
			'title_font_size','legend_font_size','marker_font_size',
			'top_margin','right_margin','bottom_margin','left_margin',
			'marker_color','marker_count',
			'minimum_value','maximum_value',
			'no_data_message',
			'x_axis_label','y_axis_increment',
			'zero_degree'
			);
	foreach($field as $x) {
		if (empty($parm[$x])) continue;
		$retval .= 'g.'.$x.' = \''.$parm[$x].'\';';
	}

	return $retval;
}

function bluff_get_inline_data(& $data)
{
	$title = $fields = array();
	$lines = line2array($data);

        foreach($lines as $line) {
		if (empty($line)) continue;
		$head  = $line{0};
		if ($head  !== '|' && $head  !== ',') continue;
                //if (substr($line,0,2) == '//') continue;

                // ヘッダーありか？
                if (is_header($line)) {
                        $title = tbl2dat($line);
                        array_pop($title);
                        continue;
                }

                $fields[] = tbl2dat($line);
        }
	return array($title, $fields);
}

function bluff_get_page_data($page, $label)
{
	static $bkup_page = '';
	static $bkup_label = '';
	static $title = array();
	static $fields = array();

	if ($bkup_page === $page && $bkup_label === $label) {
		return array($title, $fields);
	}

	// ２度読み対応
	$bkup_page = $page;
	$bkup_label = $label;
	$title = $fields = array();
	$lines = array();
	$sw = false;

	foreach (get_source($page) as $line) {
		if (empty($line)) continue;
		if (substr($line,0,2) == '//') continue;

		$head  = $line{0};
		$level = strspn($line, $head);
		if ($level > 3) continue;

		if ($head == '*') {
			$line = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/', '$1$2', $line); // アンカーの削除
			$line = substr(trim($line),$level); // * を取り除く
			$sw = ($line == $label) ? true : false;
		}
		if (!$sw) continue;

		if ($head == '|') {
			$lines[] = trim($line);
		}
        }

	foreach($lines as $line) {
		// ヘッダーありか？
		if (is_header($line)) {
			$title = tbl2dat($line);
			array_pop($title);
			continue;
		}
		$fields[] = tbl2dat($line);
	}
	return array($title, $fields);
}

function bluff_get_attach_data($page, & $parm)
{
	global $_bluff_msg;

	$filename = UPLOAD_DIR . encode($page) . '_' . encode($parm['file']);
	if (! file_exists($filename)) die_message( $_bluff_msg['msg_no_attach_file']);
	exist_plugin('attach');

/*
	$file_path = pathinfo($parm['file']);
	switch(strtolower($file_path['extension'])) {
	case 'xls': // Excel
	case 'csv':
	case 'gz':
	default:
		if (PLUGIN_ATTACH_COMPRESS_TYPE != 'GZ')
			die_message( $_bluff_msg['msg_no_attach_file']);
	}
*/

	// read Excel
	require_once('peruser.php');
// defined('PLUGIN_ATTACH_COMPRESS_TYPE')  or define('PLUGIN_ATTACH_COMPRESS_TYPE', 'GZ'); // TGZ or GZ

	$obj = new Excel_Peruser;
	$obj->setInternalCharset('UTF-8');
	$obj->setErrorHandling(1);
	$obj->fileread($filename);

	$sn = isset($parm['seet_no']) ? min($parm['seet_no'],$obj->sheetnum) : 0;
	$flag = 'h';
	$tmp_table = '';
	// echo 'seetno: '.$sn.' name: '.$obj->sheetname[$sn]."\n";
	for($row=0;$row<=$obj->maxrow[$sn];$row++) {
		for($col=0;$col<=$obj->maxcell[$sn];$col++) {
			$tmp_table .= '|'.$obj->dispcell($sn,$row,$col);
		}
		$tmp_table .= '|'.$flag."\n";
		$flag = '';
	}

	return bluff_get_inline_data($tmp_table);
}

function bluff_make_data(& $title, & $fields, & $parm)
{
	$retval = '';

	// g.data
	switch ($parm['type']) {
		//case 'AccumulatorBar':
		//case 'Area':
		//case 'Bar':
		//case 'Line':
	//case 'Mini.Bar':
	case 'Mini.Pie':
	//case 'Mini.SideBar':
	//case 'Net':
	case 'Pie':
	//case 'SideBar':
	case 'SideStackedBar':
	case 'Spider':
		//case 'StackedArea':
		//case 'StackedBar':
		foreach($fields as $field) {
			$retval .= 'g.data(\''.$field[0].'\',['.$field[1].']);';
		}
		break;
	default:
		if (isset($parm['data_no'])) {
			$start = $end = $parm['data_no'] - 1;
			$end++;
		} else {
			$start = 1;
			$end = count($title);
		}

		for($i=$start; $i<$end; $i++) {
			$retval .= 'g.data(\''.$title[$i].'\',[';
			$delm = '';
			foreach($fields as $field) {
				$retval .= $delm . $field[$i];
				$delm = ',';
			}
			$retval .= ']);';
		}
	}

	// g.labels
	$retval .= 'g.labels = {';
	$delm = '';
	$i = -1;
	foreach($fields as $field) {
		$i++;
		if (($i%$parm['step']) > 0) continue;
		$retval .= $delm.$i.':\''.$field[0].'\'';
		$delm = ',';
	}
	$retval .= '};';
        return $retval;
}

?>
