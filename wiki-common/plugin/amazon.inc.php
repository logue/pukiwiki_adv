<?php
/**
 * amazon plugin
 *
 * Copyright: 2003-2004 By 閑舎 <raku@rakunet.org>
 * Thanks: To reimy, t, Ynak, WikiRoom, upk, 水橋希 and PukiWiki Developers Team.
 *
 * @copyright   Copyright &copy; 2009, Katsumi Saito <jo1upk@users.sourceforge.net>
 *              Copyright &copy; 2010-2012, PukiWiki Advance Developers Team
 * @version	 $Id: amazon.inc.php,v 3.0.3 2012/05/11 18:05:00 Logue Exp $
 * See Aloso	http://d.hatena.ne.jp/mokehehe/20090526/productadvertisingapi
 *
 */
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;
use PukiWiki\Router;
/* **************** */
/* * 設 定 必 須  * */
/* **************** */
// アソシエイト ID
defined('AMAZON_AID')				or define('AMAZON_AID', '');
// アクセスキーID http://www.amazon.co.jp/gp/feature.html?docId=451209 から取得
defined('AWS_ACCESS_KEY_ID')		or define('AWS_ACCESS_KEY_ID', '');
// 秘密キー(Product Advertising API 署名認証に必要)
defined('AWS_SECRET_ACCESS_KEY')	or define('AWS_SECRET_ACCESS_KEY', '');
/* **************** */
/* * 変 更 可 能  * */
/* **************** */
defined('USE_CACHE')			or define('USE_CACHE', true);			// キャッシュ機能の使用の有無
defined('AMAZON_EXPIRE_CACHE')	or define('AMAZON_EXPIRE_CACHE', 24);	// キャッシュの有効期限(単位:時間)
defined('AMAZON_ALLOW_CONT')	or define('AMAZON_ALLOW_CONT', true);	// true にすると、紹介本文取り込みが可能
defined('USE_CARGO')			or define('USE_CARGO', true);			// true にすると買物かごを使用可能
defined('AMAZON_NO_IMAGE')		or define('AMAZON_NO_IMAGE', 'http://images.amazon.com/images/G/09/x-locale/detail/thumb-no-image');		// 写影なしの画像
defined('AMAZON_CARGO')			or define('AMAZON_CARGO',	'http://images.amazon.com/images/G/09/extranet/associates/buttons/remote-buy-jp1.gif');	// 買物かごのアイコン

// 画像サイズ SwatchImage, SmallImage, ThumbnailImage, TinyImage, MediumImage, LargeImage
defined('PLUGIN_AMAZON_IMAGE_SIZE') or define('PLUGIN_AMAZON_IMAGE_SIZE', 'MediumImage');
defined('PLUGIN_AMAZON_CACHE_SUBDIR') or define('PLUGIN_AMAZON_CACHE_SUBDIR', 'amazon/');	// ex. 'amazon/' -> CACHE_DIR.PLUGIN_AMAZON_CACHE_SUBDIR
// Tracker プラグイン利用用
defined('PLUGIN_AMAZON_TRACKER_PAGE_NAME') or define('PLUGIN_AMAZON_TRACKER_PAGE_NAME', ':config/plugin/tracker/amazon/page');
// スキーマのバージョン
defined('PLUGIN_AMAZON_SCHEMA_VERSION') or define('PLUGIN_AMAZON_SCHEMA_VERSION', '2011-08-01');

use Zend\Http\ClientStatic;

function plugin_amazon_init()
{
	$msg = array(
		'_amazon_msg' => array(
			'msg_ReviewEdit'	=> T_("Review edit"),			//
			'msg_Code'			=> T_("(ASIN,ISBN or URL)"),	// (ASIN, ISBN or URL)
			'msg_Cargo'			=> T_("Add to Shopping Cart"),	// 買物かごへ
			'msg_Price'			=> T_("Price"),					// 価格
			'msg_Tax'			=> T_("(Including tax)"),		// (税込)
			'msg_lprice'		=> T_("List Price"),			// 参考価格
			'msg_asaved'		=> T_("You Save"),				// OFF
			'msg_psaved'		=> T_("%"),						// %
			'msg_avail'			=> T_("State of stock"),		// 在庫状況
			'msg_unavailable'	=> T_("Currently unavailable."),	// 現在在庫がありません。
			'msg_ReleaseDate'	=> T_("Release Date"),			// 発売日
			'err_code_set'		=> T_("Please specify the ASIN code, the ISBN code or URL."),	// ISBNコード、ASINコードまたはURLを指定して下さい。
			'err_not_found'		=> T_("The ASIN code is fictitious. "),	// ASINコードは架空です。
			'msg_myname'		=> T_("MY_NAME"),				// お名前
			'msg_this_edit'		=> T_("THIS EDIT"),				// ここ編集のこと
			'err_newpage'		=> T_('You have not permission to create new page.'),
			'err_nodefined'		=> T_('You must define AMAZON_AID and AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY before use.')
	)
  );
	set_plugin_messages($msg);
}

