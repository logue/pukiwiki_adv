<?php
/**
 * Google Buzz Post プラグイン
 *
 * @copyright   Copyright &copy; 2010, Katsumi Saito <jo1upk@users.sourceforge.net>
 * @version     $Id: google_buzz_post.inc.php,v 0.1 2010/05/17 01:55:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 *
 */

function plugin_google_buzz_post_init()
{
	$msg = array(
		'_google_buzz_post_msg' => array(
			'msg_title'		=> _('Post on Google Buzz'),
		)
	);
	set_plugin_messages($msg);
}

function plugin_google_buzz_post_convert()
{
	global $head_tags, $_google_buzz_post_msg, $language;
	static $set_tag = true;

	$argv = func_get_args();
	$parm = google_buzz_post_set_parm($argv);

	if ($set_tag) {
		$head_tags[] = ' <script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>';
		$set_tag = false;
	}

	$data_locale = $language;
	$locale = ' data-locale="'.$data_locale.'"';

	return <<<EOD
<a title="{$_google_buzz_post_msg['msg_title']}" class="google-buzz-button" href="http://www.google.com/buzz/post" data-button-style="{$parm['style']}"{$locale}></a>

EOD;
}

function plugin_google_buzz_post_inline()
{
	$args = func_get_args();
	array_pop($args);
	return call_user_func_array('plugin_google_buzz_post_convert', $args);
}

function google_buzz_post_set_parm($argv)
{
	$parm = array();
	$parm['style'] = 'normal-count';

	foreach($argv as $arg) {
		$val = split('=', $arg);
		$val[1] = (empty($val[1])) ? htmlspecialchars($val[0]) : htmlspecialchars($val[1]);

		switch($val[0]) {
		case 'link':
			$parm['style'] = 'link';
			break;
		case 'ctrs':
			$parm['style'] = 'small-count';
			break;
		case 'ctr':
		case 'ctrn':
			$parm['style'] = 'normal-count';
			break;
		case 'btns':
			$parm['style'] = 'small-button';
			break;
		case 'btnn':
		case 'btn':
			$parm['style'] = 'normal-button';
			break;
		default:
			// if (is_numeric($val[1])) $parm['id'] = $val[1];
		}
	}
	return $parm;
}

?>
