<?
/**
 * Sbm Plugin.
 *
 * @copyright   Copyright &copy; 2010, Katsumi Saito <jo1upk@users.sourceforge.net>
 * @version     $Id: sbm.inc.php,v 0.2 2010/01/09 14:06:00 upk Exp $
 *
 */

defined('PLUGIN_SBM_DEFAULT') or define('PLUGIN_SBM_DEFAULT', 'delicious,yahoo,twitter,google,google_translate');

function plugin_sbm_init()
{
  $msg = array(
	'_sbm_msg' => array(
		'msg_alt'		=> _('This entry is added to %s.'), 				// このエントリーを%s に追加
		'msg_twitter'		=> _('It mutters.'),						// つぶやく
		'msg_google_translate'	=> _('This page translates by way of Google Translate.'),	// このページは、Google Translateを通して翻訳されます。
		'msg_hatena_entry'	=> _('Hatena bookmark including this page.'), 			// このページを含むはてなブックマーク
	)
  );
  set_plugin_messages($msg);
}

function plugin_sbm_inline()
{
  global $vars;
  static $sbm;

  $sbm = array(
	'digg'		=> array('name'=>'Digg',
				 'img'=>'http://digg.com/favicon.ico',
				 'url'=>'http://digg.com/submit?phase=3&amp;url=$link&amp;title=$title', // $link
			),
	'delicious'	=> array('name'=>'Delicious',
				 'img'=>'http://delicious.com/favicon.ico',
				 'url'=>'http://del.icio.us/post?url=$rtlink&amp;title=$title',
			),
	'dzone'		=> array('name'=>'dzone.com',
				 'img'=>'http://www.dzone.com/favicon.ico',
				 'url'=>'http://www.dzone.com/links/add.html?url=$rtlink&amp;title=$title',
			),
	'blogmarks'	=> array('name'=>'Blogmarks.net',
				 'img'=>'http://blogmarks.net/favicon.ico',
				 'url'=>'http://blogmarks.net/my/new.php?mini=1&amp;simple=1&amp;url=$rlink&amp;title=$title',
			),
	'yahoo'		=> array('name'=>'Yahoo!', // http://bookmarks.yahoo.com/
				 'img'=>'http://us.js2.yimg.com/us.yimg.com/lib/bm/favicon.ico',
				 'url'=>'http://myweb2.search.yahoo.com/myresults/bookmarklet?u=$link&amp;t=$title',
			),
	'newsvine'	=> array('name'=>'newsvine.com',
				 'img'=>'http://www.newsvine.com/favicon.ico',
				 'url'=>'http://www.newsvine.com/_tools/seed&amp;save?u=$rtlink&amp;h=$title',
			),
	'reddit'	=> array('name'=>'reddit.com',
				 'img'=>'http://reddit.com/favicon.ico',
				 'url'=>'http://reddit.com/submit?url=$rtlink&amp;title=$title',
			),
	'baidu'		=> array('name'=>'Baidu',
				 'img'=>'http://cang.baidu.com/favicon.ico',
				 'url'=>'http://cang.baidu.com/do/add?it=$title&amp;iu=$rlink',
			),
	'twitter'	=> array('name'=>'Twitter',
				 'msg'=>'msg_twitter',
				 'img'=>'http://twitter.com/favicon.ico',
				 'url'=>'http://twitter.com/?status=$link',
			),
	'google'	=> array('name'=>'Google',
				 'img'=>'http://www.google.com/favicon.ico',
				 'url'=>'http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=$link&amp;title=$title',
			),
	'google_translate'	=> array('name'=>'Google Translate',
				 'msg'=>'msg_google_translate',
				 'img'=>'http://www.google.com/favicon.ico',
				 'url'=>'http://translate.google.com/translate?u=$rlink&amp;sl=auto', // &tl=ja
			),
	'facebook'	=> array('name'=>'Facebook',
				 'img'=>'http://static.ak.fbcdn.net/rsrc.php/z9Q0Q/hash/8yhim1ep.ico',
				 'url'=>'http://www.facebook.com/share.php?u=$rlink',
			),
	'linkedin'	=> array('name'=>'LinkedIn',
				 'img'=>'http://www.linkedin.com/favicon.ico',
				 'url'=>'http://www.linkedin.com/shareArticle?mini=true&amp;url=$rlink&amp;title=$title&amp;ro=false&amp;summary=&amp;source=',
			),
	// jp
	'hatena'	=> array('name'=>'Hatena',
				 'img'=>IMAGE_URI.'sbm/append.gif',
				 'width'=>'16', 'height'=>'12',
				 'url'=>'http://b.hatena.ne.jp/add?mode=confirm&amp;url=$link', // $link
			),	// 16x12
	'hatena_entry'	=> array('name'=>'Hatena',
				 'msg'=>'msg_hatena_entry',
				 'img'=>IMAGE_URI.'sbm/b_entry.gif',
				 'width'=>'16', 'height'=>'12',
				 'url'=>'http://b.hatena.ne.jp/entry/$link',	// $link
			),	// 16x12
	'yahoo_jp'	=> array('name'=>'Yahoo! JAPAN',
				 'img'=>'http://i.yimg.jp/images/sicons/ybm16.gif',
				 'url'=>'http://bookmarks.yahoo.co.jp/action/bookmark?t=$title&amp;u=$rlink',
			),
	'yahoo_jp_entry'=> array('name'=>'Yahoo! JAPAN',
				 'img'=>'http://num.bookmarks.yahoo.co.jp/ybmimage.php?disptype=shortsmall&amp;url=$rlink',
				 'url'=>'http://bookmarks.yahoo.co.jp/url?url=$rlink',
			),
	'nifty'		=> array('name'=>'@nifty',
				 'img'=>'http://clip.nifty.com/images/addclip_icn.gif',
				 'url'=>'http://clip.nifty.com/create?url=$link&amp;title=$title',
			),
	'livedoor'	=> array('name'=>'livedoor',
				 'img'=>'http://parts.blog.livedoor.jp/img/cmn/clip_16_16_w.gif',
				 'url'=>'http://clip.livedoor.com/clip/add?link=$link&amp;title=$title',
			),
	'livedoor_entry'=> array('name'=>'livedoor',
				 'img'=>'http://image.clip.livedoor.com/counter/$link',
				 'url'=>'http://clip.livedoor.com/page/$link',
			),
  );

  $argv = func_get_args();
  $argc = func_num_args();

  if ($argc===1) $argv=explode(',',PLUGIN_SBM_DEFAULT);

  $rc = '';
  foreach($argv as $cmd) {
	if (!isset($sbm[$cmd])) continue;
	$rc .= sbm_make($cmd, $sbm[$cmd]);
  }
  return $rc;
}

