<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: menu.inc.php,v 1.10.9 2011/02/05 11:06:00 Logue Exp $
//

use PukiWiki\Factory;
use PukiWiki\Renderer\RendererFactory;

// サブメニューを使用する
define('MENU_ENABLE_SUBMENU', TRUE);

// サブメニューの名称
define('MENU_SUBMENUBAR', 'MenuBar');

function plugin_menu_convert()
{
	global $vars, $menubar, $menuhtml;
	static $menu = NULL;

//miko patched
	// Cached MenuHTML
	if ($menuhtml !== NULL)
		return preg_replace('/<ul class="list[^>]*>/','<ul class="menu">', $menuhtml);
//miko patched

	$num = func_num_args();
	if ($num > 0) {
		// Try to change default 'MenuBar' page name (only)
		if ($num > 1)       return '#menu(): Zero or One argument needed';
		if ($menu !== NULL) return '#menu(): Already set: ' . htmlsc($menu);
		$args = func_get_args();
		if (! is_page($args[0])) {
			return '#menu(): No such page: ' . htmlsc($args[0]);
		} else {
			$menu = $args[0]; // Set
			return '';
		}

	} else {
		// Output menubar page data
		$page = ($menu === NULL) ? $menubar : $menu;

		if (MENU_ENABLE_SUBMENU) {
			$path = explode('/', strip_bracket($vars['page']));
			while(! empty($path)) {
				$_page = join('/', $path) . '/' . MENU_SUBMENUBAR;
				if (is_page($_page)) {
					$page = $_page;
					break;
				}
				array_pop($path);
			}
		}
		$wiki = Factory::Wiki($page);

		if (! $wiki->has()) {
			return '';
		} else if ($vars['page'] === $page) {
			return '<!-- #menu(): You already view ' . htmlsc($page) . ' -->';
		} else {
			// Cut fixed anchors
			$menutext = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $wiki->get(true));
//miko patched
			if (function_exists('convert_filter')) {
				$menutext = convert_filter($menutext);
			}
			global $top;
			$tmptop = $top;
			$top = '';
			$menuhtml = RendererFactory::factory($menutext);
			$top = $tmptop;
			$menuhtml = str_replace("\n",'',$menuhtml);
			return preg_replace('/<ul class="list[^>]*>/','<ul class="menu">',$menuhtml);
//miko patched
		}
	}
}
/* End of file menu.inc.php */
/* Location: ./wiki-common/plugin/menu.inc.php */
