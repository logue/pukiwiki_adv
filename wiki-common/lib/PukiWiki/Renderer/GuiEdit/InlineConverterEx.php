<?php
namespace PukiWiki\Renderer\GuiEdit;

use PukiWiki\Utility;
use PukiWiki\Renderer\PluginRenderer;

/**
 * GuiEdit用インライン変換クラス
 */
class InlineConverterEx {
	public static function convert($line, $link = TRUE, $enc = TRUE) {
		if ($enc) {
			$line = preg_replace("/&amp;/", "&#038;", $line);
			$line = Utility::htmlsc($line);
		}

		// インライン・プラグイン
		$pattern = '/&amp;(\w+)(?:\(((?:(?!\)[;{]).)*)\))?(?:\{((?:(?R)|(?!};).)*)\})?;/';
		$line = preg_replace_callback($pattern, array(&$this, 'convert_plugin'), $line);
		
		// ルールの変換
		$line = guiedit_make_line_rules($line);
		
		// 文字サイズの変換
		$pattern = "/<span\s(style=\"font-size:(\d+)px|class=\"size([1-7])).*?>/";
		$line = preg_replace_callback($pattern, array(&$this, 'convert_size'), $line);
		// 色の変換
		$pattern = "/<sapn\sstyle=\"color:([#0-9a-z]+)(; background-color:([#0-9a-z]+))?\">/";
		$line = preg_replace_callback($pattern, array(&$this, 'convert_color'), $line);
		// 注釈
		$line = preg_replace("/\(\(((?:(?R)|(?!\)\)).)*)\)\)/", "<img alt=\"Note\" title=\"$1\" />", $line);
		// 参照文字
		$line = preg_replace('/&amp;(#?[a-z0-9]+);/', "&$1;", $line);

		// 上付き文字
		$line = preg_replace('/SUP{(.*?)}/', "<sup>$1</sup>", $line);
		// 下付き文字・添え字 
		$line = preg_replace('/SUB{(.*?)}/', "<sub>$1</sub>", $line);
		
		// リンク
		if ($link) {
			$pattern = "/\(\(((?:(?R)|(?!\)\)).)*)\)\)/";
			$replace = "<img alt=\"Note\" title=\"$1\" />";
			$line = $this->make_link($line);
		}
		
		if (preg_match("/^<br\s\/>$/", $line)) {
			$line .= "\n&nbsp;";
		}

		return $line;
	}