function plugin_amazon_convert()
{
	global $vars, $_string;
	global $_amazon_msg;

	if (func_num_args() == 0) {
		if( Auth::check_role('readonly') ) die_message( $_string['prohibit'] );
		return amazon_make_review_page();
	}

	if (AMAZON_AID === ''|| AWS_ACCESS_KEY_ID === '' || AWS_SECRET_ACCESS_KEY == ''){
		return '<div class="alert alert-warning">#amazon : '.$_amazon_msg['err_nodefined'].'</div>';
	}

	$argv = func_get_args();
	$parm = amazon_set_parm($argv);
	if (empty($parm['itemid']) && !empty($parm['clear'])) return amazon_ecs::clear($parm['clear']);

	$retval = '';

	if ($parm['popup']) {
		// インプレッションレポート対応
		// https://affiliate.amazon.co.jp/gp/associates/tips/impressions.html
		$amazon_aid = AMAZON_AID;
		$retval .= <<<EOD
<script type="text/javascript" src="http://www.assoc-amazon.jp/s/link-enhancer?tag={$amazon_aid}&amp;o=9"></script>
<noscript><img src="http://www.assoc-amazon.jp/s/noscript?tag={$amazon_aid}" alt="" /></noscript>
EOD;
		return $retval;
	}

	if (empty($parm['itemid'])) return '<div class="alert alert-warning">#amazon: '.$_amazon_msg['err_code_set'].'</div>';

	$obj = new amazon_ecs($parm['itemid'],$parm['locale']);
	if (!$obj->is_itemid) return false;

	$obj->set_expire($parm['expire']);
	$obj->set_cache($parm['cache']);
	$obj->set_image_size($parm['size']);

	$obj->rm_cache($parm['del']);

	$obj->get_items();

	if (!empty($obj->items['Error'])) {
		$obj->rm_cache(array('xml'=>true,'img'=>true));
		return '<div>'.$obj->items['Error'].'</div>';
	}

	// パラメータ指定なしの場合
	if ($parm['title']) {
		$style = '';
		if (!empty($obj->items['Width'])) {
			$style = ' style="width:'.$obj->items['Width'].'px;"';
		}
                $retval = '<figure class="amazon_img" style="'.amazon_ecs::style_float($parm['align']).'">'.
                        $obj->get_imagelink().
                        '<figcaption><a href="' . $obj->shop_url() . $obj->asin.'/' . AMAZON_AID . '">' . $obj->items['title'] . '</a></figcaption>'."\n".'</figure>';
		if (!empty($parm['clear'])) $retval .= amazon_ecs::clear($parm['clear']);
		return $retval;
	}


	if ($parm['image']) {
                $retval .= '<figure class="amazon_img" style="'.amazon_ecs::style_float($parm['align']).'">'.
                        $obj->get_imagelink() . '</figure>';
		
		if ($parm['image'] === 1) {
			if (!empty($parm['clear'])) $retval .= amazon_ecs::clear($parm['clear']);
			return $retval;
		}
	}

	if ($parm['cargo']) {
		$retval .= '<form method="post" action="'.$obj->cart_url() . '">'.
			'<div class="amazon_sub" style="'.amazon_ecs::style_text_align($parm['align']).'">'.
			'<input type="hidden" name="ASIN.1" value="'.$obj->asin.'" />'.
			'<input type="hidden" name="Quantity.1" value="1" />'.
			'<input type="hidden" name="AWSAccessKeyId" value="'.AWS_ACCESS_KEY_ID.'" />'.
			'<input type="hidden" name="AssociateTag" value="'.AMAZON_AID.'" />';
	} else {
		$retval .= '<div class="amazon_sub" style="'.amazon_ecs::style_text_align($parm['align']).'">';
	}

	$retval .= $obj->items['author'].'<br />'.
		   $obj->items['manufact'].'<br />';

	// 発売日
	if(!empty($obj->items['rdate'])) {
		$retval .= '<strong>'.$_amazon_msg['msg_ReleaseDate'].': </strong>'.$obj->items['rdate'].'<br />';
	}
	// 参考価格
	if(!empty($obj->items['lprice'])) {
		$retval .= '<strong>'.$_amazon_msg['msg_lprice'].': </strong><del>'.$obj->items['lprice'].
			$_amazon_msg['msg_Tax'].'</del><br />';
	}
	// 価格
	if(!empty($obj->items['nprice'])) {
		$retval .= '<strong>'.$_amazon_msg['msg_Price'].': <span style="color:#990000;">'.$obj->items['nprice'].
			$_amazon_msg['msg_Tax'].'</span></strong><br />';
	}
	// OFF
	if(!empty($obj->items['asaved'])) {
		$retval .= '<strong>'.$_amazon_msg['msg_asaved'].': </strong><span style="color:#990000;">'.$obj->items['asaved'].
			'('.$obj->items['psaved'].$_amazon_msg['msg_psaved'].')</span><br />';
	}

	// 在庫状況
	if(!empty($obj->items['avail'])) {
		$retval .= '<strong>'.$_amazon_msg['msg_avail'].': </strong>'.$obj->items['avail'].'</div>';
		if ($parm['cargo']) {
			$retval .= '<input type="image" src="'.$obj->cart_img_url().
				   '" name="submit" style="'.amazon_ecs::style_float($parm['align']).'border-style:none;" value="'.$_amazon_msg['msg_Cargo'].'" />';
		}
	} else {
		$retval .= '<strong>'.$_amazon_msg['msg_avail'].': </strong>'.$_amazon_msg['msg_unavailable'].'</div>';
	}

	if ($parm['cargo']) $retval .= '</form>';

	// 紹介文
	if ($parm['content'] && !empty($obj->items['feature'])) {
		$retval .= '<blockquote>';
		$retval .= '<ul class="amazon_feature">';
		foreach ($obj->items['feature'] as $x) {
			$retval .= '<li>'.$x.'</li>';
		}
		$retval .= '</ul>';
		$retval .= '</blockquote><div style="clear:both"></div>';
	}
	if (!empty($parm['clear'])) $retval .= amazon_ecs::clear($parm['clear']);

	return $retval;
}

