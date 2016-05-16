<?php
/**
 * MeCab (和布蕪）ラッパークラス
 *
 * @package   PukiWiki\Text
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/02/13
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: MeCab.php,v 1.0.1 2014/03/25 16:52:00 Logue Exp $
 **/
namespace PukiWiki\Text;

use MeCab_Tagger;

/**
 * MeCabラッパークラス
 */
class MeCab{
	public $usable = false;
	public function __construct($mecab_path = ''){
		if (!extension_loaded('mecab')) {
			if (!empty($mecab_path) && open_basedir($mecab_path) && file_exists($mecab_path)){
				$this->mecab_path = $mecab_path;
				$this->usable = true;
				//throw new Exception('Mecab is not found or not executable. Please check mecab path: '.$mecab_path);
			}
		}else{
			// PHPのMeCabモジュールが使えるとはマニアですなぁ
			$this->usable = true;
		}
	}
	/**
	 * MeCabのバイナリを直接叩く
	 * @param type $switch
	 * @param type $str
	 * @return boolean
	 */
	private function stdio($switch, $str){
		$pipes = array();
		$result = $error = '';
		$descriptorspec = array (
			0 => array('pipe', 'r'), // stdin
			1 => array('pipe', 'w'), // stdout
			2 => array('pipe', 'w')
		);

		$cmd = $this->mecab_path. isset($switch) ? ' '.$switch : '';
		$process = proc_open($cmd, $descriptorspec, $pipes, null, null);
		if (!is_resource($process)) return false;

		fwrite($pipes[0], $str);
		fclose($pipes[0]);

		$lines = array();
		while ($line = fgets($pipes[1])) $lines[] = rtrim($line);
		fclose($pipes[1]);

		fwrite($pipes[2], $error);
		fclose($pipes[2]);

		$status = proc_close($process);

		return join("\n",$lines);
	}
	/**
	 * パース（未使用）
	 * @param type $input 入力文字列
	 * @return object
	 */
	public function parse($input){
		if (!extension_loaded('mecab')) {
			$result = $this->stdio('',$input);
		}else{
			$mecab = new MeCab_Tagger();
			$result = $mecab->parse($input);
		}
		// 出力フォーマット：表層形\t品詞, 品詞細分類1, 品詞細分類2, 品詞細分類3, 活用形, 活用型, 原形, 読み, 発音
		$lines = explode("\n", $result);
		foreach($lines as $line){
			if(in_array(trim($line), array('EOS', ''))){
				continue;
			}
			$s = explode("\t", $line);
			$surface = $s[0];
			$info = explode(',', $s[1]);

			$analisys[] = array(
				'surface'       => $surface,							// 表層形
				'class'         => $info[0],							// 品詞
				'detail1'       => $info[1] !== '*' ? $info[1] : null,	// 品詞細分類1
				'detail2'       => $info[2] !== '*' ? $info[2] : null,	// 品詞細分類2
				'detail3'       => $info[3] !== '*' ? $info[3] : null,	// 品詞細分類3
				'inflections'   => $info[4] !== '*' ? $info[4] : null,	// 活用形
				'conjugation'   => $info[5] !== '*' ? $info[5] : null,	// 活用型
				'origin'        => $info[6] !== '*' ? $info[6] : null,	// 原形
			);
		}
		return $analisys;
	}
	/**
	 * 分かち書きをする
	 * @param string $input
	 * @return string
	 */
	public function wakati($input){
		if (extension_loaded('mecab')) {
			$mecab = new MeCab_Tagger();
			return $mecab->split($input);
		}
		return $this->stdio('-O wakati', $input);
	}
	/**
	 * 読みを取得する
	 * @param string $input
	 * @return string
	 */
	public function reading($input){
		if (extension_loaded('mecab')) {
			$mecab = new MeCab_Tagger();
			return $mecab->split($input);
		}
		return $this->stdio('-Oyomi', $input);
	}
}

/* End of file MeCab.php */
/* Location: /vendor/PukiWiki/Lib/Text/MeCab.php */