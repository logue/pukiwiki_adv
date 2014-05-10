<?php
/**
 * –ÚŽŸƒvƒ‰ƒOƒCƒ“
 *
 * @package   PukiWiki\Text
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2014 PukiWiki Advance Developers Team
 * @create    2014/03/21
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Rule.php,v 1.2.0 2014/03/21 09:01:00 Logue Exp $
 **/

use PukiWiki\Factory;
use PukiWiki\Text\Rules;
use PukiWiki\Renderer\RendererFactory;

function plugin_contents_convert()
{
	return '<#_contents_>';
	/*
	global $vars;
	$page = isset($vars['page']) ? $vars['page'] : null;
	
	if (empty($page)) return;
	
	$ret = array();
	$wiki = Factory::Wiki($page);
	foreach($wiki->get() as $line){
		list($anchor, $id, $level) = Rules::getHeading($line);
		if (empty($id)) continue;
		$ret[] = str_repeat('-', $level).'[['.$anchor.'>#'.$id.']]';
	}
	return '<div class="contents">' . RendererFactory::factory($ret) . '</div>';
	*/
}
/* End of file contents.inc.php */
/* Location: ./wiki-common/plugin/contents.inc.php */