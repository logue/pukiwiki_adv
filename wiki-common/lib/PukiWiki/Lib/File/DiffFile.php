<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: DiffFile.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//

namespace PukiWiki\Lib\File;
use PukiWiki\Lib\File\File;
use PukiWiki\Lib\Diff;
use PukiWiki\Lib\Router;

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
	public function set($postdata){
		global $notify, $notify_diff_only;
		// 差分を作成
		$diff = new Diff(FileFactory::Wiki($this->page)->source(true), explode("\n",$postdata));
		$str = $diff->getDiff();

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
		parent::set($str);
	}
}

/* End of file DiffFile.php */
/* Location: /vender/PukiWiki/Lib/File/DiffFile.php */
