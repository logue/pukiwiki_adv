<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: read.inc.php,v 1.9.10 2012/03/31 00:15:00 Logue Exp $
//
// Read plugin: Show a page and InterWiki

function plugin_read_init(){
	$msg = array(
		'_read_msg' => array(
			'title_invalidwn'	=> T_('Redirect'),
			'msg_invalidiwn'	=> T_('This pagename is an alias to %s.'),
			'title_notfound'	=> T_('Page not found'),
			'msg_notfound1'		=> T_('Sorry, but the page you were trying to view does not exist or deleted.'),
			'msg_notfound2'		=> T_('Please check <a href="%1s" rel="nofollow">backups</a> or <a href="%2s" rel="nofollow">create page</a>.')
		)
	);
	set_plugin_messages($msg);
}

function plugin_read_action()
{
	global $vars, $_read_msg;

	$page = isset($vars['page']) ? $vars['page'] : '';

	if (is_page($page)) {
		// ページを表示
		check_readable($page, true, true);
		header_lastmod($page);
		return array('msg'=>'', 'body'=>'');

	// } else if (! PKWK_SAFE_MODE && is_interwiki($page)) {
	} else if (! auth::check_role('safemode') && is_interwiki($page)) {
		return do_plugin_action('interwiki'); // InterWikiNameを処理

	} else if (is_pagename($page)) {
		$realpages = get_autoaliases($page);
		if (count($realpages) == 1) {
			$realpage = $realpages[0];
			if (is_page($realpage)) {
				header('HTTP/1.0 301 Moved Permanently');
				header('Location: ' . get_page_location_uri($realpage));
				return;
			} elseif (is_url($realpage)) {
				header('HTTP/1.0 301 Moved Permanently');
				header('Location: ' . $realpage);
				return;
			} elseif (is_interwiki($realpage)) {
				header('HTTP/1.0 301 Moved Permanently');
				$vars['page'] = $realpage;
				return do_plugin_action('interwiki'); // header('Location');
			} else { 
				return plugin_read_notfound($page);
			}
		} elseif (count($realpages) >= 2) {
			$body = '<p>';
			$body .= $_read_msg['msg_invalidwn'] . '<br />';
			$link = '';
			foreach ($realpages as $realpage) {
				$link .= '[[' . $realpage . '>' . $realpage . ']]&br;';
			}
			$body .= make_link($link);
			$body .= '</p>';
			return array('msg'=>$_read_msg['title_invalidwn'], 'body'=>$body);
		}
		$vars['cmd'] = 'edit';
		return plugin_read_notfound($page); // 存在しないので、編集フォームを表示
	} else {
		// 無効なページ名
		return array(
			'msg'=>$_title_invalidwn,
			'body'=>str_replace('$1', htmlsc($page),
				str_replace('$2', 'WikiName', $_msg_invalidiwn))
		);
	}
	exit;
}

function plugin_read_notfound($page){
	global $_read_msg;
	$script = get_script_uri();
	header('HTTP/1.0 404 Not Found');
	
	$msg_edit = sprintf($_read_msg['msg_notfound2'], get_cmd_uri('backup',$page), get_cmd_uri('edit',$page));
	$body = <<<HTML
<div class="message_box ui-state-error ui-corner-all">
<p><span class="ui-icon ui-icon-alert" style="float: left;"></span>{$_read_msg['msg_notfound1']}</p>
<p>$msg_edit</p>
</div>
<script type="text/javascript">/* <![CDATA */
var GOOG_FIXURL_LANG = (navigator.language || '').slice(0,2),GOOG_FIXURL_SITE = location.host;
/* ]]> */</script>
<script type="text/javascript" src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>
HTML;
	return array(
		'msg' => $_read_msg['title_notfound'],
		'body'=> $body
	);
}
/* End of file read.inc.php */
/* Location: ./wiki-common/plugin/read.inc.php */
