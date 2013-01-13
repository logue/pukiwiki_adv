<?php
/**
 * 差分ファイルクラス
 *
 * @package   PukiWiki\Lib\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: DiffFile.php,v 1.0.0 2013/01/10 17:28:00 Logue Exp $
 **/
namespace PukiWiki\Lib\File;
use PukiWiki\Lib\Diff;
use PukiWiki\Lib\Router;
use PukiWiki\Lib\Utility;

class DiffFile extends File{
	/**#@+
	 * 宣言
	 */
	// 拡張子
	const EXT = '.txt';
	// 格納ディレクトリ
	const DIR = DIFF_DIR;
	/**#@-*/

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {
		if (empty($page)){
			throw new Exception('Page name is missing!');
		}
		$this->page = $page;
		parent::__construct(self::DIR . encode($page) . self::EXT);
	}
	/**
	 * 書き込み
	 * @global boolean $notify
	 * @global boolean $notify_diff_only
	 * @param string $str
	 */
	public function set($diffdata){
		global $notify, $notify_diff_only;
		// 差分を作成
		//$diff = new Diff(FileFactory::Wiki($this->page)->source(true), explode("\n",$postdata));
		//$str = $diff->getDiff();

		if ($notify){
			if ($notify_diff_only) $str = preg_replace('/^[^-+].*\n/m', '', $str);
			$summary = array(
				'ACTION'		=> 'Page update',
				'PAGE'			=> & $page,
				'URI'			=> Router::get_script_uri() . '?' . rawurlencode($page),
				'USER_AGENT'	=> TRUE,
				'REMOTE_ADDR'	=> TRUE
			);
			pkwk_mail_notify($notify_subject, $str, $summary) or
				die_message('pkwk_mail_notify(): Failed');
		}
		parent::set($diffdata);
	}
	public function render(){
		foreach (self::get() as $line){
			// 先頭の１文字だけを抜き出す
			$str = Utility::htmlsc(substr($line, 1));
			if ($str === '') {
				// 空行
				$ret[] = ' ';
				continue;
			}
			
			switch (substr($line, 0, 1)) {
				case '+' :
					$ret[] = '+<ins class="diff_added">' . $str . '</ins>';
					break;
				case '-':
					$ret[] = '-<del class="diff_removed">' . $str . '</del>';
					break;
				case '/' :
					$ret[] = '/'.$str;
					break;
				default:
					$ret[] = ' '.$str;
					break;
			}
		}
		return '<pre class="sh" data-brush="diff">'."\n".join("\n",$ret).'</pre>';
	}
}

/* End of file DiffFile.php */
/* Location: /vender/PukiWiki/Lib/File/DiffFile.php */
