<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: read.inc.php,v 1.9.10 2012/03/31 00:15:00 Logue Exp $
//
// Read plugin: Show a page and InterWiki

use PukiWiki\Router;
use PukiWiki\Renderer\InlineFactory;
use PukiWiki\Renderer\Inline\AutoAlias;
use PukiWiki\Renderer\Inline\InterWikiName;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;

function plugin_read_init(){
	$msg = array(
		'_read_msg' => array(
			'title_invalied'    => T_('Invalied page name'),
			'msg_invalidiwn'    => T_('This pagename is an alias to %s.'),
			'msg_ibvaliediw'    => T_('This is not a valid InterWikiName')
		)
	);
	set_plugin_messages($msg);
}

function plugin_read_action()
{
	global $vars, $_read_msg;

	$page = isset($vars['page']) ? $vars['page'] : null;
	$ret = array('msg'=>null, 'body'=>null);

	if (!$page) return $ret;

	// 読み込むことができるページか
	if (Factory::Wiki($page)->isReadable(true)) {
		return $ret;
	}

	global $referer;
	$referer = 0;

	// InterWikiNameに含まれるページか？
	// ?[http://pukiwiki.logue.be/? adv]みたいな感じでアクセス
	if (Utility::isInterWiki($page) && preg_match('/^'.InterWikiName::INTERWIKINAME_PATTERN.'$/', $page, $match)){
		$url = InterWikiName::getInterWikiUrl($match[2], $match[3]);
		if ($url == false){
			return array('msg'=>$_read_msg['title_invalied'], 'body'=>$_read_msg['msg_invalidiw']);
		}
		Router::redirect($url);
		return;
	}

	// AutoAliasに含まれるページか？
	$realpages = AutoAlias::getAutoAlias($page);
	if (count($realpages) === 1) {
		$realpage = $realpages[0];
		// AutoAliasの指定先のページを指定
		$a_wiki = Factory::Wiki($realpage);
		if ($a_wiki->isValied()) {
			Router::redirect($a_wiki->link());
			return;
		} else if (Utility::isUri($realpage)) {
			Router::redirect($realpage);
			return;
		}
	} else if (count($realpages) >= 2) {
		$body = '<p>';
		$body .= $_read_msg['msg_invalidwn'] . '<br />';
		$link = '';
		foreach ($realpages as $realpage) {
			$link .= '[[' . $realpage . '>' . $realpage . ']]&br;';
		}
		$body .= InlineFactory::Wiki($link);
		$body .= '</p>';
		return array('msg'=>$_read_msg['title_invalied'], 'body'=>$body);
	}
	
	Utility::notfound();
	exit;
}

/* End of file read.inc.php */
/* Location: ./wiki-common/plugin/read.inc.php */
