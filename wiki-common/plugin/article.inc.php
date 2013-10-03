<?php
// $Id: article.inc.php,v 1.28.8 2012/05/11 18:06:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2006,2008 PukiWiki Plus! Team
//   2002-2005 PukiWiki Developers Team
//   2002      Originally written by OKAWARA,Satoshi <kawara@dml.co.jp>
//             http://www.dml.co.jp/~kawara/pukiwiki/pukiwiki.php
//
// article: BBS-like plugin

 /*
 メッセージを変更したい場合はLANGUAGEファイルに下記の値を追加してからご使用ください
	$_btn_name = 'お名前';
	$_btn_article = '記事の投稿';
	$_btn_subject = '題名: ';

 ※$_btn_nameはcommentプラグインで既に設定されている場合があります

 投稿内容の自動メール転送機能をご使用になりたい場合は
 -投稿内容のメール自動配信
 -投稿内容のメール自動配信先
 を設定の上、ご使用ください。

 */
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;

defined('PLUGIN_ARTICLE_COLS')           or define('PLUGIN_ARTICLE_COLS',	70); // テキストエリアのカラム数
defined('PLUGIN_ARTICLE_ROWS')           or define('PLUGIN_ARTICLE_ROWS',	 5); // テキストエリアの行数
defined('PLUGIN_ARTICLE_NAME_COLS')      or define('PLUGIN_ARTICLE_NAME_COLS',	24); // 名前テキストエリアのカラム数
defined('PLUGIN_ARTICLE_SUBJECT_COLS')   or define('PLUGIN_ARTICLE_SUBJECT_COLS',	60); // 題名テキストエリアのカラム数
defined('PLUGIN_ARTICLE_NAME_FORMAT')    or define('PLUGIN_ARTICLE_NAME_FORMAT',	'[[$name]]'); // 名前の挿入フォーマット
defined('PLUGIN_ARTICLE_SUBJECT_FORMAT') or define('PLUGIN_ARTICLE_SUBJECT_FORMAT',	'**$subject'); // 題名の挿入フォーマット

defined('PLUGIN_ARTICLE_INS')            or define('PLUGIN_ARTICLE_INS',	0); // 挿入する位置 1:欄の前 0:欄の後
defined('PLUGIN_ARTICLE_COMMENT')        or define('PLUGIN_ARTICLE_COMMENT',	1); // 書き込みの下に一行コメントを入れる 1:入れる 0:入れない
defined('PLUGIN_ARTICLE_AUTO_BR')        or define('PLUGIN_ARTICLE_AUTO_BR',	1); // 改行を自動的変換 1:する 0:しない

defined('PLUGIN_ARTICLE_MAIL_AUTO_SEND') or define('PLUGIN_ARTICLE_MAIL_AUTO_SEND',	0); // 投稿内容のメール自動配信 1:する 0:しない
defined('PLUGIN_ARTICLE_MAIL_FROM')      or define('PLUGIN_ARTICLE_MAIL_FROM',	''); // 投稿内容のメール送信時の送信者メールアドレス
defined('PLUGIN_ARTICLE_MAIL_SUBJECT_PREFIX') or define('PLUGIN_ARTICLE_MAIL_SUBJECT_PREFIX', "[someone's PukiWiki]"); // 投稿内容のメール送信時の題名

// 投稿内容のメール自動配信先
global $_plugin_article_mailto;
$_plugin_article_mailto = array (
	''
);

function plugin_article_init()
{
	global $_string;
	$msg = array(
		'_article_msg' => array(
			'title_updated'				=> $_string['updated'],
			'title_collided'			=> $_string['title_collided'],
			'msg_collided'				=> $_string['msg_collided'],
			'msg_article_mail_sender'	=> T_('Author: '),
			'msg_article_mail_page'		=> T_('Page: '),
			'form_name'					=> T_('Name: '),
			'form_subject'				=> T_('Subject: '),
			'form_msg'					=> T_('Message: '),
			'btn_submit'				=> T_('Submit')
			
		)
	);
	set_plugin_messages($msg);
}

