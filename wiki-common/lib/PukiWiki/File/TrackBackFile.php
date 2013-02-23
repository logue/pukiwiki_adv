<?php
/**
 * TrackBackファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/30
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: TrackBackFile.php,v 1.0.0 2013/01/30 19:54:00 Logue Exp $
 */

namespace PukiWiki\File;

use PukiWiki\File\File;
use PukiWiki\Factory;
use PukiWiki\File\FileUtility;
use PukiWiki\Utility;
use PukiWiki\Diff;

class TrackBackFile extends File{
	/**#@+
	 * 宣言
	 */
	// 拡張子
	const EXT = '.txt';
	// 格納ディレクトリ
	const DIR = TRACKBACK_DIR;
	// ファイル名のパターン
	const FILENAME_PATTERN = '/^((?:[0-9A-F]{2})+).txt$/';
	/**#@-*/

	private $page, $id;

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {
		if (empty($page)){
			throw new \Exception('Page name is missing!');
		}
		$this->page = $page;
		$this->id = md5($page);
		parent::__construct(self::DIR . encode($page) . self::EXT);
	}
	/**
	 * TrackBack Ping IDを取得
	 * @return string
	 */
	public function get_id(){
		return $this->id;
	}
	/**
	 * TrackBackの件数を取得
	 * @return int
	 */
	public function count(){
		return count(self::get(false));
	}
	/**
	 * TrackBackを送信
	 * @param array $links 送信先
	 */
	public function send($links){
		global $trackback, $page_title, $log;
		$script = Router::get_script_uri();

		// No link, END
		if (! is_array($links) || empty($links)) return;

		$wiki = Factory::Wiki($this->page);

		// PROHIBITION OF INVALID TRANSMISSION
		$url = parse_url($script);
		$host = (empty($url['host'])) ? $script : $url['host'];
		if (is_ipaddr($host)) {
			if (is_localIP($host)) return;
		} else {
			if (is_ReservedTLD($host)) return;
		}
		if (is_ignore_page($page)) return;

		// Disable 'max execution time' (php.ini: max_execution_time)
		if (ini_get('safe_mode') == '0') set_time_limit(0);

		$excerpt = strip_htmltag($wiki->render());

		// Sender's information
		$putdata = array(
			'title'     => $this->page, // Title = It's page name
			'url'       => $wiki->get_uri(),
			'excerpt'   => mb_strimwidth(preg_replace("/[\r\n]/", ' ', $excerpt), 0, 255, '...'),
			'blog_name' => $page_title . ' (' . self::TRACKBACK_VERSION . ')',
			'charset'   => SOURCE_ENCODING // Ping text encoding (Not defined)
		);


		foreach ($links as $link) {
			if (path_check($script, $link)) continue; // Same Site
			$tb_url = tb_get_url($link);  // Get Trackback ID from the URL
			if (empty($tb_url)) continue; // Trackback is not supported

			$client = new Client($tb_url);
			$client->setParameterPost($putdata);

			//$result = pkwk_http_request($tb_id, 'POST', '', $putdata, 2, CONTENT_CHARSET);
			// FIXME: Create warning notification space at pukiwiki.skin!

			$log[] = $client->request("POST");
		}
	}
	public function get(){
		$this->setFlags(SplFileObject::READ_CSV);
		foreach ($file as $row) {
			list($animal, $class, $legs) = $row;
			printf("A %s is a %s with %d legs\n", $animal, $class, $legs);
		}
		