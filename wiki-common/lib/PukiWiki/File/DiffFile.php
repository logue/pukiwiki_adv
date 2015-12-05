<?php
/**
 * 差分ファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: DiffFile.php,v 1.0.0 2013/01/10 17:28:00 Logue Exp $
 **/

namespace PukiWiki\File;

use Exception;
use PukiWiki\File\AbstractFile;
use PukiWiki\Mailer;
use PukiWiki\Router;
use PukiWiki\Utility;

/**
 * 差分ファイルクラス
 */
class DiffFile extends AbstractFile{
	public static $dir = DIFF_DIR;
	public static $pattern = '/^((?:[0-9A-F]{2})+)\.txt$/';

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {
		if (empty($page)){
			throw new Exception('DiffFile::__construct(): Page name is missing!');
		}
		$this->page = $page;
		parent::__construct(self::$dir . Utility::encode($page) . '.txt');
	}
	/**
	 * 書き込み
	 * @global boolean $notify
	 * @global boolean $notify_diff_only
	 * @param string $str
	 */
	public function set($diffdata = '', $keeptimestamp = false){
		global $notify, $notify_diff_only, $notify_subject;
		// 差分を作成
		//$diff = new Diff(WikiFactory::Wiki($this->page)->source(true), explode("\n",$postdata));
		//$str = $diff->getDiff();

		if ($notify){
			$str = ($notify_diff_only) ? preg_replace('/^[^-+].*\n/m', '', $diffdata) : $diffdata;
			$summary = array(
				'ACTION'		=> 'Page update',
				'PAGE'			=> & $page,
				'URI'			=> Router::get_script_uri() . '?' . rawurlencode($page),
				'USER_AGENT'	=> TRUE,
				'REMOTE_ADDR'	=> TRUE
			);
			Mailer::notify($notify_subject, $str, $summary) or
				Utility::dieMessage('Mailer::notify(): Failed');
		}
		parent::set($diffdata);
	}
	/**
	 * 差分をHTMLにして出力
	 * @return string
	 */
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
		return '<pre class="sh sunlight-highlight-diff">'."\n".join("\n",$ret).'</pre>';
	}
}

/* End of file DiffFile.php */
/* Location: /vender/PukiWiki/Lib/File/DiffFile.php */
