<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: xmlrpc.inc.php,v 1.0.0 2014/02/28 11:19:00 Logue Exp $
// Copyright (C)
//	 2012,2014 PukiWiki Advance Developers Team
// License: GPL v2
//
// XML-Rpc Server plugin

use PukiWiki\Renderer\Header;
use Zend\XmlRpc\Server as XmlRpcServer;

defined('XMLRPC_CACHE') or define('XMLRPC_CACHE', CACHE_DIR.'xmlrpc.cache');

function plugin_xmlrpc_action(){
	//$headers = Header::getHeaders('application/xml');
	header('Content-Type: application/xml');
	$server = new XmlRpcServer();
	XmlRpcServer\Fault::attachFaultException('Services\Exception');
	//if (!DEBUG && !XmlRpcServer\Cache::get(XMLRPC_CACHE, $server) ) {
		$server->setClass('PukiWiki\Service\PingBack', 'pingback');
		$server->setClass('PukiWiki\Service\WikiRpc', 'wiki');
	//	XmlRpcServer\Cache::save(XMLRPC_CACHE, $server);
	//}
	//Header::writeResponse($headers, 200, $server->handle());
	echo $server->handle();
	exit;
}
