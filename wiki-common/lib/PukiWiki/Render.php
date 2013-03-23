<?php
/**
 * ページ出力クラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Render.php,v 1.0.0 2013/03/23 09:30:00 Logue Exp $
 */

namespace PukiWiki;

use PukiWiki\Factory;
use PukiWiki\Renderer\Header;
use PukiWiki\Renderer\View;
use PukiWiki\Router;
use PukiWiki\Search;
use PukiWiki\Wiki;
use Zend\Http\Response;
use Zend\Json\Json;

/**
 * ページ出力クラス
 */
class Render{
	/**
	 * 通常読み込むスクリプト
	 */
	private static $default_js = array(
		/* libraly */
		'tzCalculation_LocalTimeZone',

		/* Use plugins */
		'activity-indicator',
		'jquery.a-tools',
		'jquery.autosize',
		'jquery.beautyOfCode',
	//	'jquery.codemirror',
		'jquery.cookie',
		'jquery.form',
		'jquery.dataTables',
		'jquery.dataTables.naturalsort',
		'jquery.i18n',
		'jquery.jplayer',
		'jquery.lazyload',
		'jquery.query',
		'jquery.superfish',
		'jquery.tabby',
		'jquery.ui.rlightbox',

		/* MUST BE LOAD LAST */
		'skin.original'
	);
	/**
	 * モバイル時読み込むスクリプト
	 */
	private static $mobile_js = array(
		/* Use plugins */
		'mobile/jquery.beautyOfCode',
		'mobile/jquery.i18n',
		'mobile/jquery.lazyload',
		'mobile/jquery.tablesorter',

		/* MUST BE LOAD LAST */
		'mobile/mobile.original'
	);

	private $page, $ajax, $wiki;
	/**
	 * コンストラクタ
	 * @param string $title 題目
	 * @param string $body 内容
	 */
	public function __construct($title, $body){
		global $vars;
		$this->page = isset($vars['page']) ? $vars['page'] : null;
		$this->ajax = isset($vars['ajax']) ? $vars['ajax'] : null;
		$this->wiki = !empty($this->page) ? Factory::Wiki($this->page) : null;
		self::output($title, $body);
	}
	
