<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: showrss.inc.php,v 1.22.9 2013/08/05 10:27:00 Logue Exp $
//  Id:showrss.inc.php,v 1.40 2003/03/18 11:52:58 hiro Exp
// Copyright (C):
//     2011-2013 PukiWiki Advance Developers Team
//     2005-2007 PukiWiki Plus! Team
//     2002-2006,2011 PukiWiki Developers Team
//     2002      PANDA <panda@arino.jp>
//     (Original)hiro_do3ob@yahoo.co.jp
// License: GPL, same as PukiWiki
//
// Show RSS (of remote site) plugin
// NOTE:
//    * This plugin needs 'PHP xml extension'
//    * Cache data will be stored as CACHE_DIR/*.tmp

use PukiWiki\Auth\Auth;
use PukiWiki\Utility;
use PukiWiki\Renderer\Header;
use Zend\Http\Response;

define('PLUGIN_SHOWRSS_USAGE', '#showrss(URI-to-RSS[,default|menubar|recent[,Cache-lifetime[,Show-timestamp]]])');
defined('PLUGIN_SHOWRSS_SHOW_DESCRIPTION') or define('PLUGIN_SHOWRSS_SHOW_DESCRIPTION', true);

define('PLUGIN_SHOWRSS_CACHE_PREFIX', 'showrss-');

// Show related extensions are found or not
function plugin_showrss_action()
{
	global $vars, $cache;
	// if (PKWK_SAFE_MODE) die_message('PKWK_SAFE_MODE prohibit this');
	if (Auth::check_role('safemode')) Utility::dieMessage('PKWK_SAFE_MODE prohibits this');

	if ($vars['feed']){
		// ajaxによる読み込み
		$target = $vars['feed'];

		list($data, $time, $reason) = plugin_showrss_get_rss($vars['feed'], 1, true);
		$header = Header::getHeaders('aplication/xml' ,$time );

		if (empty($reason)) {
			Header::writeResponse($header, Response::STATUS_CODE_200, $data);
		}else{
			// とりあえずXMLでエラー
			Header::writeResponse($header, Response::STATUS_CODE_200, '<?xml version="1.0" encoding="UTF-8"?><response><error>1</error><message>'.Utility::htmlsc($reason).'</message></response>');
		}
		exit();
	}

	$body = '';
	foreach(array('xml', 'mbstring') as $extension){
		$$extension = extension_loaded($extension) ?
			'&color(green){Found};' :
			'&color(red){Not found};';
		$body .= '| ' . $extension . ' extension | ' . $$extension . ' |' . "\n";
	}
	return array('msg' => 'showrss_info', 'body' => convert_html($body));
}

function plugin_showrss_convert()
{
	static $_xml;

	if (! isset($_xml)) $_xml = extension_loaded('xml');
	if (! $_xml) return '#showrss: xml extension is not found<br />' . "\n";

	$num = func_num_args();
	if ($num == 0) return PLUGIN_SHOWRSS_USAGE . '<br />' . "\n";

	$argv = func_get_args();
	$timestamp = FALSE;
	$cachehour = 6;
	$template = 'default';
	$uri = '';
	switch ($num) {
	case 4: $timestamp = (trim($argv[3]) == '1');	/*FALLTHROUGH*/
	case 3: $cachehour = trim($argv[2]);		/*FALLTHROUGH*/
	case 2: $template  = strtolower(trim($argv[1]));/*FALLTHROUGH*/
	case 1: $uri       = trim($argv[0]);
	}

	$class = (empty($template) || $template == 'default') ? 'ShowRSS_html' : 'ShowRSS_html_' . $template;
	if (! is_numeric($cachehour))
		return '<p class="message_box ui-state-error ui-corner-all">#showrss: Cache-lifetime seems not numeric: <var>' . htmlsc($cachehour) . '</var></p>' . "\n";
	if (! class_exists($class))
		return '<p class="message_box ui-state-error ui-corner-all">#showrss: Template not found: <var>' . htmlsc($template) . '</var></p>' . "\n";
	if (! is_url($uri))
		return '<p class="message_box ui-state-error ui-corner-all">#showrss: Seems not URI: <var>' . htmlsc($uri) . '</var></p>' . "\n";

	if (! is_requestable($uri))
		return '<p class="message_box ui-state-error ui-corner-all">#showrss: Prohibit fetching RSS from my server.</p>' . "\n";

	list($rss, $time, $reason) = plugin_showrss_get_rss($uri, $cachehour);
	if (!$rss) return '<p class="message_box ui-state-error ui-corner-all">#showrss: '.$reason.'</p>' . "\n";

	if ($timestamp > 0) {
		$time = '<p style="font-size:small; font-weight:bold; text-align:right;">Last-Modified:' .
			get_date('Y/m/d H:i:s', $time) .  '</p>';
	}
	

	$obj = new $class($rss);
	return $obj->toString($time);
}

