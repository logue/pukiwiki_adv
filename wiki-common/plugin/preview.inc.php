<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: preview.inc.php,v 1.8 2005/05/26 13:57:07 miko Exp $
//
// Read plugin: Show a page and InterWiki

use PukiWiki\Factory;
use PukiWiki\Renderer\RendererFactory;

function plugin_preview_action()
{
	global $vars;

	$page = isset($vars['page']) ? $vars['page'] : '';
	$wiki = WikiFactory::Wiki($page);

	if ($wiki->isReadable(true) ) {
		$source = $wiki->get();
		array_splice($source, 10);
		$body = RenderFactory::Wiki($source);

		pkwk_common_headers(true,true);
		header('Content-type: text/xml');
		print '<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n";
		print $body;
	}
	exit;
}
/* End of file preview.inc.php */
/* Location: ./wiki-common/plugin/preview.inc.php */