function amazon_make_review_page()
{
	global $vars, $vars, $_amazon_msg;

	if (!isset($vars['page']) && !isset($vars['refer'])) return 'pagename is missing.';

	$s_page = Utility::htmlsc(isset($vars['page']) ? $vars['page'] : $vars['refer']);
	$script = get_script_uri();

	return <<<EOD
<form action="$script" method="post" class="form-horizontal plugin-amazon-form">
	<input type="hidden" name="cmd" value="amazon" />
	<input type="hidden" name="refer" value="$s_page" />
	<div class="form-group">
		<label for="amazon-locale" class="col-md-2 control-label">amazon.</label>
		<div class="col-md-10">
			<select name="locale"  class="form-control" id="amazon-locale" class="textbox">
				<option value="jp" selected="selected">co.jp</option>
				<option value="com">com</option>
				<option value="co.uk">co.uk</option>
				<option value="ca">ca</option>
				<option value="fr">fr</option>
				<option value="de">de</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="amazon-itemid" class="col-md-2 control-label">{$_amazon_msg['msg_Code']}</label>
		<div class="col-md-10">
			<input type="text" name="itemid" id="amazon-itemid" class="form-control" size="30" value="" placeholder="{$_amazon_msg['msg_Code']}" />
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-offset-2 col-md-10">
			<input type="submit" class="btn btn-default" value="{$_amazon_msg['msg_ReviewEdit']}" />
		</div>
	</div>
</form>

EOD;
}

function plugin_amazon_action()
{
	global $vars;
	global $_amazon_msg, $_string;
	global $_title;
	// global $_no_name;

	if (empty($vars['itemid'])) {
		$retvars['msg'] = $_amazon_msg['msg_ReviewEdit'];
		$retvars['body'] = amazon_make_review_page();
		return $retvars;
	} else {
		$itemid = Utility::htmlsc($vars['itemid']);
	}

	if ( Auth::check_role('readonly') ) die_message( $_string['prohibit'] );
	if ( Auth::is_check_role(PKWK_CREATE_PAGE)) die_message( $_amazon_msg['err_newpage'] );
	if (empty($vars['refer']) || !check_readable($vars['refer'], false, false)) die();

	$locale = (empty($vars['locale'])) ? 'jp' : Utility::htmlsc($vars['locale']);

	$obj = new amazon_ecs($itemid,$locale);
	if (!$obj->is_itemid) {
			$retvars['msg'] = $_amazon_msg['err_code_set'];
	$retvars['body'] = amazon_make_review_page();
			return $retvars;
	}

	$obj->get_items();

	if (empty($obj->asin)) die_message( $_amazon_msg['err_not_found'] );

	$s_page = $vars['refer'];

	// 入力された内容ではなく、一律 ASINに変換
	$r_page = $s_page . '/' . $obj->asin;
	// 入力された ISBNm ASINで作成
	// $r_page = $s_page . '/' . $obj->itemid;

	$r_page_url = rawurlencode($r_page);
	$wiki = Factory::Wiki($r_page);

	$wiki->checkEditable(true);

	if (!empty($obj->items['Error'])) {
			$obj->rm_cache(array('xml'=>true,'img'=>true));
			return array('msg'=>'Error', 'body'=>$obj->items['Error']);
	}

	if (empty($obj->items['title']) or preg_match('/^\//', $s_page)) {
		Utility::redirect(Router::get_page_uri($s_page));
	}

	// レビューページ編集
	$body = Factory::Wiki(PLUGIN_AMAZON_TRACKER_PAGE_NAME)->get(true);
	// $body = str_replace('$1', $obj->itemid, $body);
	$body = str_replace('$1', $obj->asin, $body);
	$body = str_replace('$2', $obj->locale, $body);
	$body = str_replace('[title]', $obj->items['title'], $body);
	$body = str_replace('[asin]', $obj->asin, $body);

	$author = $obj->items['author'];
	$author = (empty($author)) ? $obj->items['manufact'] : $author;
	$body = str_replace('[author]', $author , $body);

	$body = str_replace('[group]', $obj->items['group'], $body);

	$auth_key = Auth::get_user_name();
	$name = (empty($auth_key['nick'])) ? $_amazon_msg['msg_myname'] : $auth_key['nick'];
	$body = str_replace('[critic]', '[['.$name.']]', $body);

	$body = str_replace('[date]', '&date;', $body);
	$body = str_replace('[recommendation]', '[['.$_amazon_msg['msg_this_edit'].']]', $body);
	$body = str_replace('[body]', '[['.$_amazon_msg['msg_this_edit'].']]', $body);
	$wiki->set($body);
	Utility::redirect($wiki->uri('edit'));
}

function plugin_amazon_inline()
{
	global $_amazon_msg;

	if (AMAZON_AID === ''|| AWS_ACCESS_KEY_ID === '' || AWS_SECRET_ACCESS_KEY == ''){
		return '<span class="text-warning">'.$_amazon_msg['err_nodefined'].'</span>';
	}

	$argv = func_get_args();
	$parm = amazon_set_parm_inline($argv);
	if (empty($parm['itemid'])) return $_amazon_msg['err_code_set'];

	$obj = new amazon_ecs($parm['itemid'],$parm['locale']);
		if (!$obj->is_itemid) return '';

	$obj->set_expire($parm['expire']);
	$obj->set_cache($parm['cache']);
	$obj->set_image_size($parm['size']);

	$obj->rm_cache($parm['del']);

	$obj->get_items();
	if (!empty($obj->items['Error'])) return $obj->items['Error'];

	switch ($parm['item']) {
	case 'image':
		// 写像
		return $obj->get_imagelink();
	case 'title':
		// 商品名
		return '<a href="'.$obj->shop_url().$obj->asin.'/'.AMAZON_AID.'">'.$obj->items[$parm['item']].'</a>';
	case 'feature':
		// 紹介文
		if (empty($obj->items['feature'])) return '';
		$retval = '<ul class="amazon_feature">';
		foreach ($obj->items['feature'] as $x) {
			$retval .= '<li>'.$x.'</li>';
		}
		$retval .= '</ul>';
		return $retval;
	}

	// 値のみ戻す
	return $obj->items[$parm['item']];
}