// Get and save RSS
function plugin_showrss_get_rss($target, $cachehour = 6, $raw = false)
{
	global $cache;
	$time = UTIME;
	$xml = null;
	$cache_name = PLUGIN_SHOWRSS_CACHE_PREFIX.md5($target);
	$reason = 'Failed fetching RSS from the server.';	// デフォルトのエラー
	$recreate = false;

	if ($cache['raw']->hasItem($cache_name)) {
		$cache_meta = $cache['raw']->getMetadata($cache_name);
		$time = $cache_meta['mtime'];	// キャッシュの時刻
		if (UTIME - $time >= $cachehour * 360){
			$cache['raw']->removeItem($cache_name);
		}
	}

	if (!$cache['raw']->hasItem($cache_name)) {
		// キャッシュが有効期限を過ぎてた場合
		try{
			// file_get_contentsでException飛ばすメモ
			// https://gist.github.com/mia-0032/4374687
			ob_start();
			//warningが出るコード
			$data = file_get_contents($target);
			$warning = ob_get_contents();
			ob_end_clean();
			//Warningがあれば例外を投げる
			if ($warning) {
				throw new Exception($warning);
			}
		}catch (Exception $e){
			// ファイルが取得できなかった
			return array(false, UTIME, $e->getMessage());
		}
		$cache['raw']->setItem($cache_name, $data);
	}else{
		$data = $cache['raw']->getItem($cache_name);
	}

	// そのままXMLを出力
	if ($raw) return array($data, $time, $reason);
	
	try{
		$xml = new SimpleXMLElement($data);
	}catch (Exception $e){
		// XMLの解析に失敗した
		return array(false, UTIME, $e->getMessage());
	}
	return array($xml,$time,null);
}


function plugin_showrss_get_timestamp($str)
{
	$str = trim($str);
	if ($str == '') return UTIME;

	$matches = array();
	if (preg_match('/(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(([+-])(\d{2}):(\d{2}))?/', $str, $matches)) {
		$str = $matches[1] . ' ' . $matches[2];
		if (! empty($matches[3])) {
			$str .= $matches[4] . $matches[5] . $matches[6];
		}
	}
	$time = strtotime($str);
	return ($time == -1 || $time === FALSE) ? UTIME : $time;
}

// Create HTML from RSS array()
class ShowRSS_html
{
	var $items = array();
	var $class = 'showrss';

	function __construct($xml)
	{
		// 整形
		if ((string) $xml->attributes()->version == '2.0'){
			// RSS2.0の場合（チャンネルが複数あった場合どーするんだ？これ？）
			foreach ($xml->channel as $channels){
				$this->title =  (string) $channels->title;
				$this->subtitle = (string) $channels->description;
				$this->url = (string) $channels->link;
				$this->logo = isset($channels->image) ? '<a href="'.(string) $channels->image->link.'" title="'.(string) $channels->image->title.'" rel="external"><img src="'.(string) $channels->image->url.'" /></a>' : null;
				$this->passage =  get_passage( strtotime((string) isset($channels->lastBuildDate) ? $channels->lastBuildDate : $channels->pubDate) );
				foreach ($channels->item as $item) {
					$this->items[] = array(
						'entry'	=> (string) $item->title,
						'link'	=> (string) $item->link,
						'date'	=> strtotime((string) $item->pubDate),
						'media'	=> isset($item->enclosure) ? (string) $item->enclosure->attributes()->url : null,
						'desc'	=> (string) htmlsc($item->description)
					);
				}
			}
		}else if($xml->entry){
			// <entry>が含まれる場合は、Atomと判断する。
			$xml->registerXPathNamespace('feed', 'http://www.w3.org/2005/Atom');
			$href = '';
			foreach ($xml->link as $link) {
				$href = (string) $link->attributes()->href;
				if ($link->attributes()->type == 'text/html'){
					break;
				}
			}
			$this->title = (string) $xml->title;
			$this->subtitle = (string) $xml->subtitle;
			$this->passage =  get_passage( strtotime((string) $xml->updated) );
			$this->url = $href;
			$this->logo = (isset($xml->icon)) ? '<a href="'.$href.' rel="external"><img src="'.(string) $xml->icon.'" /></a>' : null;
			
			// atom podcastは未対応（複数指定可能ってどんだけー！？）
			// contentタグには未対応
			foreach ($xml->entry as $entry) {
				$this->items[] = array(
					'entry'	=> (string) $entry->title,
					'link'	=> (string) $entry->link->attributes()->href,
					'date'	=> strtotime((string) $entry->updated),
					'desc'	=> (string) htmlsc($entry->summary)
				);
			}
		}else{
//			$rdf = $xml->channel->items->children('http://www.w3.org/1999/02/22-rdf-syntax-ns#');	// RDF（未使用）
			$dc = $xml->channel->children('http://purl.org/dc/elements/1.1/');	// ダブリンコア
			// RSS1.xの場合
			$this->title = $xml->channel->title;
			$this->subtitle = (string) $xml->channel->description;
			$this->passage = ($dc->date) ? get_passage( strtotime((string) $dc->date) ) : '';
			$this->url = $xml->channel->link;
			$this->logo = (isset($xml->channels->image)) ? '<a href="'.$xml->channels->image->link.'" title="'.$xml->channels->image->title.'" rel="external"><img src="'.$xml->channels->image->url.'" /></a>' : null;

			foreach ($xml->item as $item) {
				$item_dc = $item->children('http://purl.org/dc/elements/1.1/');
				$this->items[] = array(
					'entry'	=> (string) $item->title,
					'link'	=> (string) $item->link,
					'date'	=> strtotime((string) $item_dc->date),
					'desc'	=> (string) Utility::htmlsc($item->description)
				);
			}
		}
	}