function sbm_make($cmd, & $info)
{
	global $vars, $_sbm_msg;

	$title   = rawurlencode($vars['page']);
	$link    = get_page_absuri($vars['page']);
	$rlink   = rawurlencode($link);
	$rtlink  = rawurlencode( get_script_absuri().'?').$title;

	// url
	switch($cmd) {
	case 'twitter':
		if (exist_plugin_inline('bitly')) {
			$val = bitly_convert('shorten',$link);
			$rlink = (!$val['rc']) ? 'N/A' : $val['msg'];
			$url = str_replace('$link',  $rlink,  $info['url']);
		} else {
			$url = $info['url'];
		}
		break;
	default:
		$url = $info['url'];
	}

	$width   = (empty($info['width']))  ? '16' : $info['width'];
	$height  = (empty($info['height'])) ? '16' : $info['height'];

	$msg_alt = (isset($info['msg'])) ? $info['msg'] : 'msg_alt';
	$alt = sprintf($_sbm_msg[$msg_alt],$info['name']);

	// img
	switch($cmd) {
	case 'yahoo_jp_entry':
		$img = str_replace('$rlink', $link, $info['img']);
		break;
	case 'livedoor_entry':
		$img = str_replace('$link', $link, $info['img']);
		$alt = '';
		break;
	default:
		$img = $info['img'];
	}

	// Conversion processing
	$url = str_replace('$title',  $title,  $url);
	$url = str_replace('$link',   $link,   $url);
	$url = str_replace('$rlink',  $rlink,  $url);
	$url = str_replace('$rtlink', $rtlink, $url);

	$retval = <<<EOD
<a href="$url">
<img src="$img" width="$width" height="$height" style="border: none;" alt="$alt" title="$alt" />
</a>
EOD;

	switch($cmd) {
	case 'hatena_entry':
		$retval .= '<img src="http://b.hatena.ne.jp/entry/image/'.$link.'" alt="counter"/>';
	}

	return $retval;
}

?>
