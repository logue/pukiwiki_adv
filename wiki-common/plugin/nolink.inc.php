<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: nolink.inc.php,v 1.0.1 2009/02/16 23:33:00 upk Exp $
//

use PukiWiki\Renderer\RendererFactory;

function plugin_nolink_convert()
{
	$argv = func_get_args();
	$argc = func_num_args();

	if ($argc < 1) return '';
	$data = $argv[ --$argc ];
	return strip_a(RendererFactory::factory(line2array($data)));
}

function strip_a($x)
{
	$x = preg_replace('#<a href="(.*?)"[^>]*>(.*?)</a>#si', '$2', $x);
	$x = preg_replace('#<a href="(.*?)"[^>]*>(.*?)<span class="pkwk-symbol.*?</a>#si','$2',$x);
	return $x;
}
/* End of file nolink.inc.php */
/* Location: ./wiki-common/plugin/nolink.inc.php */