	// エントリの内容
	function format_line($line){
		$desc = mb_strimwidth(preg_replace("/[\r\n]/", ' ', strip_tags($line['desc'])), 0, 127, '...');
		if (IS_MOBILE){
			return '<a href="'. preg_replace("/\s/",'', $line['link']) .'" rel="external">'.$line['entry'].'<span class="ui-li-count">'.get_passage($line['date'],false).'</span></a>';
		}else{
			return '<a href="'. $line['link'] .'" title="'.$desc.' '.get_passage($line['date']).'">'.$line['entry'].'</a>';
		}
	}

	// エントリの外側
	function format_body($body){
		$retval = array();
		if (IS_MOBILE){
			$retval[] = '<div data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d"><h4>'.$this->title.'</h4>';
			
			$retval[] = '<ul data-role="listview" data-inset="true">';
			$retval[] = $body;
			$retval[] = '</ul>' . "\n".  '</div>';
		}else{
			$title = ($this->url) ? '<a href="' . $this->url . '" title="' . $this->passage . '" rel="external">' . $this->title . '</a>' : $this->title;
			$retval[] = '<legend>'.$title.'</legend>';
			$retval[] = isset($this->logo) ? '<figure>'.$this->logo.'</figure>' : null;
			$retval[] = '<ul>';
			$retval[] =  $body; 
			$retval[] = '</ul>';
		}
		return join("\n",$retval);
	}

	function toString($timestamp){
		$retval = '';
		$rss_body = array();
		
		// エントリの内部を展開
		foreach ($this->items as $item){
			$rss_body[] = '<li>'.$this->format_line($item).'</li>';
		}

		$body = $this->format_body(join("\n",$rss_body));
		if (!IS_MOBILE){
			return '<fieldset class="'.$this->class.'">' . "\n" . $body . '</fieldset>' . "\n";
		}else{
			return '<div class="'.$this->class.'">' . "\n" . $body . '</div>' . "\n";
		}
	}
}

class ShowRSS_html_menubar extends ShowRSS_html
{
	// エントリの外側
	 function format_body($body){
		$retval = array();
		if (IS_MOBILE){
			$retval[] = '<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c"><h4>'.$this->title.'</h4>';
			$retval[] = '<ul data-role="listview" data-inset="true">';
			$retval[] = $body;
			$retval[] = '</ul>' . isset($this->title) ? '</div>' : '';
		}else{
			$desc = $line['desc'] ? mb_strimwidth(preg_replace("/[\r\n]/", ' ', strip_tags($line['desc'])), 0, 255, '...').get_passage($line['date']) : get_passage($line['date']);
			$title = ($this->url) ? 
				'<a href="' . $this->url . '" title="' .$desc . '" rel="external">' . $this->title . '</a>' :
				'<span title="'.$desc.'">' . $this->title . '</span>';
			$retval[] = '<h4>'.$title.'</h4>';
			$retval[] = '<ul>';
			$retval[] =  $body; 
			$retval[] = '</ul>';
		}
		return join("\n",$retval);
	}
	
	function toString($timestamp){
		$retval = '';
		$rss_body = array();
		
		// エントリの内部を展開
		foreach ($this->items as $item){
			$rss_body[] = '<li>'.$this->format_line($item).'</li>';
		}

		$body = $this->format_body(join("\n",$rss_body));
		return '<div class="'.$this->class.'">' . "\n" . $body . '</div>' . "\n";
	}
}

class ShowRSS_html_recent extends ShowRSS_html
{
	function format_line($line){
		$desc = mb_strimwidth(preg_replace("/[\r\n]/", ' ', strip_tags($line['desc'])), 0, 255, '...');
		if (IS_MOBILE){
			return '<li><a href="'. $line['link'] .'">'.$line['entry'].'</a><span class="ui-count">'.get_passage($line['date']).'</span></li>';
		}else{
			return '<li><a href="'. $line['link'] .'" title="'.$desc.' '.get_passage($line['date']).'">'.$line['entry'].'</a></li>';
		}
	}

	function format_body($body){
		if (IS_MOBILE){
			return '<ul data-role="listview">'. 
				'<li data-role="list-divider">' . $date . '</li>' . "\n" . $body . '</ul>' . "\n";
		}else{
			return '<strong>' . $date . '</strong>' . "\n" .
				'<ul class="recent_list">' . "\n" . $body . '</ul>' . "\n";
		}
	}
}
/* End of file showrss.inc.php */
/* Location: ./wiki-common/plugin/showrss.css.php */