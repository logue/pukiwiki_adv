<?php
namespace PukiWiki\Renderer;

use Exception;
/**
 * ビュークラス
 */
class View{
	private $_vars, $_theme;
	public $conf;
	/**
	 * コンストラクタ
	 * @param file スキンファイル
	 */
	function __construct($theme){
		$this->_vars = array();
		$this->_theme = $theme;
		// テーマ設定を読み込む
		$this->conf = self::loader('ini');
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
	 * レンダリング
	 */
	public function render()
	{
		ob_start('ob_gzhandler');
		self::loader('skin');
		return ob_get_clean();
	}
	/**
	 * テーマと設定を取得
	 * @param string $skin_name スキン名
	 */
	private function loader($type = 'skin'){
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

		if ($type == 'skin') throw new Exception('Skin File:'.$this->_theme.' is not found or not readable.');
		return array();
	}
}

