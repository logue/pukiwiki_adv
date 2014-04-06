<?php
/**
 * ページ出力クラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Render.php,v 1.0.0 2014/03/12 20:12:00 Logue Exp $
 */

namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Renderer\Header;
use PukiWiki\Renderer\View;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Router;
use PukiWiki\Search;
use PukiWiki\Time;
use Zend\Http\Response;
use Zend\Json\Json;
use PukiWiki\File\File;

/**
 * ページ出力クラス
 */
class Render{
	/**
	 * 厳格なXHTMLモードを使用する
	 */
	const USE_STRICT_XHTML = false;
	/**
	 * jQueryのバージョン
	 */
	const JQUERY_VER = '2.1.0';
	//const JQUERY_VER = '1.11.0';
	/**
	 * jQuery UIのバージョン
	 */
	const JQUERY_UI_VER = '1.10.4';
	/**
	 * jQuery Mobileのバージョン
	 */
	const JQUERY_MOBILE_VER = '1.4.2';
	/**
	 * Twitter Bootstrapのバージョン
	 */
	const TWITTER_BOOTSTRAP_VER = '3.1.1';
	/**
	 * Font Awesomeのバージョン
	 */
	const FONT_AWESOME_VER = '4.0.3';
	/**
	 * スキンスクリプト（未圧縮）
	 */
	const DEFAULT_JS = 'skin.original.js';
	/**
	 * スキンスクリプト（圧縮）
	 */
	const DEFAULT_JS_COMPRESSED = 'js.php?file=skin';
	/**
	 * モバイルスクリプト（未圧縮）
	 */
	const MOBILE_JS = 'mobile.original.js';
	/**
	 * モバイルスクリプト（圧縮）
	 */
	const MOBILE_JS_COMPRESSED = 'js.php?file=mobile';
	/**
	 * jQueryのCDNドメイン名
	 */
	const JQUERY_CDN = 'code.jquery.com';
	/**
	 * BootstrapのCDNドメイン名
	 */
	const BOOTSTRAP_CDN = 'netdna.bootstrapcdn.com';
	
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
		'jquery.cookie',
		'jquery.dataTables',
		'jquery.dataTables.naturalsort',
		'jquery.form',
		'jquery.i18n',
		'jquery.query',
		'jquery.superfish',
		'jquery.tabby',
		'jquery.ui.rlightbox'
	);
	/**
	 * モバイル時読み込むスクリプト
	 */
	private static $mobile_js = array(
		/* Use plugins */
		'jquery.beautyOfCode',
		'jquery.i18n',
		'jquery.tablesorter'
	);

	private $page, $ajax, $wiki;
	/**
	 * コンストラクタ
	 * @param string $title 題目
	 * @param string $body 内容
	 */
	public function __construct($title, $body, $http_code = Response::STATUS_CODE_200){
		global $vars, $lastmod;
		$this->page = isset($vars['page']) ? $vars['page'] : null;
		$this->ajax = isset($vars['ajax']) ? $vars['ajax'] : null;
		$this->wiki = !empty($this->page) ? Factory::Wiki($this->page) : null;
		$this->title = $title;
		$this->body = $body;
		switch ($this->ajax) {
			case 'json':
				$content_type = 'application/json';
				$content = Json::encode(array(
					'title' => $this->title,
					'body'  => $this->body,
					'process_time' => Time::getTakeTime()
				));
			break;
			case 'xml':
				$content_type = 'application/xml';
				$content = '<?xml version="1.0" encoding="'.CONTENT_CHARSET.'" ?>'."\n".$this->body;
			break;
			case 'raw':
				$content_type = 'text/plain';
				$content = $this->body;
			break;
			default:
				// 厳格にXHTMLとして出力する場合は、ブラウザの対応状況を読んでapplication/xhtml+xmlを出力
				$content_type =
					self::USE_STRICT_XHTML === TRUE && strstr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false ?
					'application/xhtml+xml' : 'text/html';
				$content = self::getContent();
			break;
		}
		if (empty($this->page) || !$lastmod){
			$headers = Header::getHeaders($content_type);
		}else{
			// ページ名が定義されている場合、最終更新日時をヘッダーに追加
			$headers = Header::getHeaders($content_type, $this->wiki->time());
		}
		Header::writeResponse($headers, $http_code, $content);
	}
	/**
	 * ページ出力の内容を生成
	 * @return string
	 */
	public function getContent(){
		global $_LINK, $info, $vars, $_LANG;
		global $site_name, $newtitle, $modifier, $modifierlink, $menubar, $sidebar, $headarea, $footarea, $navigation, $attach_link, $related_link;

		$body = $this->body;

		$_LINK = self::getLinkSet($this->page);

		// ページをコンストラクト
		$view = new View(PLUS_THEME);

		$view->is_page = isset($vars['page']);
		$view->is_read = isset($vars['cmd']) && $vars['cmd'] === 'read';
		$view->is_freeze = isset($vars['page']) ? Factory::Wiki($vars['page'])->isFreezed() : false;

		if ($vars['cmd'] === 'read'){
			global $adminpass, $_string, $menubar, $sidebar;
			if ($adminpass == '{x-php-md5}1a1dc91c907325c69271ddf0c944bc72' || $adminpass == '' ){
				$body = '<p class="alert alert-danger"><span class="fa fa-exclamation-triangle"></span>'.
					'<strong>'.$_string['warning'].'</strong> '.$_string['changeadminpass'].'</p>'."\n".
					$body;
			}

			if (DEBUG === true && ! empty($info)){
				$body = '<div class="panel panel-info" id="pkwk-info">'.
						'<div class="panel-heading"><span class="fa fa-info-circle"></span>'.$_string['debugmode'].'</div>'."\n".
						'<div class="panel-body">' . "\n" . '<ul>'."\n".
						'<li>'.join("</li>\n<li>",$info).'</li>'."\n".
						'</ul></div></div>'."\n\n".$body;
			}
			// リファラーを保存
			if ($this->page !== null) Factory::Referer($this->page)->set();
			
			global $attach_link, $related_link;
			$view->lastmodified = '<time datetime="'.Time::getZoneTimeDate('c',$this->wiki->time()).'">'.Time::getZoneTimeDate('D, d M Y H:i:s T', $this->wiki->time()) . ' ' . $this->wiki->passage().'</time>';
			if (!$this->wiki->isEditable()){
				$view->lastmodified .= '<span class="fa fa-lock"></span>';
			}

			// ページの添付ファイル、関連リンク
			if ($attach_link) {
				$view->attaches = $this->getAttaches();
			}
			if ($related_link) {
				$view->related = $this->getRelated();
			}

			// ノート
			global $foot_explain;
			ksort($foot_explain, SORT_NUMERIC);
			$notes = ! empty($foot_explain) ? '<ul>'.join("\n", $foot_explain).'</ul>' : '';

			// 検索語句をハイライト
			if (isset($vars['word'])){
				$notes = self::hilightWord($vars['word'],$notes);
				$body = '<p class="alert alert-info">' . $_string['word'] . '<var>' . Utility::htmlsc($vars['word']) . '</var></p>'."\n".'<hr />'."\n".self::hilightWord($vars['word'], $body);
			}
			$view->notes = $notes;
		}

		// モードによって、3カラム、2カラムを切り替える。
		if ($vars['cmd'] === 'read') {
			$view->menubar = Factory::Wiki($menubar)->has() ? PluginRenderer::executePluginBlock('menu') : null;
			if ( Factory::Wiki($sidebar)->has()){
				$view->sidebar = Factory::Wiki($sidebar)->has() ? PluginRenderer::executePluginBlock('side') : null;
				$view->colums = View::CLASS_THREE_COLUMS;
			}else{
				$view->colums = View::CLASS_TWO_COLUMS;
			}
		}else{
			$view->colums = View::CLASS_NO_COLUMS;
		}

		// ナビバー
		$view->navibar = PluginRenderer::executePluginBlock('navibar',$view->conf['navibar']);
		// ツールバー
		$view->toolbar = PluginRenderer::executePluginBlock('toolbar',$view->conf['toolbar']);
		// <head>タグ内
		$view->head = self::getHead($view->conf);
		// ナビゲーション
		$view->navigation = Factory::Wiki($navigation)->has() ? PluginRenderer::executePluginBlock('suckerfish') : null;
		// ヘッドエリア
		$view->headarea = Factory::Wiki($headarea)->has() ? PluginRenderer::executePluginInline('headarea') : null;
		// フッターエリア
		$view->footarea = Factory::Wiki($footarea)->has() ? PluginRenderer::executePluginInline('footarea') : null;
		// パンくずリスト
		$view->topicpath = $this->getBreadcrumbs();
		// 中身
		$view->body = $body;
		// サイト名
		$view->site_name = $site_name;
		// ページ名
		$view->page = $this->page;
		// タイトル
		$view->title = !empty($newtitle) ? $newtitle : $this->title;
		// 管理人の名前
		$view->modifier = $modifier;
		// 管理人のリンク
		$view->modifierlink = $modifierlink;
		// JavaScript
		$view->js = $this->getJs();
		// 汎用ワード
		$view->strings = $_LANG;
		// 表示言語
		$view->lang = substr(LANG,0,2);
		// テーマディレクトリへの相対パス
		$view->path =  SKIN_URI . THEME_PLUS_NAME . (!IS_MOBILE ? PLUS_THEME : 'mobile') . '/';

		$view->links = $_LINK;

		return $view->__toString();
	}
	/**
	 * JavaScriptタグを出力
	 * @return string
	 */
	private function getJs(){
		global $vars, $js_tags, $js_blocks, $google_analytics;

		// JavaScriptフレームワーク設定
		// jQueryUI Official CDN
		// http://code.jquery.com/
		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'//'.self::JQUERY_CDN.'/jquery-'.self::JQUERY_VER.'.min.js', 'defer'=>'defer');
		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'//'.self::JQUERY_CDN.'/ui/'.self::JQUERY_UI_VER.'/jquery-ui.min.js', 'defer'=>'defer');

		// Bootstrap
		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'//'.self::BOOTSTRAP_CDN . '/bootstrap/' . self::TWITTER_BOOTSTRAP_VER . '/js/bootstrap.min.js', 'defer'=>'defer' );

		// JS用初期設定
		$js_init = array(
			'DEBUG'         => constant('DEBUG'),
			'DEFAULT_LANG'  => constant('DEFAULT_LANG'),
			'IMAGE_URI'     => constant('IMAGE_URI'),
			'JS_URI'        => constant('JS_URI'),
			'LANG'          => constant('LANG'),
			'SCRIPT'        => Router::get_script_absuri(),
			'SKIN_DIR'      => constant('SKIN_URI'),
			'THEME_NAME'    => constant('PLUS_THEME'),
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
		}else{
			// jquery mobileは、mobile.jsで非同期読み込み。
			$js_init['JQUERY_MOBILE_VER'] = self::JQUERY_MOBILE_VER;
			$jsfiles = self::$mobile_js;
		}
		if (DEBUG === true) {
			// 読み込むsrcディレクトリ内のJavaScript
			foreach($jsfiles as $script_file)
				$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.(!IS_MOBILE ? 'src/' : 'mobile/').$script_file.'.js', 'defer'=>'defer');
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.(!IS_MOBILE ? 'src/'.self::DEFAULT_JS : 'mobile/'.self::MOBILE_JS ), 'defer'=>'defer');
		} else {
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.(IS_MOBILE ? self::MOBILE_JS_COMPRESSED : self::DEFAULT_JS_COMPRESSED), 'defer'=>'defer' );
		}

		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.( DEBUG ? 'locale.js' : 'js.php?file=locale'), 'defer'=>'defer' );

