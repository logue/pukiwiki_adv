<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: memo.inc.php,v 1.17.4 2011/02/05 11:05:00 Logue Exp $
//
// Memo box plugin

define('MEMO_COLS', 60); // Columns of textarea
define('MEMO_ROWS',  5); // Rows of textarea

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;

function plugin_memo_action()
{
	global $vars, $cols, $rows, $_string;
//	global $_title_collided, $_msg_collided, $_title_updated;

$_title_collided   = $_string['title_collided'];
$_title_updated    = $_string['update'];
$_msg_collided =  $_string['msg_collided'];

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (Auth::check_role('readonly')) die_message('PKWK_READONLY prohibits editing');
	if (! isset($vars['msg']) || $vars['msg'] == '') return;

	$memo_body = preg_replace('/' . "\r" . '/', '', $vars['msg']);
	$memo_body = str_replace("\n", '\n', $memo_body);
	$memo_body = str_replace('"', '&#x22;', $memo_body); // Escape double quotes
	$memo_body = str_replace(',', '&#x2c;', $memo_body); // Escape commas

	$wiki = Factory::Wiki($vars['refer']);
	$postdata = array();
	$memo_no = 0;
	foreach($wiki-get() as $line) {
		if (preg_match('/^#memo\(?.*\)?$/i', $line)) {
			if ($memo_no == $vars['memo_no']) {
				$postdata[] = '#memo(' . $memo_body . ')' . "\n";
				$line = '';
			}
			++$memo_no;
		}
		$postdata[] = $line;
	}

	$postdata_input = $memo_body . "\n";

	$body = '';
	if ($wiki->digest() !== $vars['digest']) {
		$title = $_title_collided;
		$body  = $_msg_collided . "\n";

		$s_refer  = Utility::htmlsc($vars['refer']);
		$s_digest = Utility::htmlsc($vars['digest']);
		$s_postdata_input = Utility::htmlsc($postdata_input);

		$script = get_script_uri();
		$body .= <<<EOD
<form action="$script" method="post" class="memo_form">
	<input type="hidden" name="cmd" value="preview" />
	<input type="hidden" name="refer"  value="$s_refer" />
	<input type="hidden" name="digest" value="$s_digest" />
	<textarea name="msg" rows="$rows" cols="$cols" id="textarea">$s_postdata_input</textarea>
</form>
EOD;
	} else {
		$wiki->set($postdata);

		$title = $_title_updated;
	}
	$retvars['msg']  = & $title;
	$retvars['body'] = & $body;

	$vars['page'] = $vars['refer'];

	return $retvars;
}

function plugin_memo_convert()
{
	global $vars, $digest;
	static $numbers = array();

	if (! isset($numbers[$vars['page']])) $numbers[$vars['page']] = 0;
	$memo_no = $numbers[$vars['page']]++;

	$data = func_get_args();
	$data = implode(',', $data);	// Care all arguments
	$data = str_replace('&#x2c;', ',', $data); // Unescape commas
	$data = str_replace('&#x22;', '"', $data); // Unescape double quotes
	$data = htmlsc(str_replace('\n', "\n", $data));

	// if (PKWK_READONLY) {
	if (Auth::check_role('readonly')) {
		$_script = '';
		$_submit = '';
	} else {
		$_script = get_script_uri();;
		$_submit = '<input type="submit" name="memo" value="' . T_('update') . '" />';
	}

	$s_page   = htmlsc($vars['page']);
	$s_digest = htmlsc($digest);
	$s_cols   = MEMO_COLS;
	$s_rows   = MEMO_ROWS;
	$string   = <<<EOD
<form action="$_script" method="post" class="memo_form">
	<input type="hidden" name="memo_no" value="$memo_no" />
	<input type="hidden" name="refer"   value="$s_page" />
	<input type="hidden" name="cmd"  value="memo" />
	<input type="hidden" name="digest"  value="$s_digest" />
	<textarea name="msg" rows="$s_rows" cols="$s_cols">$data</textarea><br />
	$_submit
</form>
EOD;

	return $string;
}
/* End of file memo.inc.php */
/* Location: ./wiki-common/plugin/memo.inc.php */
