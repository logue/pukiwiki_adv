<?php
/**
 * シングルトン
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2014 PukiWiki Advance Developers Team
 * @create    2014/06/15
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Render.php,v 1.0.0 2014/06/15 16:46:00 Logue Exp $
 */
namespace PukiWiki;

/**
 * シングルトン
 */
trait Singleton
{
	/**
	 * インスタンス
	 */
	protected static $instance;
	/**
	 * インスタンス生成
	 */
	final public static function getInstance()
	{
		return isset(static::$instance)
			? static::$instance
			: static::$instance = new static;
	}
	/**
	 * コンストラクタ
	 */
	final private function __construct() {
		static::init();
	}
	/**
	 * 初期化
	 */
	protected function init() {}
	/**
	 * 許可しないマジックメソッド
	 */
	final private function __wakeup() {
		throw new RuntimeException("Singleton: You can't wakeup this instance.");
	}
	final private function __clone() {
		throw new RuntimeException("Singleton: You can't clone this instance.");
	}
}