function amazon_set_parm_inline($argv)
{
	$parm = array();
	$parm['itemid'] = $parm['item'] = '';
	$parm['locale'] = 'jp';
	$parm['del']['img'] = $parm['del']['xml'] = false;
	$parm['cache'] = USE_CACHE;
	$parm['expire'] = AMAZON_EXPIRE_CACHE;
	$parm['size'] = PLUGIN_AMAZON_IMAGE_SIZE; // MediumImage

	foreach($argv as $arg) {
				// $val = split('=', $arg);
		$val = explode('=', $arg);
		$val[1] = (empty($val[1])) ? Utility::htmlsc($val[0]) : Utility::htmlsc($val[1]);

		switch($val[0]) {
			case 'title':		// Title
			case 'author':		// Author, Director, Artist, Actor
			case 'manufact':	// Manufacturer
			case 'lprice':		// ListPrice
			case 'nprice':		// LowestNewPrice
			case 'asaved':		// AmountSaved
			case 'psaved':		// PercentageSaved
			case 'avail':		// Availability
			case 'feature':		// Feature
			case 'image':
			case 'rdate':		// ReleaseDate, PublicationDate
			case 'platform':	// Platform
			case 'binding':		// Binding
			case 'genre':		// Genre
			case 'ingredients':	// Ingredients, IngredientsSetElement
			case 'ean':		// EAN
			case 'mpn':		// MPN
			case 'type':		// ProductTypeName
			case 'group':		// ProductGroup
			case 'page':		// NumberOfPages
			case 'edition':		// Edition
			case 'format':		// Format
			case 'isize':		// ItemDimensions - 商品の寸法
			case 'psize':		// PackageDimensions - パッケージの寸法
				$parm['item'] = $val[1];
				break;

			case 'temp':
			case 'nocache':
				// キャッシュが有効なら無効にできる
				// if (USE_CACHE) $parm['cache'] = false;
				$parm['cache'] = false;
				break;
			case 'notemp':
			case 'cache':
				$parm['cache'] = true;
				break;
			case 'ttl':
			case 'time':
			case 'expire':
				if (is_numeric($val[1])) $parm['expire'] = $val[1];
				break;
			case 'size':
			case 'SwatchImage':
			case 'SmallImage':
			case 'ThumbnailImage':
			case 'TinyImage':
			case 'MediumImage':
			case 'LargeImage':
				$parm['size'] = $val[1];
				break;
			// 互換パラメータ
			case 'content':
				$parm['item'] = 'feature';
				break;
			case 'pricel':
				$parm['item'] = 'lprice';
				break;
			case 'price':
				$parm['item'] = 'nprice';
				break;

			case 'locale':
			case 'jp':
			case 'ca':
			case 'com':
			case 'co.uk':
			case 'de':
			case 'fr':
			case 'cn':
				$parm['locale'] = $val[1];
				break;
			case 'uk':
				$parm['locale'] = 'co.uk';
				break;
			// キャッシュ削除
			case 'delimage':
			case 'delimg':
				$parm['del']['img'] = true;
				break;
			case 'deltitle':
			case 'delxml':
				$parm['del']['xml'] = true;
				break;
			case 'del':
			case 'delete':
				$parm['del']['img'] = true;
				$parm['del']['xml'] = true;
			break;
			default:
				if (empty($parm['itemid'])) {
					$parm['itemid'] = $val[1];
				}
		}
	}
	if (empty($parm['item'])) $parm['item'] = 'title';
	return $parm;
}