	/**
	 * 出力
	 * @param string $title 題名
	 * @param string $body 内容
	 * @return void
	 */
	private function output($title, $body){
		global $lastmod;
		switch ($this->ajax) {
			case 'json':
				// JSONで出力
				if (!isset($JSON)){
					// $JSON関数が定義されていない場合
					$JSON = array(
						'title'			=> $title,
						'body'			=> $body,
					);
				}
				$content_type = 'application/json';
				$content = Json::encode($JSON);
			break;
			case 'xml':
				$content_type = 'application/xml';
				$content = '<?xml version="1.0" encoding="'.CONTENT_CHARSET.'" ?>'."\n".$body;
			break;
			case 'raw':
				$content_type = 'text/plain';
				$content = $body;
			break;
			default:
				// 厳格にXHTMLとして出力する場合は、ブラウザの対応状況を読んでapplication/xhtml+xmlを出力
				$content_type =
					PKWK_STRICT_XHTML === TRUE && strstr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false ?
					'application/xhtml+xml' : 'text/html';
				$content = self::getContent($title, $body);
			break;
		}
		if (empty($page) || !$lastmod){
			$headers = Header::getHeaders($content_type);
		}else{
			$wiki = Factory::Wiki($page);
			$headers = Header::getHeaders($content_type, $page->time);
		}
		Header::writeResponse($headers, Response::STATUS_CODE_200, $content);
	}
	/**
	 * ページ出力の内容を生成
	 * @param string $title 題名
	 * @param string $body 内容
	 * @return string
	 */
	public function getContent($title, $body){
		global $vars, $js_tags, $_LINK, $info;

		global $auth_api, $fb;

		$page = isset($vars['page']) ? $vars['page'] : null;

		// ページをコンストラクト
		$_LINK = self::getLinkSet($page);

		if (!IS_MOBILE) {
			$view = new View(PLUS_THEME);
		}else{
			$view = new View('mobile');
		}

		if (!empty($page) ){
			// リファラーを保存
			$referer = Factory::Referer($page);
			$referer->set();
			
			global $attach_link, $related_link;
			$view->lastmodified = '<time pubdate="pubdate" datetime="'.get_date('c',$this->wiki->time()).'">'.get_date('D, d M Y H:i:s T', $this->wiki->time()) . ' ' . $this->wiki->passage().'</time>';

			// List of attached files to the page
			$view->attaches = ($attach_link && (exist_plugin('attach') && do_plugin_init('attach') !== FALSE)) ? attach_filelist() : '';
			$view->related = ($related_link && (exist_plugin('related') && do_plugin_init('related') !== FALSE)) ? make_related($page,'dl') : '';

			// List of footnotes
			global $foot_explain;
			ksort($foot_explain, SORT_NUMERIC);
			$view->notes = ! empty($foot_explain) ? '<ul>'.join("\n", $foot_explain).'</ul>' : '';

			// 検索語句をハイライト
			if (isset($vars['word'])){
				list($body, $notes) = self::hilightWord($vars['word'], array($body, $notes));
				$body = '<div class="small">' . $_string['word'] . Utility::htmlsc($vars['word']) . '</div>'."\n".'<hr />'."\n".$body;
			}
		}

//		if ($notify_from !== 'from@example.com') $link_tags[] = array('rev'=>'made',	'href'=>'mailto:'.$notify_from,	'title'=>	'Contact to '.$modifier);

		global $adminpass, $_string;
		if ($adminpass == '{x-php-md5}1a1dc91c907325c69271ddf0c944bc72' || $adminpass == '' ){
			$body = '<div class="message_box ui-state-error ui-corner-all">'.
				'<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>'.
				'<strong>'.$_string['warning'].'</strong> '.$_string['changeadminpass'].'</p></div>'."\n".
				$body;
		}

		$pkwk_head = $this->getMeta() . $this->getLink();
		if (!IS_MOBILE) {
			// modernizrの設定
			// Modernizrは、ヘッダー内にないと正常に動作しない
			$pkwk_head .= '<script type="text/javascript" src="'.JS_URI.'js.php?file=modernizr.min'.'"></script>'."\n";	
		}

		// フッター用
		
		if (!empty($css_blocks)){
			$pkwk_head .= self::tag_helper('style',array(array('type'=>'text/css', 'content'=>join("\n",$css_blocks))));
		}

		if (DEBUG === true && ! empty($info)){
			$body = '<div class="message_box ui-state-highlight ui-corner-all">'.
					'<p><span class="ui-icon ui-icon-info"></span>'.$_string['debugmode'].'</p>'."\n".
					'<ul>'."\n".
					'<li>'.join("</li>\n<li>",$info).'</li>'."\n".
					'</ul></div>'."\n\n".$body;
		}

		global $page_title, $newtitle, $modifier, $modifierlink;

		$view->head = $pkwk_head;
		$view->body = $body;
		$view->page = $this->page;
		$view->title = !empty($newtitle) ? $newtitle : $title;
		$view->page_title = $page_title;
		$view->is_page = !empty($this->wiki) && $this->wiki->isReadable();
		$view->js = $this->getJs();
		$view->modifier = $modifier;
		$view->modifierlink = $modifierlink;
		return $view->render();
	}
	/**
	 * JavaScriptタグを出力
	 * @return string
	 */
	private function getJs(){
		global $vars, $js_tags, $google_analytics;
		// JavaScriptフレームワーク設定
		// jQueryUI Official CDN
		// http://code.jquery.com/
		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'http://code.jquery.com/jquery-'.JQUERY_VER.'.min.js');
		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'http://code.jquery.com/ui/'.JQUERY_UI_VER.'/jquery-ui.min.js');

		// JS用初期設定
		$js_init = array(
			'DEBUG'=>constant('DEBUG'),
			'DEFAULT_LANG'=>constant('DEFAULT_LANG'),
			'IMAGE_URI'=>constant('IMAGE_URI'),
			'JS_URI'=>constant('JS_URI'),
			'LANG'=>constant('LANG'),
			'SCRIPT'=>get_script_absuri(),
			'SKIN_DIR'=>constant('SKIN_URI'),
			'THEME_NAME'=>constant('PLUS_THEME'),
		);
		// JavaScriptタグの組み立て
		if (isset($vars['page'])){
			$js_init['PAGE'] = rawurlencode($vars['page']);
			$js_init['MODIFIED'] = $this->wiki->time();
		}

		if(isset($google_analytics)){
			$js_init['GOOGLE_ANALYTICS'] = $google_analytics;
		}

		if (!IS_MOBILE){
			$jsfiles = self::$default_js;
			$c_js = 'js.php?file=skin';
		}else{
			
			// jquery mobileは、mobile.jsで非同期読み込み。
			//$js_init['JQUERY_MOBILE_VER'] = constant('JQUERY_MOBILE_VER');
			$jsfiles = self::$mobile_js;
			$c_js = 'js.php?file=mobile';
			
		}
		if (DEBUG === true) {
			// 読み込むsrcディレクトリ内のJavaScript
			foreach($jsfiles as $script_file)
				$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'src/'.$script_file.'.js');
		} else {
			//$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'mobile.js', 'defer'=>'defer');
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.$c_js);
		}

		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.( DEBUG ? 'locale.js' : 'js.php?file=locale'), 'defer'=>'defer' );

		foreach( $js_init as $key=>$val){
			if ($val !== ''){
				$js_vars[] = 'var '.$key.' = "'.$val.'";';
			}
		}
		array_unshift($pkwk_head_js,array('type'=>'text/javascript', 'content'=>join($js_vars,"\n")));
		unset($js_var, $key, $val);

		$script_tags = self::tag_helper('script',$pkwk_head_js) . self::tag_helper('script',$js_tags);
		$script_tags .= (!empty($js_blocks)) ? tag_helper('script',array(array('type'=>'text/javascript', 'content'=>join("\n",$js_blocks)))) : '';

		return $script_tags;
	}
	/**
	 * Metaタグを出力
	 * @return string
	 */
	private function getMeta(){
		global $vars, $nofollow, $google_analytics, $google_api_key, $google_site_verification, $yahoo_site_explorer_id, $bing_webmaster_tool, $shortcut_icon, $modifier, $modifierlink;
		if (IS_MOBILE){
			$meta_tags[] = array('name' => 'viewport',	'content' => 'width=device-width, initial-scale=1');
		}else{
			$meta_tags[] = array('charset'=>constant('SOURCE_ENCODING'));
			$meta_tags[] = array('name'=>'generator','content'=>constant('S_APPNAME'));
			// 管理人
			($modifier !== 'anonymous') ?			$meta_tags[] = array('name' => 'author',					'content' => $modifier) : '';
			// Googleアクセス解析
			(!empty($google_site_verification)) ?	$meta_tags[] = array('name' => 'google-site-verification',	'content' => $google_site_verification) : null;
			// Yahooアクセス解析
			(!empty($yahoo_site_explorer_id)) ?		$meta_tags[] = array('name' => 'y_key',						'content' => $yahoo_site_explorer_id) : null;
			// Bing（MSN）アクセス解析
			(!empty($bing_webmaster_tool)) ?		$meta_tags[] = array('name' => 'msvalidate.01',				'content' => $bing_webmaster_tool) : null;

			

			if ($nofollow ){
				$meta_tags[] = array('name' => 'robots', 'content' => 'NOINDEX,NOFOLLOW');
			}

			if (!empty($this->wiki) ){

				global $keywords, $description, $attach_link, $related_link, $attaches, $related, $notes, $page_title;
				// ロゴ
				$logo = !empty($view->logo['src']) ? $view->logo['src'] : IMAGE_URI.'pukiwiki_adv.logo.png';
				// 要約
				$desc = !empty($description) ? $description : mb_strimwidth(preg_replace("/[\r\n]/" ,' ' ,strip_htmltag($this->wiki->render())) ,0 ,256 ,'...');
				$meta_tags[] = array('name' => 'description', 'content' => $desc);
				// キーワード
				if (!empty($keywords)){ $meta_tags[] =  array('name' => 'keywords', 'content' => $keywords); }

				// The Open Graph Protocol
				// http://ogp.me/
				$meta_tags[] = array('property' => 'og:title',			'content' => $this->wiki->page);
				$meta_tags[] = array('property' => 'og:locale ',		'content' => LANG);
				$meta_tags[] = array('property' => 'og:type',			'content' => 'website');
				$meta_tags[] = array('property' => 'og:url',			'content' => $this->wiki->uri());
				$meta_tags[] = array('property' => 'og:image',			'content' => $logo);
				$meta_tags[] = array('property' => 'og:site_name',		'content' => $page_title);
				$meta_tags[] = array('property' => 'og:description',	'content' => $desc);
				$meta_tags[] = array('property' => 'og:updated_time',	'content' => $this->wiki->time());

				global $fb;
				if (isset($fb)){
					$meta_tags[] = array('property' => 'fb:app_id', 'content' => $fb->getAppId());
				}
			}
		}
		return self::tag_helper('meta',$meta_tags);
	}
	/**
	 * Linkタグの生成
	 * @return string
	 */
	private function getLink(){
		// scriptタグと異なり、順番が変わっても処理への影響がない。
		// http://www.w3schools.com/html5/tag_link.asp
		global $_LANG, $_LINK, $page_title;

		$shortcut_icon = isset($view->conf['shortcut_icon']) ? $view->conf['shortcut_icon'] : ROOT_URI.'favicon.ico';

		$link_tags = array(
			array('rel'=>'alternate',		'href'=>$_LINK['rss'],	'type'=>'application/rss+xml',	'title'=>'RSS'),
			array('rel'=>'canonical',		'href'=>$_LINK['reload'],	'type'=>'text/html',	'title'=>$this->page),
			array('rel'=>'contents',		'href'=>$_LINK['menu'],		'type'=>'text/html',	'title'=>$_LANG['skin']['menu']),
			array('rel'=>'sidebar',			'href'=>$_LINK['side'],		'type'=>'text/html',	'title'=>$_LANG['skin']['side']),
			array('rel'=>'glossary',		'href'=>$_LINK['glossary'],	'type'=>'text/html',	'title'=>$_LANG['skin']['glossary']),
			array('rel'=>'help',			'href'=>$_LINK['help'],		'type'=>'text/html',	'title'=>$_LANG['skin']['help']),
			array('rel'=>'home',			'href'=>$_LINK['top'],		'type'=>'text/html',	'title'=>$_LANG['skin']['top']),
			array('rel'=>'index',			'href'=>$_LINK['list'],		'type'=>'text/html',	'title'=>$_LANG['skin']['list']),
			array('rel'=>'search',			'href'=>$_LINK['opensearch'],'type'=>'application/opensearchdescription+xml',	'title'=>$page_title.$_LANG['skin']['search']),
			array('rel'=>'search',			'href'=>$_LINK['search'],	'type'=>'text/html',	'title'=>$_LANG['skin']['search']),
			array('rel'=>'sitemap',			'href'=>$_LINK['sitemap'],	'type'=>'text/html',	'title'=>'Sitemap'),
			array('rel'=>'shortcut icon',	'href'=>$shortcut_icon,		'type'=>'image/vnd.microsoft.icon')
		);
		// DNS prefetching
		// http://html5boilerplate.com/docs/DNS-Prefetching/
		$link_tags[] = array('rel'=>'dns-prefetch',		'href'=>'//code.jquery.com');
		if (COMMON_URI !== ROOT_URI){
			$link_tags[] = array('rel'=>'dns-prefetch',		'href'=>COMMON_URI);
		}
		return self::tag_helper('link',$link_tags);
	}
	/**
	 * リンク一覧を取得
	 * @param string $_page ページ名
	 * @return array
	 */
	private static function getLinkSet($_page){
		global $defaultpage, $whatsnew, $whatsdeleted, $interwiki, $aliaspage, $glossarypage;
		global $menubar, $sidebar, $navigation, $headarea, $footarea, $protect;

		global $trackback, $referer;

		// Set $_LINK for skin
		$links = array(
			'search'        => Router::get_cmd_uri('search'),
			'opensearch'    => Router::get_cmd_uri('search',    null,   null,   array('format'=>'xml')),
			'list'          => Router::get_cmd_uri('list'),
			'filelist'      => Router::get_cmd_uri('filelist'),

			'sitemap'       => Router::get_resolve_uri('list',    null, 'full',   array('type'=>'sitemap')),
			'rss'           => Router::get_resolve_uri('mixirss', null, 'full'),

			'read'          => Router::get_resolve_uri('read', $_page),
			'reload'        => Router::get_resolve_uri('read', $_page, 'full'),

			'login'         => Router::get_cmd_uri('login', $_page),
			'logout'        => Router::get_cmd_uri('login', $_page, null, array('action'=>'logout') ),

			/* Special Page */
			'help'          => Router::get_cmd_uri('help'),
			'top'           => Router::get_resolve_uri('read',$defaultpage),
			'recent'        => Router::get_resolve_uri('read',$whatsnew),
			'deleted'       => Router::get_resolve_uri('read',$whatsdeleted),
			'interwiki'     => Router::get_resolve_uri('read',$interwiki),
			'alias'         => Router::get_resolve_uri('read',$aliaspage),
			'glossary'      => Router::get_resolve_uri('read',$glossarypage),
			'menu'          => Router::get_resolve_uri('read',$menubar),
			'side'          => Router::get_resolve_uri('read',$sidebar),
			'navigation'    => Router::get_resolve_uri('read',$navigation),
			'head'          => Router::get_resolve_uri('read',$headarea),
			'foot'          => Router::get_resolve_uri('read',$footarea),
			'protect'       => Router::get_resolve_uri('read',$protect)
		);

        if (empty($_page)) {
			$links = array_merge($links,array(
				'add'           => Router::get_cmd_uri('add'),
				'backup'        => Router::get_cmd_uri('backup'),
				'copy'          => Router::get_cmd_uri('template'),
				'log'           => Router::get_cmd_uri('logview'),
				'log_browse'    => Router::get_cmd_uri('logview',   null,   null,   array('kind'=>'browse')),
				'log_check'     => Router::get_cmd_uri('logview',   null,   null,   array('kind'=>'check')),
				'log_down'      => Router::get_cmd_uri('logview',   null,   null,   array('kind'=>'download')),
				'log_login'     => Router::get_cmd_uri('logview',   null,   null,   array('kind'=>'login')),
				'log_update'    => Router::get_cmd_uri('logview'),
				'new'           => Router::get_cmd_uri('newpage'),
				'newsub'        => Router::get_cmd_uri('newpage_subdir'),
				'rename'        => Router::get_cmd_uri('rename'),
				'upload_list'   => Router::get_cmd_uri('attach',    null,   null,   array('pcmd'=>'list'))
			));
		}else{
			$links = array_merge($links,array(
				'add'           => Router::get_cmd_uri('add',           $_page),
				'backup'        => Router::get_cmd_uri('backup',        $_page),
				'brokenlink'    => Router::get_cmd_uri('brokenlink',    $_page),
				'copy'          => Router::get_cmd_uri('template',      null,   null,   array('refer'=>$_page)),
				'diff'          => Router::get_cmd_uri('diff',          $_page),
				'edit'          => Router::get_cmd_uri('edit',          $_page),
				'freeze'        => Router::get_cmd_uri('freeze',        $_page),
				'guiedit'       => Router::get_cmd_uri('guiedit',       $_page),

				'log'           => Router::get_cmd_uri('logview',       $_page),
				'log_browse'    => Router::get_cmd_uri('logview',       $_page, null,   array('kind'=>'browse')),
				'log_check'     => Router::get_cmd_uri('logview',       $_page, null,   array('kind'=>'check')),
				'log_down'      => Router::get_cmd_uri('logview',       $_page, null,   array('kind'=>'download')),
				'log_login'     => Router::get_cmd_uri('logview',       null,   null,   array('kind'=>'login')),
				'log_update'    => Router::get_cmd_uri('logview',       $_page),
				'new'           => Router::get_cmd_uri('newpage',       null,   null,   array('refer'=>$_page)),
				'newsub'        => Router::get_cmd_uri('newpage_subdir',null,   null,   array('directory'=>$_page)),
				'rename'        => Router::get_cmd_uri('rename',        null,   null,   array('refer'=>$_page)),

				'source'        => Router::get_cmd_uri('source',        $_page),

				'unfreeze'      => Router::get_cmd_uri('unfreeze',      $_page),
				'upload'        => Router::get_cmd_uri('attach',        $_page, null,   array('pcmd'=>'upload')), // link rel="alternate" にも利用するため absuri にしておく

				'template'      => Router::get_cmd_uri('template',      null,   null,   array('refer'=>$_page))
			));
		}

		if ($referer){
			$links['referer']    = !empty($_page) ? Router::get_cmd_uri('referer',    $_page) : Router::get_cmd_uri('referer');
		}
		ksort($links);
		return $links;
	}
	/**
	 * ワードをハイライト
	 * @param string or array $target ハイライトさせたいワード
	 * @param array $content 対象の文字列
	 * @return string
	 */
	private static function hilightWord($target, $content){
		$contents = is_string($content) ? array($content) : $content;

		// ワードが配列で渡されてないときはスペースと+の部分で分割
		$words = is_string($target) ? preg_split('/\s+/', $target, -1, PREG_SPLIT_NO_EMPTY) : $target;
		$words = array_splice($words, 0, 10); // Max: 10 words
		$words = array_flip($words);

		$keys = array();
		foreach ($words as $word=>$id) $keys[$word] = strlen($word);

		$keys = Search::get_search_words(array_keys($keys), TRUE);
		$id = 0;
		foreach ($keys as $key=>$pattern) {
			$s_key    = Utility::htmlsc($key);
			$pattern  = '/' .
				'<textarea[^>]*>.*?<\/textarea>' .  // textareaを除外
				'|' . '<[^>]*>' .                   // タグを除外
				'|' . '&[^;]+;' .                   // インライン型プラグイン名や参照文字を除外
				'|' . '(' . $pattern . ')' .        // $matches[1]: 検索語句
				'/sS';
				// ハイライトさせる関数を生成
				$decorate_Nth_word = create_function(
					'$matches',
					'return isset($matches[1]) ? ' . '\'<mark class="word' .$id .'">\' . $matches[1] . \'</mark>\' : ' . '$matches[0];'
				);

			// 書き換え
			foreach($contents as $content){
				$contents = preg_replace_callback($pattern, $decorate_Nth_word, $content);
			}
			++$id;
		}
		return $contents;
	}
	/**
	 * 配列からタグを生成
	 * @param string $tagname タグ名
	 * @param array $tags 内容
	 * @return string
	 */
	private static function tag_helper($tagname,$tags){
		$out = array();
		foreach ($tags as $tag) {
			// linkタグで、rel属性やtype属性がない場合スタイルシートとする。
			if ($tagname == 'link' && (empty($tags['rel'])) ){
				$tags['rel'] = 'stylesheet';
			}

			if (isset($tags['rel']) && $tags['rel'] == 'stylesheet'){
				$tags['type'] = 'text/css';
			}

			// scriptタグでtypeが省略されていた場合JavaScriptとする。
			if ($tagname == 'script' && empty($tags['type'])){
				$tags['type'] = 'text/javascript';
			}

			// タグをパース
			foreach( $tag as $key=>$val){
				$IE_flag = '';
				if ($key == 'content' && ($tagname == 'script' || $tagname == 'style')){
					$content = ($tagname !== 'style') ? '/'.'*<![CDATA[*'.'/'."\n".$val."\n".'/'.'*]]>*'.'/' : '/'.'/<![CDATA['."\n". $val . "\n".'/'.'/]]>';
				}else if($key == 'IE_flag'){
					$IE_flag = $val;
				}else{
					$tag_contents[] = $key.'="'.Utility::htmlsc($val).'"';
				}
			}
			unset($tag, $key, $val);
			// タグの属性を結合
			$tag_content = join(' ',$tag_contents);
			if ($tagname == 'script' || $tagname == 'style'){
				if (empty($content)){
					$ret = '<'.$tagname.' '.$tag_content.'></'.$tagname.'>';
				}else{
					$ret = '<'.$tagname.' '.$tag_content.'>'.$content.'</'.$tagname.'>';
				}
			}else{
				$ret = '<'.$tagname.' '.$tag_content.' />';
			}

			if ($IE_flag){
				$out[] = '<!--[if lte IE '.$IE_flag.']>'.$ret.'<![endif]-->';
			}else{
				$out[] = $ret;
			}
			unset($tag_contents,$tag_content,$key,$val,$content,$IE_flag,$ret);
		}

		return join("\n",$out)."\n";
	}
}