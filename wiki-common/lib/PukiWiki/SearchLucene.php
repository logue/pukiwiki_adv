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
 * @version   $Id: SearchLucene.php,v 1.0.3 2014/03/10 19:24:00 Logue Exp $
 */
namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;
use Igo\Tagger;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document as LuceneDoc;
use ZendSearch\Lucene\Document\Html as LuceneDocHtml;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\QueryParser;
use ZendSearch\Lucene\Search\Query\Boolean as QueryBoolean;
/**
 * 検索クラス
 */
class SearchLucene extends Search{
	/**
	 * 検索インデックス名
	 */
	const INDEX_NAME = 'lucene';
	
	protected static $igo;
	/**
	 * インデックスファイルを生成
	 */
	public static function updateIndex(){
		
		if (empty(self::$igo)){
			self::$igo = new Tagger(array('dict_dir'=>LIB_DIR.'ipadic', 'reduce_mode'  => true));
		}
		
		Analyzer::setDefault(new Utf8());

		// 索引の作成
		$index = Lucene::create(CACHE_DIR . self::INDEX_NAME);
		
		foreach (Listing::pages() as $page) {
			if (empty($page)) continue;

			$wiki = Factory::Wiki($page);

			// 読む権限がない場合スキップ
			if (!$wiki->isReadable() || $wiki->isHidden()) continue;
/*
			// HTML出力
			$html[] = '<html><head>';
			$html[] = '<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>';
			$html[] = '<title>' . $wiki->title() . '</title>';
			$html[] = '</head>';
			$html[] = '<body>' . $wiki->render() . '</body>';
			$html[] = '</html>';
*/
			$doc = new LuceneDoc();
			
			$doc->addField(Field::Text('title', $wiki->title()));

			// Store document URL to identify it in the search results
			$doc->addField(Field::Text('url', $wiki->uri()));
			 
			// Index document contents
			//$contents = join(" ", self::$igo->wakati(strip_tags($wiki->render())));
			$contents = strip_tags($wiki->render());
			$doc->addField(Field::UnStored('contents', $contents ));

			// 索引へ文書の登録
			$index->addDocument($doc);
		}
		$index->optimize();
		
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
		$query = QueryBoolean();
		$keys = QueryParser;;parse($word);
		// Luceneに渡す
		$query->addSubquery($userQuery, true  /* required */);
		$query->addSubquery($constructedQuery, true  /* required */);
		foreach ($keys as $key=>$value)
			$query->addSubquery( new Term($value), true);
		
		//  検索と実行
		$hits = $index->find($query);
		var_dump($hits);
		
		if ($non_format){
			//
		}
		return $hits;
	}
}