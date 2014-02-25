<?php
/**
 * フィードプラグイン
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2014 PukiWiki Advance Developers Team
 * @create    2014/02/23
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: feed.inc.php,v 1.0.0 2014/02/23 16:54:00 Logue Exp $
 */

use PukiWiki\Listing;
use PukiWiki\Factory;
use PukiWiki\Recent;
use PukiWiki\Router;
use PukiWiki\Time;
use PukiWiki\Renderer\Header;

// 出力するエントリの数
define('PLUGIN_FEED_ENTRIES', 25);

// 説明文の長さ
define('PLUGIN_FEED_ENTRY_DESCRIPTION_LENGTH', 256);

function plugin_feed_action(){
	global $vars, $site_name, $site_logo, $modifier, $modifierlink, $_feed_msg, $cache;
	
	$type = isset($vars['type']) ? $vars['type'] : 'rss';
	$page = isset($vars['page']) ? $vars['page'] : null;
	
	Recent::getFeed($page, $type, false);
	exit;
}