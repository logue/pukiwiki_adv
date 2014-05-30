<?php
/**
 * ZendSearch（Lucene）による検索処理
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/05/23
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Search.php,v 1.0.3 2014/03/10 19:24:00 Logue Exp $
 */
namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;
use Igo\Tagger;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document\Html as LuceneDocHtml;
/**
 * 検索クラス
 */
class SearchLucene extends Search{
	/**
	 * 検索インデックス名
	 */
	const INDEX_NAME = 'search-index';
	/**
	 * インデックスファイルを生成
	 */
	public static function updateIndex(){
		static $igo;
		
		if (empty($igo)){
			$igo = new Tagger('../ipadic', 'reduce_mode'  => true);
		}
		// 索引の作成
		$index = Lucene::create(CACHE_DIR . self::INDEX_NAME);
		foreach (Listing::pages() as $page) {
			
			if (empty($page)) continue;

			$wiki = Factory::Wiki($page);

			// 読む権限がない場合スキップ
			if (!$wiki->isReadable() || $wiki->isHidden()) continue;
			
			// HTML出力
			$html[] = '<html><head>';
			$html[] = '<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>';
			$html[] = '<title>' . $wiki->title() . '</title>';
			$html[] = '</head>';
			// HTMLをテキストに変換して分かち書きしたものをbodyとする。
			$html[] = '<body>' . $igo->wakati(strip_tags($wiki->render)) . '</body>';
			$html[] = '</html>';
			// HTMLの解析
			$doc = LuceneDocHtml::loadHTML(join("\n", $html), false);
			
			// 索引へ文書の登録
			$index->addDocument($doc);
		}
		
		//$hits = $index->find('hoge');
		//var_dump($hits);
	}
	/**
	 * 検索メイン処理
	 * @param string $word 検索ワード
	 * @param string $type 検索方法（and, or）
	 * @param boolean $non_format
	 * @param string $base ベースとなるページ
	 * @return string
	 */
	public static function do_search($word, $type = 'and', $non_format = FALSE, $base = ''){
		// インデックスファイルを開く
		$index = Lucene::open(CACHE_DIR . self::INDEX_NAME);
		
		// 検索クエリをパース
		$query = \ZendSearch\Lucene\Search\Query\Boolean();
		$keys = parent::get_search_words(preg_split('/\s+/', $word, -1, PREG_SPLIT_NO_EMPTY));
		// Luceneに渡す
		foreach ($keys as $key=>$value)
			$query->addSubquery( new \ZendSearch\Lucene\Index\Term($value), true);
		
		//  検索と実行
		$hits = $index->find($query);
		var_dump($hits);
		
		if ($non_format){
			//
		}
		return $hits;
	}
}