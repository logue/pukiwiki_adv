<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: clear.inc.php,v 1.4 2011/03/02 22:54:21 Logue Exp $
//
// Clear plugin - inserts a CSS class 'clear', to set 'clear:both'

function plugin_clear_convert()
{
	//return '<div class="clear"></div>';
	return '<div class="clearfix"></div>';
}
?>
