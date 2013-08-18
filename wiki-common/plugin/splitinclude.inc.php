<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: splitinclude.inc.php,v 1.3 2004/08/06 06:12:17 miko Exp $
//

/*
 splitinclude.inc.php
 ページをインクルードする(分割を有効にする)
*/

use PukiWiki\Factory;

function plugin_splitinclude_convert()
{
	global $vars,$get,$post;
	global $_msg_splitinclude_restrict;
	static $splitinclude_list = array(); //処理済ページ名の配列

	if (func_num_args() == 0)
	{
		return;
	}

	$splitinclude_list[$vars['page']] = TRUE;

	$func_vars_num = func_num_args();
	$func_vars_array = func_get_args();
	$incbody = "";

	foreach($func_vars_array as $page)
	{
		$page = strip_bracket($page);
		
		if (!is_page($page) or isset($splitinclude_list[$page]))
		{
			return '';
		}
		$splitinclude_list[$page] = TRUE;
		
		$_page = $vars['page'];
		$get['page'] = $post['page'] = $vars['page'] = $page;
                
                $wiki = Factory::Wiki($page);
		
		// splitincludeのときは、認証画面をいちいち出さず、後始末もこちらでつける
		if ($wiki->isReadable()) {
                        $body = $wiki->render();
		} else {
			$body = str_replace('$1',$page,$_msg_splitinclude_restrict);
		}
		
		$get['page'] = $post['page'] = $vars['page'] = $_page;
		
		$incbody .= "<div style=\"width:".intval(96/$func_vars_num)."%;margin:0px 2px;vartical-align:top;float:left;\">$body</div>\n";
	}
	$incbody = "<div style=\"width:100%\">\n$incbody</div>\n<div style=\"display:block;\"></div>\n";

	return $incbody;
}
/* End of file splitinclude.inc.php */
/* Location: ./wiki-common/plugin/splitinclude.inc.php */