	/**
	 * 文からリンクを検出し、link_replace を呼び出す
	 */
	static function make_link($line) {
		$link_rules = "/(
			(?:\[\[((?:(?!\]\]).)+):)? 
			((?:https?|ftp|news)(?::\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+))
			(?(2)\]\])
			|
			 (\[\[
			  (?:
			   (?:((?:(?!\]\]).)+))
			   (?:&gt;)
			  )?
			  (?:
			   (\#(?:[a-zA-Z][\w-]*)?)
			   |
			   ((?:(?!\]\]).)*)
			  )?
			 \]\])
		)/x";

		return preg_replace_callback($link_rules, array(&$this,'link_replace'), $line);
	}

	/**
	 * make_link で検出したリンクにリンクタグを付加する
	 */
	static function link_replace($matches) {
		if ($matches[3] != '') {
			if (!$matches[2]) {
				return $matches[3];
			}
			$url = $matches[3];
			$alias = empty($matches[2]) ? $url : $matches[2];
			return '<a href="'.$url.'">'.$alias.'</a>';
		}
		if ($matches[6] != '') {
			$str = empty($matches[5]) ? $matches[6] : $matches[5];
			return '<a href="' . $matches[6] . '">' . $str.'</a>';
		}
		if ($matches[7] != '') {
			$str = empty($matches[5]) ? $matches[7] : $matches[5];
			return '<a href="' . $matches[7] . '">' . $str . '</a>';
		}
		return $matches[0];
	}
	
	/**
	 * インラインプラグイン処理メソッド
	 */
	static function convert_plugin($matches) {
		$aryargs = (!empty($matches[2])) ? explode(',', $matches[2]) : array();
		$name = strtolower($matches[1]);
		$body = empty($matches[3]) ? '' : $matches[3];
		
		//	プラグインが存在しない場合はそのまま返す。
		// if (!file_exists(PLUGIN_DIR . $name . '.inc.php')) {
		if (!PluginRenderer::hasPlugin($name)) {
			return $matches[0];
		}

		switch ($name) {
			case 'aname':
				return '<a name="'.$aryargs[0].'">'.$body.'</a>';
			case 'br':
				return '<br />';
			case 'color':
				$color = $aryargs[0];
				$bgcolor = $aryargs[1];
				if ($body == '')
					return '';
				if ($color != '' && !preg_match('/^(#[0-9a-f]+|[\w-]+)$/i', $color))
					return $body;
				if ($bgcolor != '' && !preg_match('/^(#[0-9a-f]+|[\w-]+)$/i', $bgcolor))
					return $body;
				if ($color != '')
					$color = 'color:'.$color;
				if ($bgcolor != '')
					$bgcolor = ($color ? '; ' : '') . 'background-color:'.$bgcolor;
				return '<span style="'.$color.$bgcolor.'">' . $this->convert($body, TRUE, FALSE) . '</span>';

			case 'sup':
			case 'sub':
				return '<'.$name.'>'.$body.'</'.$name.'>';

			case 'size':
				$size = $aryargs[0];
				if ($size == '' || $body == '')
					return '';
				if (!preg_match('/^\d+$/', $size))
					return $body;
				return '<span style="font-size:' . $size . 'px;line-height:130%">' . 
				       $this->convert($body, TRUE, FALSE) . "</span>";
			case 'ref':
				return guiedit_convert_ref($aryargs, FALSE);
		}
		
		if ($body) {
			$pattern = array("%%", "''", "[[", "]]", "{", "|", "}");
			$replace = array("&#037;&#037;", "&#039;&#039;", "&#091;&#091;",
		 	 	"&#093;&#093;", "&#123;", "&#124;", "&#125;");
			$body = str_replace($pattern, $replace, $body);
		}
		
		$inner = '&' . $matches[1] . ($matches[2] ? '('.$matches[2].')' : '') . ($body ? '{'.$body.'}' : '') . ';';
		$style = (UA_NAME == MSIE) ? '' : ' style="cursor:default"';
		
		return '<span class="plugin text-primary" contenteditable="false"'.$style.'>'.$inner.'</span>';
	}
	
	/**
	 * 色の変換
	 */
	static function convert_color($matches) {
		$color = $matches[1];
		$bgcolor = $matches[3];
		if ($bgcolor && preg_match("/^#[0-9a-zA-Z]{3}$/i", $bgcolor)) {
			$bgcolor = "; background-color:" . preg_replace('/[0-9a-fA-F]/i', "$0$0", $bgcolor);
		}
		if (preg_match("/^#[0-9a-zA-Z]{3}$/i", $color)) {
			$color = preg_replace('/[0-9a-fA-F]/i', "$0$0", $color);
		}
		
		// return "<sapn\sstyle=\"color:$color$bgcolor\">";
		// UPK
		return '<sapn style="color:'.$color.$bgcolor.'">';
	}

	/** 
	 * 文字サイズの変換
	 */
	static function convert_size($matches) {
		if ($matches[2]) {
			$size = $matches[2];
			
			if      ($size <=  8) $size = 8;
			else if ($size <=  9) $size = 9;
			else if ($size <= 10) $size = 10;
			else if ($size <= 11) $size = 11;
			else if ($size <= 12) $size = 12;
			else if ($size <= 14) $size = 14;
			else if ($size <= 16) $size = 16;
			else if ($size <= 18) $size = 18;
			else if ($size <= 22) $size = 20;
			else if ($size <= 26) $size = 24;
			else if ($size <= 30) $size = 28;
			else if ($size <= 36) $size = 32;
			else if ($size <= 44) $size = 40;
			else if ($size <= 52) $size = 48;
			else		      $size = 60;
			
			return '<span style="font-size:' . $size . 'px; line-height:130%">';
		}
		
		switch ($matches[3]) {
			case 1:	$size = 'xx-small';
			case 2: $size = 'x-small';
			case 3:	$size = 'small';
			case 4:	$size = 'medium';
			case 5:	$size = 'large';
			case 6:	$size = 'x-large';
			case 7:	$size = 'xx-large';
		}
		
		return '<span style="font-size:'.$size.';">';
	}
}