function amazon_set_parm($argv)
{
	static $item_name = array('title','image','content','cargo');
	static $item_value = array(
		//			 0:title, 1:image, 2:content, 3:cargo
		'default'	=> array(1,1,0,0),
		'image'		=> array(0,1,0,0),
		'content'	=> array(0,2,1,0),
		'contentc'	=> array(0,2,1,1),
		'nocontent'	=> array(0,2,0,0),
		'nocontentc'	=> array(0,2,0,1),
		'subscript'	=> array(0,0,0,0),
		'subscriptc'	=> array(0,0,0,1),
	);

	$parm = array(
		'itemid'	=> '',
		'align'		=> '',
		'clear'		=> '',
		'image'		=> 0,
		'popup'		=> 0,
		'locale'	=>'jp',
		'del'=>array(
			'img'	=> false,
			'xml'	=> false
		),
		'cache'		=> USE_CACHE,
		'expire'	=> AMAZON_EXPIRE_CACHE,
		'size'		=> PLUGIN_AMAZON_IMAGE_SIZE // MediumImage
	);
	for($i=0;$i<count($item_name);$i++){
		$parm[$item_name[$i]] = $item_value['default'][$i];
	}

	foreach($argv as $arg) {
		// $val = split('=', $arg);
		$val = explode('=', $arg);
		$val[1] = (empty($val[1])) ? Utility::htmlsc($val[0]) : Utility::htmlsc($val[1]);

		switch($val[0]) {
		case 'r':
		case 'right':
			$parm['align'] = 'right';
			break;
		case 'n':
		case 'none':
			$parm['align'] = 'none';
			break;
		case 'l':
		case 'left':
			$parm['align'] = 'left';
			break;
		case 'center':
			$parm['align'] = 'center';
			break;
		case 'c':
		case 'clear';
			$parm['clear'] = 'clear';
			break;
		case 'cl':
		case 'clearl':
			$parm['clear'] = 'clearl';
			break;
		case 'cr':
		case 'clearr':
			$parm['clear'] = 'clearr';
			break;
		case 'temp':
		case 'nocache':
			$parm['cache'] = false;
			break;
		case 'notemp':
		case 'cache':
			$parm['cache'] = true;
			break;
		case 'ttl':
		case 'time':
		case 'expire':
			if (is_numeric($val[1])) $parm['expire'] = $val[1];
			break;
		// $item
		case 'popup':
			$parm['popup'] = 1;
			break;
		case 'cargo':
			if (USE_CARGO) $parm['cargo'] = 1;
			break;
		case 'image':
		case 'content':
		case 'contentc':
		case 'nocontent':
		case 'nocontentc':
		case 'subscript':
		case 'subscriptc':
			for($i=0;$i<6;$i++) $parm[$item_name[$i]] = $item_value[$val[0]][$i];
			break;

		case 'size':
		case 'SwatchImage':
		case 'SmallImage':
		case 'ThumbnailImage':
		case 'TinyImage':
		case 'MediumImage':
		case 'LargeImage':
						$parm['size'] = $val[1];
						break;

		// 領域指定
		case 'locale':
		case 'jp':
		case 'ca':
		case 'com':
		case 'co.uk':
		case 'de':
		case 'fr':
		case 'cn':
			$parm['locale'] = $val[1];
			break;
		case 'uk':
			$parm['locale'] = 'co.uk';
			break;
		// キャッシュ削除
		case 'delimage':
		case 'delimg':
			$parm['del']['img'] = true;
			break;
		case 'deltitle':
		case 'delxml':
			$parm['del']['xml'] = true;
			break;
		case 'del':
		case 'delete':
			$parm['del']['img'] = true;
			$parm['del']['xml'] = true;
			break;
		default:
			if (empty($parm['itemid'])) {
				$parm['itemid'] = $val[1];
			}
		}
	}
	if ($parm['cargo']) $parm['cargo'] = USE_CARGO;
	if (empty($parm['align'])) $parm['align'] = 'right';
	return $parm;
}

class amazon_ecs
{
	var $asin, $itemid, $is_itemid, $idtype,$ext;
	var $locale;
	var $items;
	var $obj_xml;
	var $expire;
	var $is_cache;
	var $image_size;

	function amazon_ecs($itemid, $locale='jp')
	{
		$this->asin = $this->itemid = '';
		$this->idtype = 'ASIN';
		$this->locale = $locale;		// 領域
		$this->expire = 24;			// キャッシュ利用時の生存時間
		$this->is_cache = false;		// キャッシュの利用有無
		$this->image_size = 'MediumImage';
		$this->align = '';

		$this->items = array();			// 整形後項目
		$this->obj_xml = array();		// xml 解析データ保存域

		$this->check_itemid($itemid);
		if (!$this->is_itemid) {
			list($tmp_itemid,$tmp_locale) = $this->itemid_lookup($itemid);
			if (!empty($tmp_itemid)) {
				$this->check_itemid($tmp_itemid);
				if ($this->is_itemid) {
					$this->locale = ($tmp_locale === 'uk') ? 'co.uk' : $tmp_locale;
				}
			}
		}
	}

	function check_itemid($itemid)
	{
		$matches = array();
		// ISBN 13桁
		if (preg_match("/^([A-Z0-9]{13})?$/", $itemid, $matches) == true) {
			$this->is_itemid = true;
			$this->itemid  = $matches[1];
			$this->idtype = 'ISBN';
			return;
		}

		if (preg_match("/^([A-Z0-9]{3})-([A-Z0-9]{10})?$/", $itemid, $matches) == true) {
			$this->is_itemid = true;
			$this->itemid  = $matches[1].$matches[2];
			$this->idtype = 'ISBN';
			return;
		}

		// ISBN 10桁 または ASIN
		if (preg_match("/^([A-Z0-9]{10}).?([0-9][0-9])?$/", $itemid, $matches) == true) {
			$this->is_itemid = true;
			$this->itemid  = $matches[1];
			$this->idtype = 'ASIN';
			$this->ext   = (empty($matches[2])) ? '09' : $matches[2];
			return;
		}

		$this->is_itemid = false;
	}

	function itemid_lookup($uri)
	{
		$patterns = array(
			'/\/gp\/product\/images\/(.*)\//i',
			'/\/gp\/product\/(.*)\//i',
			'/\/gp\/product\/(.*)\?/i',
			'/\/dp\/(.*)\//i',
			'/\/dp\/(.*)%3F/i',
			'/ASIN\/(.*)\//i',
			'/\&asin=(.*)\&/i',	// cn
			'/\&asin=(.*)/i',	// cn
		);

		foreach($patterns as $pattern) {
			if (!preg_match($pattern, $uri, $matches)) continue;
			$arr = parse_url($uri);
			$pos = strrpos($arr['host'],'.');
			$locale = ($pos === false) ? '' : strtolower( substr($arr['host'],$pos+1) );
			return array($matches[1],$locale);
		}
		return array('','jp');
	}

