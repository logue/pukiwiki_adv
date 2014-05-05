<?php
/**
 * ビュークラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: View.php,v 1.0.0 2014/01/30 14:49:00 Logue Exp $
 */
namespace PukiWiki\Renderer;

use PukiWiki\Time;
use PukiWiki\Render;

/**
 * ビュークラス
 */
class View{
	/**
	 * カラムなし（本文のみ）
	 */
	const CLASS_NO_COLUMS = 'no-colums';
	/**
	 * ２カラム（本文＋メニューバー）
	 */
	const CLASS_TWO_COLUMS = 'two-colums';
	/**
	 * ３カラム（本文＋メニューバー＋サイドバー）
	 */
	const CLASS_THREE_COLUMS = 'three-colums';
	/**
	 * 内部変数
	 */
	private $_vars;
	/**
	 * テーマ
	 */
	private $_theme;
	/**
	 * デフォルト設定
	 */
	public $conf = array(
		/**
		 * Bootswatchのテーマ
		 */
		'bootswatch'     => false,
		/**
		 * jQuery UIのテーマ
		 */
		'ui_theme'      => 'redmond',
		/**
		 * アドレスの代わりにパスを表示
		 */
		'topicpath'     => true,
		/**
		 * ナビバープラグインでもアイコンを表示する
		 */
		'showicon'      => false,
		/**
		 * 標準スタイルシートを読み込む
		 */
		'default_css'   => true,
		/**
		 * ナビバーの項目
		 */
		'navibar'       => 'top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,login',
		/**
		 * ツールバーの項目
		 */
		'toolbar'       => 'reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss',
		/**
		 * 追加で読み込むJavaScript
		 */
		'js'            => array()
	);
	public $title, $body, $meta;
	/**
	 * コンストラクタ
	 * @param file スキンファイル
	 */
	function __construct($theme = ''){
		$this->_vars = array();
		$this->_theme = !IS_MOBILE ? $theme : 'mobile';
		$this->colums = self::CLASS_NO_COLUMS;
		// テーマディレクトリへの相対パス
		$this->path =  SKIN_URI . THEME_PLUS_NAME . $this->_theme . '/';
		$this->conf = array_merge($this->conf, self::loader('ini'));
	}
	/**
	 * 値のセット
	 * @param string $key
	 * @param string or array $value
	 */
	public function __set( $key , $value ){
		$this->_vars[$key] = $value;
	}
	/**
	 * 値の取得
	 * @param string $key
	 * @return string or array
	 */
	public function __get( $key ){
		return $this->_vars[$key];
	}
	/**
	 * 値が存在するか
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key){
		return isset($this->_vars[$key]);
	}
	/**
	 * 値を削除
	 * @param string $key
	 * @return void
	 */
	public function __unset($key){
		unset($this->_vars[$key]);
	}
	/**
	 * レンダリング
	 */
	public function __toString()
	{
		if (empty($this->path)) $this->_theme = '';
		// 出力するHTMLをバッファに書き込み出力
		ob_start(! DEBUG ? 'ob_gzhandler': null);
		self::loader('skin');
		return ob_get_clean();
	}
	/**
	 * ブロック型プラグインを実行
	 * @param string $name プラグイン名
	 * @param string $args プラグインに渡す引数
	 * @return string
	 */
	public function pluginBlock($name, $args = ''){
		return PluginRenderer::executePluginBlock($name, $args);
	}
	/**
	 * インライン型プラグインを実行
	 * @param string $name プラグイン名
	 * @param string $args プラグインに渡す引数
	 * @return string
	 */
	public function pluginInline($name, $args = ''){
		return PluginRenderer::executePluginInline($name, $args);
	}
	/**
	 * プラグインのリンクを返す
	 * @param string $name プラグイン名
	 * @return string
	 */
	public function pluginLink($name, $query = array(), $fragment = ''){
		return Router::get_resolve_uri($name, $this->page, 'rel', $query, $fragment);
	}
	/**
	 * テーマと設定を取得
	 * @param string $skin_name スキン名
	 * @return array
	 */
	private function loader($type = 'skin'){
		if ($type === 'skin' && empty($this->_theme)){
			// テーマが指定されてない場合や、スキンが見つからない場合scaffoldを出力
			$html[] = '<!doctype html>';
			$html[] = '<html>';
			$html[] = '<head>';
			$html[] = '<meta charset="utf-8">';
			$html[] = $this->meta;
			$html[] = '<link rel="stylesheet" href="//' . Render::BOOTSTRAP_CDN . '/bootstrap/' . Render::TWITTER_BOOTSTRAP_VER . '/css/bootstrap.min.css" type="text/css" />';
			$html[] = '<title>' . $this->title . '</title>';
			$html[] = '</head>';
			$html[] = '<body>';
			$html[] = $this->body;
			$html[] = '</body>';
			$html[] = '</html>';
			return join("\n",$html);
		}
	
		$cond = array(
			SKIN_DIR . THEME_PLUS_NAME . $this->_theme . '/',
			EXT_SKIN_DIR . THEME_PLUS_NAME . $this->_theme . '/'
		);

		$file = basepagename($this->_theme) . '.' . $type . '.php';

		foreach($cond as $dir){
			if (file_exists($dir.$file) && is_readable($dir.$file)){
				// スキンを読み込み;
				return include $dir . $file;
			}
		}
		
		return array();
	}
}

