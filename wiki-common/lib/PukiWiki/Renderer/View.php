<?php
namespace PukiWiki\Renderer;
use PukiWiki\Time;
use Exception;

/**
 * ビュークラス
 */
class View{
	private $_vars, $_theme;
	public $conf, $body, $title;
	/**
	 * コンストラクタ
	 * @param file スキンファイル
	 */
	function __construct($theme = ''){
		$this->_vars = array();
		$this->_theme = !IS_MOBILE ? $theme : 'mobile';
		
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
	 * 値が存在するか
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key){
		return isset($this->_vars[$key]);
	}
	/**
	 * レンダリング
	 */
	public function render()
	{
		// 出力するHTMLをバッファに書き込み出力
		ob_start('ob_gzhandler');
		$this->proc_time = 
		self::loader('skin');
		return ob_get_clean();
	}
	/**
	 * 処理時間
	 * @return string
	 */
	public function getTakeTime(){
		return Time::getTakeTime();
	}
	/**
	 * テーマと設定を取得
	 * @param string $skin_name スキン名
	 */
	private function loader($type = 'skin'){
		global $site_title;
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
		if ($type === 'skin'){
			// テーマが指定されてない場合scaffoldを出力
			$html = array();
			$html[] = '<!doctype html>';
			$html[] = '<html>';
			$html[] = '<head>';
			$html[] = '<meta charset="utf-8">';
			$html[] = $this->meta;
			$html[] = '<link rel="stylesheet" href="http://code.jquery.com/ui/' . JQUERY_UI_VER . '/themes/' . $this->conf->ui_theme . '/jquery-ui.min.css" type="text/css" />';
			$html[] = '<title>' . $this->title . ' - ' . $site_title . '</title>';
			$html[] = '</head>';
			$html[] = '<body>';
			$html[] = $this->body;
			$html[] = '</body>';
			$html[] = '</html>';
			return join("\n",$html);
		}
		return array();
	}
}

