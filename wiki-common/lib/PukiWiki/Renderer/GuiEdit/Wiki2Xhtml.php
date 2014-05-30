<?php
namespace PukiWiki\Renderer\GuiEdit;

use PukiWiki\Utility;

class Wiki2Xml{
	// 添付ファイルプラグインの変換
	function convertRef($args, $div = TRUE) {
		$options = Utility::htmlsc(join(',', $args));

		$filename = array_shift($args);
		$_title = array();
		$params = array(
			'left'   => 0, // 左寄せ
			'center' => 0, // 中央寄せ
			'right'  => 0, // 右寄せ
			'wrap'   => 0, // TABLEで囲む
			'nowrap' => 0, // TABLEで囲まない
			'around' => 0, // 回り込み
			'noicon' => 0, // アイコンを表示しない
			'nolink' => 0, // 元ファイルへのリンクを張らない
			'noimg'  => 0, // 画像を展開しない
			'zoom'   => 0, // 縦横比を保持する
			'_w'     => 0,     // 幅
			'_h'     => 0,     // 高さ
			'_size'  => '%'
		);

		// パラメータ解析
		foreach ($args as $arg) {
			$s_arg = strtolower($arg);
			if (array_key_exists($s_arg, $params)) {
				$params[$s_arg] = 1;
			} else if (preg_match('/^([0-9]+)x([0-9]+)$/', $arg, $matches)) {
				$params['_w'] = $matches[1];
				$params['_h'] = $matches[2];
				$params['_size'] = 'px';
			} else if (preg_match('/^([0-9.]+)%$/', $arg, $matches) && $matches[1] > 0) {
				$params['_w'] = $matches[1];
			} else {
				$_title[] = $arg;
			}
		}

		$align = '';
		if ($params['left']) {
			$align = 'left';
		} else if ($params['center']) {
			$align = 'center';
		} else if ($params['right']) {
			$align = 'right';
		}

		$alt = !empty($_title) ? Utility::htmlsc(join(',', $_title)) : '';
		$alt = preg_replace("/^,/", '', $alt);

		$attribute = 'class="bg-primary" contenteditable="false"' . ((UA_NAME == MSIE) ? '' : ' style="cursor:default"');
		$attribute .= ' _filename="' . $filename . '"';
		$attribute .= ' _alt="' . $alt . '"';
		$attribute .= ' _width="' . ($params['_w'] ? $params['_w'] : '') . '"';
		$attribute .= ' _height="' . ($params['_h'] ? $params['_h'] : '') . '"';
		$attribute .= ' _size="' . $params['_size'] . '"';
		$attribute .= ' _align="' . $align . '"';
		$attribute .= ' _wrap="' . $params['wrap'] . '"';
		$attribute .= ' _around="' . $params['around'] . '"';
		$attribute .= ' _nolink="' . $params['nolink'] . '"';
		$attribute .= ' _noicon="' . $params['noicon'] . '"';
		$attribute .= ' _noimg="' . $params['noimg'] . '"';
		$attribute .= ' _zoom="' . $params['zoom'] . '"';
		
		if ($div) {
			$tags = "<div $attribute>#ref($options)</div>";
		}
		else {
			$tags = "<span $attribute>&ref($options);</span>";
		}
		
		return $tags;
	}
}