//		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.( DEBUG ? 'codemirror/lib/codemirror.js' : 'js.php?file=codemirror/lib/codemirror.min'), 'defer'=>'defer' );

//		$js_vars[] = 'var pukiwiki = {};';
		foreach( $js_init as $key=>$val){
			$js_vars[] = 'var '.$key.' = ' . (!empty($val) ? '"'.$val.'"' : 'false') .';';
		}
		array_unshift($pkwk_head_js,array('type'=>'text/javascript', 'content'=>join($js_vars,"\n")));
		unset($js_var, $key, $val);

		// MathJax
//		$pkwk_head_js[] = array('type'=>'text/x-mathjax-config', 'content'=>MathJax::MATHJAX_CONF);
//		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>MathJax::MATHJAX_URL );

		$script_tags = self::tag_helper('script',$pkwk_head_js) . self::tag_helper('script',$js_tags);
		$script_tags .= (!empty($js_blocks)) ? self::tag_helper('script',array(array('type'=>'text/javascript', 'content'=>join("\n",$js_blocks)))) : '';

		if (defined('BB2_CWD')){
			global $bb2_javascript;
			// Bad-behavior
			$script_tags .= $bb2_javascript;
		}

		return $script_tags;
	}
	/**
	 * ヘッダータグを出力
	 * @param array $sytlesheet
	 * @return string
	 */
	private function getHead($conf){
		global $vars, $nofollow, $google_analytics, $google_api_key, $google_site_verification, $yahoo_site_explorer_id, $bing_webmaster_tool, $shortcut_icon, $modifier, $modifierlink;
		$meta_tags[] = array('charset'=>constant('SOURCE_ENCODING'));
		// $meta_tags[] = array('http-equiv'=>'x-dns-prefetch-control','content'=>'on');
		if (IS_MOBILE){
			$meta_tags[] = array('name' => 'viewport',	'content' => 'width=device-width, initial-scale=1');
		}else{
			$meta_tags[] = array('name'=>'generator','content'=>constant('S_APPNAME'));
			// 管理人
			($modifier !== 'anonymous') ?			$meta_tags[] = array('name' => 'author',					'content' => $modifier) : '';
			// Googleアクセス解析
			(!empty($google_site_verification)) ?	$meta_tags[] = array('name' => 'google-site-verification',	'content' => $google_site_verification) : null;
			// Yahooアクセス解析
			(!empty($yahoo_site_explorer_id)) ?		$meta_tags[] = array('name' => 'y_key',						'content' => $yahoo_site_explorer_id) : null;
			// Bing（MSN）アクセス解析
			(!empty($bing_webmaster_tool)) ?		$meta_tags[] = array('name' => 'msvalidate.01',				'content' => $bing_webmaster_tool) : null;

			if ($nofollow && $vars['cmd'] === 'read'){
				$meta_tags[] = array('name' => 'robots', 'content' => 'NOINDEX,NOFOLLOW');
			}

			if (!empty($this->wiki) && $vars['cmd'] === 'read'){
				global $keywords, $description, $site_name, $site_logo;
				// 要約
				$desc = !empty($description) ? $description : $this->wiki->description();
				$meta_tags[] = array('name' => 'description', 'content' => $desc);
				// キーワード
				if (!empty($keywords)){ $meta_tags[] =  array('name' => 'keywords', 'content' => $keywords); }

				// The Open Graph Protocol
				// http://ogp.me/
				$meta_tags[] = array('property' => 'og:title',			'content' => $this->wiki->page);
				$meta_tags[] = array('property' => 'og:locale ',		'content' => LANG);
				$meta_tags[] = array('property' => 'og:type',			'content' => 'website');
				$meta_tags[] = array('property' => 'og:url',			'content' => $this->wiki->uri());
				$meta_tags[] = array('property' => 'og:image',			'content' => $site_logo);
				$meta_tags[] = array('property' => 'og:site_name',		'content' => $site_name);
				$meta_tags[] = array('property' => 'og:description',	'content' => $desc);
				$meta_tags[] = array('property' => 'og:updated_time',	'content' => $this->wiki->time());

				global $fb;
				if (isset($fb)){
					$meta_tags[] = array('property' => 'fb:app_id', 'content' => $fb->getAppId());
				}
			}
			// Linkタグの生成
			// scriptタグと異なり、順番が変わっても処理への影響がない。
			// http://www.w3schools.com/html5/tag_link.asp
			global $_LANG, $_LINK, $site_name;

			$link_tags = array(
				array('rel'=>'alternate',		'href'=>$_LINK['rss'],	'type'=>'application/rss+xml',	'title'=>'RSS'),
				array('rel'=>'canonical',		'href'=>$_LINK['reload'],	'type'=>'text/html',	'title'=>$this->page),
				array('rel'=>'contents',		'href'=>$_LINK['menu'],		'type'=>'text/html',	'title'=>$_LANG['skin']['menu']),
				array('rel'=>'glossary',		'href'=>$_LINK['glossary'],	'type'=>'text/html',	'title'=>$_LANG['skin']['glossary']),
				array('rel'=>'help',			'href'=>$_LINK['help'],		'type'=>'text/html',	'title'=>$_LANG['skin']['help']),
				array('rel'=>'home',			'href'=>$_LINK['top'],		'type'=>'text/html',	'title'=>$_LANG['skin']['top']),
				array('rel'=>'index',			'href'=>$_LINK['list'],		'type'=>'text/html',	'title'=>$_LANG['skin']['list']),
				array('rel'=>'pingback',	    'href'=>$_LINK['pingback'], 'type'=>'application/xml'),
				array('rel'=>'search',			'href'=>$_LINK['opensearch'],'type'=>'application/opensearchdescription+xml',	'title'=>$site_name.$_LANG['skin']['search']),
				array('rel'=>'search',			'href'=>$_LINK['search'],	'type'=>'text/html',	'title'=>$_LANG['skin']['search']),
				array('rel'=>'shortcut icon',	'href'=>isset($conf['shortcut_icon']) ? $conf['shortcut_icon'] : ROOT_URI.'favicon.ico'),
				array('rel'=>'sidebar',			'href'=>$_LINK['side'],		'type'=>'text/html',	'title'=>$_LANG['skin']['side']),
				array('rel'=>'sitemap',			'href'=>$_LINK['sitemap'],	'type'=>'application/xml')
			);
		}

		// DNS prefetching
		// http://html5boilerplate.com/docs/DNS-Prefetching/
		$link_tags[] = array('rel'=>'dns-prefetch',		'href'=>'//'.self::JQUERY_CDN);
		$link_tags[] = array('rel'=>'dns-prefetch',		'href'=>'//'.self::BOOTSTRAP_CDN);
		if (COMMON_URI !== ROOT_URI){
			$link_tags[] = array('rel'=>'dns-prefetch',		'href'=>COMMON_URI);
		}

		// Twitter Bootstrap
		// http://getbootstrap.com/
		if ($conf['bootswatch'] === false || empty($conf['bootswatch'])){
			$link_tags[] = array('rel'=>'stylesheet', 'href'=>'//' . self::BOOTSTRAP_CDN . '/bootstrap/' . self::TWITTER_BOOTSTRAP_VER . '/css/bootstrap.min.css', 'type'=>'text/css');
			$link_tags[] = array('rel'=>'stylesheet', 'href'=>'//' . self::BOOTSTRAP_CDN . '/bootstrap/' . self::TWITTER_BOOTSTRAP_VER . '/css/bootstrap-theme.min.css', 'type'=>'text/css', 'id'=>'bootstrap-theme');
		}else{
			// Bootswatch
			// http://bootswatch.com/
			$link_tags[] = array('rel'=>'stylesheet', 'href'=>'//' . self::BOOTSTRAP_CDN . '/bootswatch/' . self::TWITTER_BOOTSTRAP_VER . '/' . $conf['bootswatch'] . '/bootstrap.min.css', 'type'=>'text/css', 'id'=>'bootstrap-theme');
		}

		// jQuery UIのテーマ
		if (! empty($conf['ui_theme']) && $conf['ui_theme'] !== false){
			$link_tags[] = array('rel'=>'stylesheet', 'href'=>'//code.jquery.com/ui/' . self::JQUERY_UI_VER .'/themes/' . $conf['ui_theme'] . '/jquery-ui.min.css', 'type'=>'text/css', 'id'=>'ui-theme');
		}

		// 標準スタイルシート
		if ($conf['default_css'] ){
			$link_tags[] = array('rel'=>'stylesheet', 'href'=> COMMON_URI . 'css/pukiwiki.' . (DEBUG ? 'css' : 'min.css'), 'type'=>'text/css');
		}

		// Font Awesome
		// http://fontawesome.io/
		// ※フォントを標準スタイルシートで書き換えているので、標準スタイルシートよりも後でFont Awesomeを読み込む
		$link_tags[] = array('rel'=>'stylesheet', 'href'=>'//' . self::BOOTSTRAP_CDN . '/font-awesome/' . self::FONT_AWESOME_VER . '/css/font-awesome.min.css', 'type'=>'text/css');

		return
			self::tag_helper('meta',$meta_tags) .
			self::tag_helper('link',$link_tags) .
			// Modernizrはヘッダー内でないと動作しない
			(!IS_MOBILE ? '<script type="text/javascript" src="'.JS_URI.'js.php?file=modernizr.min'.'"></script>'."\n" : '');
	}
	/**
	 * リンク一覧を取得
	 * @param string $_page ページ名
	 * @return array
	 */
	private static function getLinkSet($_page = ''){
		static $d_links;

		if (!isset($d_links)){
			global $defaultpage, $whatsnew, $whatsdeleted, $interwiki, $aliaspage, $glossarypage;
			global $menubar, $sidebar, $navigation, $headarea, $footarea, $protect;
			// Set $_LINK for skin
			$d_links = array(
				'search'        => Router::get_cmd_uri('search'),
				'opensearch'    => Router::get_cmd_uri('search',    null,   null,   array('format'=>'xml')),
				'list'          => Router::get_cmd_uri('list'),
				'filelist'      => Router::get_cmd_uri('filelist'),

				'sitemap'       => Router::get_resolve_uri('list',    null, 'full',   array('type'=>'sitemap')),
				'rss'           => Router::get_resolve_uri('feed', null, 'full'),

				'read'          => Router::get_resolve_uri('read', $_page),
				'reload'        => Router::get_resolve_uri('read', $_page, 'full'),
				'related'       => Router::get_resolve_uri('related', $_page),

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
				'protect'       => Router::get_resolve_uri('read',$protect),

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
				'upload_list'   => Router::get_cmd_uri('attach',    null,   null,   array('pcmd'=>'list')),
				'referer'       => Router::get_cmd_uri('referer'),
				'pingback'      => Router::get_cmd_uri('xmlrpc')
			);
		}
		$links = $d_links;

		if (!empty($_page)){
			static $p_links;
			if (!isset($p_links[$_page])){
				$p_links[$_page] = array(
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

					'template'      => Router::get_cmd_uri('template',      null,   null,   array('refer'=>$_page)),
					'referer'       => Router::get_cmd_uri('referer',       $_page),
				);
			}
			$links = array_merge($d_links,$p_links[$_page]);
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
		foreach ($words as $word=>$id){
			$keys[$word] = strlen($word);
		}

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
					$tag_contents[] = $key.'="'.($key !=='href' ? Utility::htmlsc($val) : $val ).'"';
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
	/**
	 * 添付ファイル一覧
	 * @return string
	 */
	private function getAttaches(){
		// TODO: UPLOAD_DIRの参照方法の変更
		global $_LANG;
		$ret = array();
		$exists = false;
		$attaches = $this->wiki->attach(false);
		if (!empty($attaches)) {
			$ret[] = '<dl class="list-inline">';
			$ret[] = '<dt>'.$_LANG['skin']['attach_title'].'</dt>';
			foreach ($attaches as $filename=>$files){
				if (!isset($files[0])) continue;
				$fileinfo = new File(UPLOAD_DIR . $files[0]);
				$exists = true;
				if (!$fileinfo->has()) continue;
				$logfileinfo = new File(UPLOAD_DIR . $files['log']);
				$count = $logfileinfo->has() ? $logfileinfo->head(0) : '0';
				$ret[] = '<dd><a href="' . 
					Router::get_cmd_uri('attach', null, null, array('pcmd'=>'open','refer'=>$this->page, 'age'=>0, 'openfile'=>$filename)) .
					'" title="' . Time::getZoneTimeDate('Y/m/d H:i:s', $fileinfo->time()) . ' ' .
					sprintf('%01.1f', round($fileinfo->getSize()/1024, 1)) . 'KB' .
					'"><span class="fa fa-download"></span>'.Utility::htmlsc($filename).'</a> ' .
					'<small>(<var>' . $count . '</var>)</small> ' .
					'<a href="' . Router::get_cmd_uri('attach', null, null, array('pcmd'=>'info','refer'=>$this->page, 'file'=>$filename)) . '" class="btn btn-default btn-xs" title="'.$_LANG['skin']['attach_info'].'">' .
					'<span class="fa fa-info"></span></a>' .
					'</dd>';
			}
			$ret[] = '</dl>';
		}
		return $exists ? join("\n", $ret) : null;
	}
	/**
	 * 関連リンク一覧
	 * @return string
	 */
	private function getRelated(){
		global $_LANG;
		$related = $this->wiki->related();
		if (empty($related)) return;
		$ret[] = '<dl class="list-inline">';
		$ret[] = '<dt>'. $_LANG['skin']['related'] . '</dt>';
		foreach ($related as $page=>$time){
			$wiki = Factory::Wiki($page);
			$ret[] = '<dd><a href="' . $wiki->uri('read') . '" title="' . $page . '">'. /* $wiki->title() 重い*/ $page. '</a><small>'. $wiki->passage(false, true) .'</small></dd>';
		}
		$ret[] = '</dl>';
		return join("\n", $ret);
	}
	/**
	 * 階層リストを出力
	 * return string
	 */
	private function getBreadcrumbs(){
		global $defaultpage, $vars;	// TODO
		if ($this->page === $defaultpage || $this->page === null){
			// トップページでは階層リストを表示しない
			return;
		}
		$links = self::getLinkSet();
		$parts = explode('/', $this->page);
		while (! empty($parts)) {
			$_landing = join('/', $parts);
			$wiki = Factory::Wiki($_landing);
			$element = htmlsc(array_pop($parts));
			if ($this->page === $_landing) {
				// This page ($_landing == $page)
				if ($vars['cmd'] === 'read'){
					$ret[] = '<li class="active">' . $element . '</li>';
				}else{
					$ret[] = '<li class="active">' . $this->title . '</li>';
					// 元のページへのリンク
					$ret[] = '<li><a href="' . $wiki->uri() . '" title="' . $_landing . ' ' . $wiki->passage(false) . '">' .$element . '</a></li>';
				}
			// } else if (PKWK_READONLY && ! is_page($_landing)) {
			} else if (Auth::check_role('readonly') && ! $wiki->isReadable()) {
				// Page not exists
				$ret[] = '<li class="disabled">'. $element . '</li>';
			} else {
				// Page exists or not exists
				$ret[] = '<li><a href="' . $wiki->uri() . '" title="' . $_landing . ' ' . $wiki->passage(false) . '">' .$element . '</a></li>';
			}
		}
		$ret[] = '<li><a href="' . $links['top'] . '"><span class="fa fa-home"></span></a></li>';
		return '<ol class="breadcrumb">' . join("\n", array_reverse( $ret)) .'</ol>';
	}
}