	function get_imagelink()
	{
		if (empty($this->items['image'])) {
			$this->items['image'] = AMAZON_NO_IMAGE;
		} else {
			if (file_exists($this->items['image'])) {
				$filename_img = substr($this->items['image'], strlen(CACHE_DIR));
				$this->items['image'] = get_cmd_uri('cache_ref','','',array('src'=>$filename_img));
			} else {
				$this->items['image'] = AMAZON_NO_IMAGE;
			}
		}

		return  '<a href="'.$this->shop_url().$this->asin.'/'.AMAZON_AID.'">'.
			'<img src="'.$this->items['image'].'" alt="'.Utility::htmlsc($this->items['title']).'"'.
			' title="'.Utility::htmlsc($this->items['title']).'" /></a>';
	}

	function file_write($filename, $data)
	{
		pkwk_touch_file($filename);
		if (!($fp = fopen($filename,'wb'))) return false;
		@flock($fp, LOCK_EX);
		fwrite($fp, $data);
		@flock($fp, LOCK_UN);
		@fclose($fp);
		return true;
	}

	function file_read($filename)
	{
		if (!($fd = fopen($filename,'rb'))) return '';
		@flock($fd, LOCK_SH);
		$rc = @fread($fd, filesize($filename));
		@flock($fd, LOCK_UN);
		fclose($fd);
		return $rc;
	}

	function page_read_xml($url)
	{
		$response = ClientStatic::get($url);
		if (! $response->isSuccess()){
			return null;
		}
		$content = $response->getBody();
		$this->obj_xml = @simplexml_load_string($content);

		// return ($rc['rc'] == 200) ? $rc['data'] : '';
		if (!$this->obj_xml->Error) {
			$this->asin = $this->obj_xml->Items->Item->ASIN;
			return $content;
		} else {
			$this->items['Error'] = '#amazon(): '.((empty($this->obj_xml->Error->Message)) ? $rc['rc'] : $this->obj_xml->Error->Message);
			return '';
		}
	}

	function page_read_img($url)
	{
//$rc = pkwk_http_request($url);
		//return ($rc['rc'] == 200) ? $rc['data'] : '';
		$response = ClientStatic::get($url);
		if ($response->isSuccess()){
			return $response->getBody();
		}
		return;
	}

	function rm_cache($func)
	{
		foreach($func as $key=>$val) {
			if (!$val) continue;
			$filename = $this->set_cache_filename($key);
			if (file_exists($filename)) @unlink($filename);
		}
		return '';
	}

	function cache_control()
	{
		$filename_xml = $this->set_cache_filename('xml');
		$filename_img = $this->set_cache_filename('img');
		$live = $expire = $this->expire * 3600;
		$live++;
		// AMAZON_NO_IMAGE - $this->items['Height'] = 91; $this->items['Width']  = 69;

		// キャッシュが存在している場合
		if (file_exists($filename_xml) && is_readable($filename_xml)) {
			// 経過秒数
			$live = time() - filemtime($filename_xml);
		}

		// 一度キャッシュを作成した場合、取得できない場合は継続利用されることになる
		if ($expire >= $live) {
			$xml = amazon_ecs::file_read($filename_xml); // read cache file.
			if (empty($xml)) {
				$this->items['image'] = '';
				$this->items['Height'] = 91;
				$this->items['Width']  = 69;
				return false;
			}
			$this->obj_xml = simplexml_load_string($xml);
			$this->asin = $this->obj_xml->Items->Item->ASIN;
			list($URL, $Height, $Width) = $this->get_image_size();
			if (file_exists($filename_img)) {
				$this->items['image']  = $filename_img;
				$this->items['Height'] = $Height;
				$this->items['Width']  = $Width;
			} else {
				$this->items['image']  = '';
				$this->items['Height'] = 91;
				$this->items['Width']  = 69;
			}
			return true;
		}

		// 直接読む場合
		$url = $this->ecs_url();
		$xml = $this->page_read_xml($url);
		if (!empty($this->items['Error'])) {
			$this->items['image'] = '';
			$this->items['Height'] = 91;
			$this->items['Width']  = 69;
			return false;
		}

		// ページが読めた場合
		amazon_ecs::file_write($filename_xml, $xml); // write xml file.
		list($URL, $Height, $Width) = $this->get_image_size();
		if (empty($URL)) {
			$this->items['image'] = '';
			$this->items['Height'] = 91;
			$this->items['Width']  = 69;
			return true;
		}

		$img = amazon_ecs::page_read_img((string)$URL);
		if (!empty($img)) amazon_ecs::file_write($filename_img, $img); // write img file.
		$this->items['image']  = $filename_img;
		$this->items['Height'] = $Height;
		$this->items['Width']  = $Width;
		return true;
	}

	function set_expire($time) { $this->expire = $time; }
	function set_cache($x) { $this->is_cache = $x; }
	function set_cache_filename($x)
	{
		static $ext = array('xml'=>'xml','img'=>'jpg');
		$retval = CACHE_DIR.PLUGIN_AMAZON_CACHE_SUBDIR.'ASIN'.$this->itemid;
		return (empty($ext[$x])) ? $retval.'.txt' : $retval.'.'.$ext[$x];
	}
	function set_image_size($x) {$this->image_size = $x; }