function plugin_article_action()
{
	global $script, $post, $vars, $cols, $rows, $now;
	global $_plugin_article_mailto, $_no_subject, $_no_name;
	global $_article_msg, $_string;

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (Auth::check_role('readonly')) die_message($_string['error_prohibit']);

	if (!isset($vars['msg']) || !isset($vars['refer']) )
		return array('msg'=>null,'body'=>null);

	$name = !isset($vars['name']) ? $_no_name : $post['name'];
	$name = empty($name) ? '' : str_replace('$name', $name, PLUGIN_ARTICLE_NAME_FORMAT);
	$subject = !isset($vars['subject']) ? $_no_subject : $post['subject'];
	$subject = empty($subject) ? '' : str_replace('$subject', $subject, PLUGIN_ARTICLE_SUBJECT_FORMAT);
	$article  = $subject . "\n" . '>' . $name . ' (' . $now . ')~' . "\n" . '~' . "\n";

	$msg = rtrim($post['msg']);
	if (PLUGIN_ARTICLE_AUTO_BR) {
		//改行の取り扱いはけっこう厄介。特にURLが絡んだときは…
		//コメント行、整形済み行には~をつけないように arino
		$msg = join("\n", preg_replace('/^(?!\/\/)(?!\s)(.*)$/', '$1~', explode("\n", $msg)));
	}
	$article .= $msg . "\n\n" . '//';

	if (PLUGIN_ARTICLE_COMMENT) $article .= "\n\n" . '#comment' . "\n";

	$postdata = array();
	$wiki = Factory::Wiki($vars['refer']);
	$article_no = 0;

	foreach($wiki->get() as $line) {
		if (! PLUGIN_ARTICLE_INS) $postdata[] = $line;
		if (preg_match('/^#article/i', $line)) {
			if ($article_no == $post['article_no'] && $post['msg'] != '')
				$postdata[] = $article;
			++$article_no;
		}
		if (PLUGIN_ARTICLE_INS) $postdata[] = $line;
	}

	$postdata_input = $article . "\n";
	$body = '';

	$wiki->set($postdata);

	// 投稿内容のメール自動送信
	if (PLUGIN_ARTICLE_MAIL_AUTO_SEND) {
		$mailaddress = implode(',', $_plugin_article_mailto);
		$mailsubject = PLUGIN_ARTICLE_MAIL_SUBJECT_PREFIX . ' ' . str_replace('**', '', $subject);
		if ($post['name'])
			$mailsubject .= '/' . $post['name'];
		$mailsubject = mb_encode_mimeheader($mailsubject);

		$mailbody = array();
		$mailbody[] = $post['msg'];
		$mailbody[] = "\n" . '---';
		$mailbody[] = $_article_msg['msg_article_mail_sender'] . $post['name'] . ' (' . $now . ')';
		$mailbody[] = $_article_msg['msg_article_mail_page'] . $post['refer'];
		$mailbody[] = 'URL: ' . get_page_absuri($post['refer']);
		$output = mb_convert_encoding(join("\n",$mailbody), 'JIS');

		$mailaddheader = 'From: ' . PLUGIN_ARTICLE_MAIL_FROM;

		mail($mailaddress, $mailsubject, $mailbody, $mailaddheader);
	}

	$retvars['msg'] = $_article_msg['title_updated'];
	$retvars['body'] = $body;

	$post['page'] = $post['refer'];
	$vars['page'] = $post['refer'];

	return $retvars;
}

function plugin_article_convert()
{
	global $vars, $digest;
//	global $_btn_article, $_btn_name, $_btn_subject;
	global $_article_msg;
	static $numbers = array();

	// if (PKWK_READONLY) return ''; // Show nothing
	if (Auth::check_role('readonly')) return ''; // Show nothing

	if (! isset($numbers[$vars['page']])) $numbers[$vars['page']] = 0;

	$article_no = $numbers[$vars['page']]++;

	$s_page   = Utility::htmlsc($vars['page']);
	$s_digest = Utility::htmlsc($digest);
	$name_cols = PLUGIN_ARTICLE_NAME_COLS;
	$subject_cols = PLUGIN_ARTICLE_SUBJECT_COLS;
	$article_rows = PLUGIN_ARTICLE_ROWS;
	$article_cols = PLUGIN_ARTICLE_COLS;
	$script = get_script_uri();
	$string = <<<EOD
<form action="$script" method="post" class="form-horizontal row plugin-article-form">
	<input type="hidden" name="article_no" value="$article_no" />
	<input type="hidden" name="cmd" value="article" />
	<input type="hidden" name="digest" value="$s_digest" />
	<input type="hidden" name="refer" value="$s_page" />
	<div class="form-group">
		<label for="_p_article_name_$article_no" class="col-md-2 control-label">{$_article_msg['form_name']}</label>
		<div class="col-md-10">
			<input type="text" name="name" class="form-control" id="_p_article_name_$article_no" size="$name_cols" placeholder="{$_article_msg['form_name']}" />
		</div>
	</div>
	<div class="form-group">
		<label for="_p_article_subject_$article_no" class="col-md-2 control-label">{$_article_msg['form_subject']}</label>
		<div class="col-md-10">
			<input type="text" name="subject" class="form-control" id="_p_article_subject_$article_no" size="$subject_cols" placeholder="{$_article_msg['form_subject']}" />
		</div>
	</div>
	<div class="form-group">
		<label for="_p_article_msg_$article_no" class="col-md-2 control-label">{$_article_msg['form_subject']}</label>
		<div class="col-md-10">
			<textarea name="msg" id="_p_article_msg_$article_no" class="form-control" rows="$article_rows" cols="$article_cols" placeholder="{$_article_msg['form_subject']}" ></textarea>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-offset-2 col-md-10">
			<input type="submit" name="article" class="btn btn-primary" value="{$_article_msg['btn_submit']}" />
		</div>
	</div>
</form>
EOD;
	if (IS_MOBILE) {
		return '<div data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">' . "\n"
			. '<h4>'. $_article_msg['btn_article'] . '</h4>'. "\n"
			. $string . "\n" .'</div>';
	}else{
		return $string;
	}
}
/* End of file amazon.inc.php */
/* Location: ./wiki-common/plugin/article.inc.php */
