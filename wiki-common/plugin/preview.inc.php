<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: preview.inc.php,v 1.8 2005/05/26 13:57:07 miko Exp $
//
// Read plugin: Show a page and InterWiki

use PukiWiki\Factory;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Renderer\Header;
use Zend\Http\Response;

function plugin_preview_action()
{
	global $vars;

	$page = isset($vars['page']) ? $vars['page'] : '';
	
	$modified = 0;

	$response = new Response();

	if (!empty($page) ){
		$wiki = Factory::Wiki($page);
		if ($wiki->isReadable() ) {
			$source = $wiki->get();
			array_splice($source, 10);
			$response->setStatusCode(Response::STATUS_CODE_200);
			$response->setContent('<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n" . RendererFactory::factory($source));
			$headers = Header::getHeaders('text/xml', $wiki->time());
		}else{
			$response->setStatusCode(Response::STATUS_CODE_404);
			$headers = Header::getHeaders('text/xml');
		}
	}else{
		$response->setStatusCode(Response::STATUS_CODE_404);
		$headers = Header::getHeaders('text/xml');
	}

	$response->getHeaders()->addHeaders($headers);
	header($response->renderStatusLine());
	foreach ($response->getHeaders() as $_header) {
		header($_header->toString());
	}
	echo $response->getBody();
	exit;
}
/* End of file preview.inc.php */
/* Location: ./wiki-common/plugin/preview.inc.php */