	function get_items()
	{
		if ($this->is_cache) {
			// $this->items['image'] の設定あり
			if (!$this->cache_control()) {
				$this->items['Error'] = 'Cache file read error.';
				return false;
			}
		} else {
			$url = $this->ecs_url();
			$xml = $this->page_read_xml($url);
			if (!empty($this->items['Error'])) {
				return false;
			}
			list($this->items['image'], $this->items['Height'], $this->items['Width']) = $this->get_image_size();
		}

		$this->items['Error']  = (empty($this->obj_xml->Items->Request->Errors->Error->Message)) ? '' : $this->obj_xml->Items->Request->Errors->Error->Message;

		$this->items['title']	   = $this->get_item_attributes('Title');
		$this->items['author']	  = $this->get_item_attributes(array('Author','Director','Artist','Actor'));
		$this->items['rdate']	   = $this->get_item_attributes(array('ReleaseDate','PublicationDate'));
		$this->items['platform']	= $this->get_item_attributes('Platform');
		$this->items['manufact']	= $this->get_item_attributes('Manufacturer');
		$this->items['binding']	 = $this->get_item_attributes('Binding');		//  ex. 単行本
		$this->items['genre']	   = $this->get_item_attributes('Genre');		//  ジャンル
		$this->items['ingredients'] = $this->get_item_attributes(array('Ingredients','IngredientsSetElement')); // 材料
		$this->items['ean']		 = $this->get_item_attributes('EAN');

		$this->items['mpn']		 = $this->get_item_attributes('MPN');		// Manufacturer Part Number
		$this->items['type']		= $this->get_item_attributes('ProductTypeName');
		$this->items['group']	   = $this->get_item_attributes('ProductGroup');
		$this->items['page']		= $this->get_item_attributes('NumberOfPages');	// 頁数
		$this->items['edition']	 = $this->get_item_attributes('Edition');		// 版数
		$this->items['format']	  = $this->get_item_attributes('Format');		// FIXME: 配列の場合あり

		$this->items['lprice'] = (empty($this->obj_xml->Items->Item->ItemAttributes->ListPrice->FormattedPrice))	? '' : $this->obj_xml->Items->Item->ItemAttributes->ListPrice->FormattedPrice;
		$this->items['nprice'] = (empty($this->obj_xml->Items->Item->OfferSummary->LowestNewPrice->FormattedPrice)) ? '' : $this->obj_xml->Items->Item->OfferSummary->LowestNewPrice->FormattedPrice;
		$this->items['asaved'] = (empty($this->obj_xml->Items->Item->Offers->Offer->OfferListing->AmountSaved->FormattedPrice)) ? ''
													   : $this->obj_xml->Items->Item->Offers->Offer->OfferListing->AmountSaved->FormattedPrice;
		$this->items['psaved'] = (empty($this->obj_xml->Items->Item->Offers->Offer->OfferListing->PercentageSaved)) ? '' : $this->obj_xml->Items->Item->Offers->Offer->OfferListing->PercentageSaved;
		$this->items['avail']  = (empty($this->obj_xml->Items->Item->Offers->Offer->OfferListing->Availability))	? '' : $this->obj_xml->Items->Item->Offers->Offer->OfferListing->Availability;


		/*
		$this->items['PackageQuantity'] = $this->get_item_attributes('PackageQuantity');	// 個数
		$this->items['BatteriesIncluded'] = $this->get_item_attributes('BatteriesIncluded');	// 1
		$this->items['BatteryType'] = $this->get_item_attributes('BatteryType');		// Lithium Ion
		$this->items['CPUManufacturer'] = $this->get_item_attributes('CPUManufacturer');	// Intel
		$this->items['CPUSpeed'] = $this->get_item_attributes('CPUSpeed');			// 2.1
		$this->items['CPUType'] = $this->get_item_attributes('CPUType');			// Pentium
		$this->items['DataLinkProtocol'] = $this->get_item_attributes('DataLinkProtocol');	// RJ-45 LAN port/USB v2.0 ports/10/100 Ethernet
		$this->items['monitor'] = $this->get_item_attributes('DisplaySize');		// 15.6
		$this->items['hdd']	 = $this->get_item_attributes('HardDiskSize');		// 500
		$this->items['MemorySlotsAvailable'] = $this->get_item_attributes('MemorySlotsAvailable');	// 2
		$this->items['os']	  = $this->get_item_attributes('OperatingSystem');	// Window 7 Home Premium 64-bit
		*/

		$this->items['isize'] = $this->get_item_dimensions('ItemDimensions');		// 商品の寸法
		$this->items['psize'] = $this->get_item_dimensions('PackageDimensions');	// パッケージの寸法

		// 商品紹介文
		$this->items['feature'] = array();
		if (AMAZON_ALLOW_CONT) {
			if (!empty($this->obj_xml->Items->Item->ItemAttributes->Feature)) {
				// $this->items['feature'] = $this->obj_xml->Items->Item->ItemAttributes->Feature;
				foreach ($this->obj_xml->Items->Item->ItemAttributes->Feature as $x) {
					//$x = preg_replace("'&amp;'", '&', $x);
					//$x = preg_replace("'&lt;'", '<', $x);
					$this->items['feature'][] = $x;
				}
			}
		}
		return true;
	}

	function get_item_attributes($pattern)
	{
		if (is_array($pattern)) {
			foreach($pattern as $x) {
				if (!empty($this->obj_xml->Items->Item->ItemAttributes->$x)) {
					return $this->obj_xml->Items->Item->ItemAttributes->$x;
				}
			}
			return '';
		}
		return (empty($this->obj_xml->Items->Item->ItemAttributes->$pattern)) ? '' : $this->obj_xml->Items->Item->ItemAttributes->$pattern;
	}

