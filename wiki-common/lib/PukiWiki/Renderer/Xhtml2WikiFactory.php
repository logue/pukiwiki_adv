<?php
namespace PukiWiki\Renderer;

class Xhtml2WikiFactory{
	public static function factory($source)
	{
		// 変換クラスのオブジェクト生成とその設定
		$obj = new XHTML2Wiki();
		// 変換メソッドの呼び出し
		$body = $obj->Convert($source);
		return $body;
	}
}