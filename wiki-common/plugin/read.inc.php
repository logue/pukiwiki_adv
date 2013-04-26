<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: read.inc.php,v 1.9.10 2012/03/31 00:15:00 Logue Exp $
//
// Read plugin: Show a page and InterWiki

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Renderer\InlineFactory;
use PukiWiki\Renderer\Inline\AutoAlias;
use PukiWiki\Renderer\Inline\InterWikiName;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Router;
use PukiWiki\Utility;

function plugin_read_init(){
	$msg = array(
		'_read_msg' => array(
			'title_invalied'    => T_('Invalied page name'),
			'msg_invalidiwn'    => T_('This pagename is an alias to %s.'),
			'msg_ibvaliediw'    => T_('%s is not a valid InterWikiName.')
		)
	);
	set_plugin_messages($msg);
}

function plugin_read_action()
{
	global $vars, $_read_msg;

	$page = isset($vars['page']) ? Utility::stripBracket($vars['page']) : null;
	$ret = array('msg'=>null, 'body'=>null);

	if (!$page) return $ret;

	// 読み込むことができるページか
	if (Factory::Wiki($page)->isReadable(true)) {
		return $ret;
	}

	global $referer;
	$referer = 0;

	// InterWikiNameに含まれるページか？
	// ?adv:FrontPageみたいな感じでアクセス
	if (preg_match('/^'.RendererDefines::INTERWIKINAME_PATTERN.'$/', $page, $match)){
		$url = InterWikiName::getInterWikiUrl($match[2], $match[3]);
		if ($url == false){
			return array('msg'=>$_read_msg['title_invalied'], 'body'=>sprintf($_read_msg['msg_ibvaliediw'], $match[2]));
		}
		Utility::redirect($url);
		return;
	}

	// AutoAliasに含まれるページか？
	$realpage = AutoAlias::getAutoAlias($page);
	if (count($realpage) === 1) {
		// AutoAliasの指定先のページを指定
		$a_wiki = Factory::Wiki($realpage);
		if ($a_wiki->isValied()) {
			Utility::redirect($a_wiki->link());
			return;
		} else if (Utility::isUri($realpage)) {
			Utility::redirect($realpage);
			return;
		}
	} else if (count($realpage) >= 2) {
		$body = '<p>';
		$body .= $_read_msg['msg_invalidwn'] . '<br />';
		foreach ($realpage as $entry) {
			$link[] = '[[' . $entry . '>' . $entry . ']]&br;';
		}
		$body .= InlineFactory::Wiki(join("\n", $link));
		$body .= '</p>';
		return array('msg'=>$_read_msg['title_invalied'], 'body'=>$body);
	}
	
	Utility::notfound();
	exit;
}

/* End of file read.inc.php */
/* Location: ./wiki-common/plugin/read.inc.php */