	function get_item_dimensions($item)
	{
		if (empty($this->obj_xml->Items->Item->ItemAttributes->$item)) return '';

		$Height = (empty($this->obj_xml->Items->Item->ItemAttributes->$item->Height)) ? 0 : $this->obj_xml->Items->Item->ItemAttributes->$item->Height; // H
		$Length = (empty($this->obj_xml->Items->Item->ItemAttributes->$item->Length)) ? 0 : $this->obj_xml->Items->Item->ItemAttributes->$item->Length; // D
		$Width  = (empty($this->obj_xml->Items->Item->ItemAttributes->$item->Width))  ? 0 : $this->obj_xml->Items->Item->ItemAttributes->$item->Width;  // W
		$Weight = (empty($this->obj_xml->Items->Item->ItemAttributes->$item->Weight)) ? 0 : $this->obj_xml->Items->Item->ItemAttributes->$item->Weight;

		$retval = '';
		if ($Height > 0 || $Length > 0 || $Width >0) {
			// L x W x H
			$retval .= round($Length*0.0254,1).' x '.round($Width*0.0254,1).' x '.round($Height*0.0254,1).' cm ';
		}

		if ($Weight > 0) {
			$retval .= ceil($Weight*4.5359237).' g'; // 商品重量, 発送重量
		}

		return $retval;
	}

	function get_image_size(){
		$image_size = $this->image_size;
/*
'SwatchImage'
'SmallImage'
'ThumbnailImage'
'TinyImage'
'MediumImage'
'LargeImage'
*/
		$URL	= (empty($this->obj_xml->Items->Item->ImageSets->ImageSet->$image_size->URL))	? '' : $this->obj_xml->Items->Item->ImageSets->ImageSet->$image_size->URL;
		$Height = (empty($this->obj_xml->Items->Item->ImageSets->ImageSet->$image_size->Height)) ? '' : $this->obj_xml->Items->Item->ImageSets->ImageSet->$image_size->Height;
		$Width  = (empty($this->obj_xml->Items->Item->ImageSets->ImageSet->$image_size->Width))  ? '' : $this->obj_xml->Items->Item->ImageSets->ImageSet->$image_size->Width;
		if (!empty($URL)) return array($URL, $Height, $Width);

		// FIXME: 不要かも？
		switch ($image_size) {
			case 'SmallImage':
			case 'MediumImage':
			case 'LargeImage':
				$URL	= (empty($this->obj_xml->Items->Item->$image_size->URL))	? '' : $this->obj_xml->Items->Item->$image_size->URL;
				$Height = (empty($this->obj_xml->Items->Item->$image_size->Height)) ? '' : $this->obj_xml->Items->Item->$image_size->Height;
				$Width  = (empty($this->obj_xml->Items->Item->$image_size->Width))  ? '' : $this->obj_xml->Items->Item->$image_size->Width;
		}
		return array($URL, $Height, $Width);
	}

	function ecs_url()
	{
		$method = 'GET';
		$host = 'ecs.amazonaws.'.$this->locale;	// ca,com,co.uk,de,fr,jp
		$path = '/onca/xml';
		$header = $method."\n".$host."\n".$path."\n";

		$query = array(
			'AWSAccessKeyId'	=> AWS_ACCESS_KEY_ID,
			'AssociateTag'		=> AMAZON_AID,
			'IdType'			=> $this->idtype,
			'ItemId'			=> $this->itemid,
			'Operation'			=> 'ItemLookup',
			'ResponseGroup'		=> 'ItemAttributes,Images,Offers',
//			'SearchIndex'		=> '',
			'Service'			=> 'AWSECommerceService',
			'Timestamp'			=>  gmdate('Y-m-d\TH:i:s\Z'),
			'Version'			=> PLUGIN_AMAZON_SCHEMA_VERSION
		);

		if ($this->idtype === 'ISBN') {
			$query['SearchIndex'] = 'Books';
			ksort($query);	// パラメータは、整列されている必要がある
		}

		$param  = http_build_query($query);

		$sign   = rawurlencode(base64_encode(hash_hmac('sha256',$header.$param,AWS_SECRET_ACCESS_KEY,true)));
		$url	= 'http://'.$host.$path.'?'.$param.'&Signature='.$sign;
		return $url;
	}

	function shop_url() { return 'http://www.amazon.'.$this->locale.'/exec/obidos/ASIN/'; }
	function cart_url() { return 'http://www.amazon.'.$this->locale.'/gp/aws/cart/add.html'; }
	function cart_img_url()
	{
		$locale_no = array('com'=>'01','uk'=>'02','co.uk'=>'02','de'=>'03','fr'=>'08','jp'=>'09','ca'=>'15');
		if ($this->locale ==='jp') return AMAZON_CARGO;
		if (!empty($locale_no[$this->locale])) return 'http://g-ec2.images-amazon.com/images/G/'.$locale_no[$this->locale].'/nav2/images/add-to-cart-md-p._V45690787_.gif';
		return AMAZON_CARGO;
	}

	static function clear($x)
	{
		switch ($x) {
			case 'clearl': return '<div style="clear:left;display:block;"></div>';
			case 'clearr': return '<div style="clear:right;display:block;"></div>';
			case 'clear' : return '<div style="clear:both;"></div>';
		}
		return '';
	}

	static function style_float($align)
	{
		switch($align) {
		case 'left':
		case 'right':
		case 'none':
			return 'float:'.$align.';';
		case 'center':
			return 'text-align:'.$align.';';
		}
		return 'float:right;';
	}

	function style_text_align($align)
	{
		switch($align) {
		case 'left':
		case 'right':
		case 'center':
			return 'text-align:'.$align.';';
		}
		return 'text-align:left;';
	}
}

/* End of file amazon.inc.php */
/* Location: ./wiki-common/plugin/amazon.inc.php */