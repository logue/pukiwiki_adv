<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: showrss.inc.php,v 1.22.8 2011/02/05 10:27:00 Logue Exp $
//  Id:showrss.inc.php,v 1.40 2003/03/18 11:52:58 hiro Exp
// Copyright (C):
//     2011      PukiWiki Advance Developers Team
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

define('PLUGIN_SHOWRSS_USAGE', '#showrss(URI-to-RSS[,default|menubar|recent[,Cache-lifetime[,Show-timestamp]]])');
defined('PLUGIN_SHOWRSS_SHOW_DESCRIPTION') or define('PLUGIN_SHOWRSS_SHOW_DESCRIPTION', true);


// Show related extensions are found or not
function plugin_showrss_action()
{
	global $vars;
	// if (PKWK_SAFE_MODE) die_message('PKWK_SAFE_MODE prohibit this');
	if (auth::check_role('safemode')) die_message('PKWK_SAFE_MODE prohibits this');
	
	if ($vars['feed']){
		$target = $vars['feed'];
		$cachehour = 1;

		// Get the cache not expired
		$filename = CACHE_DIR . encode($target) . '.xml';
		
		// Remove expired cache
		plugin_showrss_cache_expire($cachehour);

		if (is_readable($filename)) {
			$buf  = join('', file($filename));
			$time = filemtime($filename);
		}else{
			// Newly get RSS
			$data = pkwk_http_request($target);
			if ($data['rc'] !== 200)
				return array(FALSE, 0);

			$buf = $data['data'];
			$time = UTIME;

			// Save RSS into cache
			if ($cachehour) {
				pkwk_touch_file($filename);
				$fp = fopen($filename, 'w');
				fwrite($fp, $buf);
				fclose($fp);
			}
		}
		pkwk_common_headers($time);
		header('Content-Type: aplication/xml');
		echo $buf;
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
	$cachehour = 0;
	$template = $uri = '';
	switch ($num) {
	case 4: $timestamp = (trim($argv[3]) == '1');	/*FALLTHROUGH*/
	case 3: $cachehour = trim($argv[2]);		/*FALLTHROUGH*/
	case 2: $template  = strtolower(trim($argv[1]));/*FALLTHROUGH*/
	case 1: $uri       = trim($argv[0]);
	}

	$class = ($template == '' || $template == 'default') ? 'ShowRSS_html' : 'ShowRSS_html_' . $template;
	if (! is_numeric($cachehour))
		return '#showrss: Cache-lifetime seems not numeric: ' . htmlsc($cachehour) . '<br />' . "\n";
	if (! class_exists($class))
		return '#showrss: Template not found: ' . htmlsc($template) . '<br />' . "\n";
	if (! is_url($uri))
		return '#showrss: Seems not URI: ' . htmlsc($uri) . '<br />' . "\n";

	if (! is_requestable($uri))
		return '#showrss: Prohibit fetching RSS from my server<br />' . "\n";

	list($rss, $time) = plugin_showrss_get_rss($uri, $cachehour);
	if ($rss === FALSE) return '#showrss: Failed fetching RSS from the server<br />' . "\n";

	if ($timestamp > 0) {
		$time = '<p style="font-size:small; font-weight:bold; text-align:right;">Last-Modified:' .
			get_date('Y/m/d H:i:s', $time) .  '</p>';
	}

	$obj = new $class($rss);
	return $obj->toString($time);
}

// Create HTML from RSS array()
class ShowRSS_html
{
	var $items = array();
	var $class = 'showrss';

	function ShowRSS_html($xml)
	{
		// 整形
		if ((string) $xml->attributes()->version == '2.0'){
			// RSS2.0の場合（チャンネルが複数あった場合どーするんだ？これ？）
			foreach ($xml->channel as $channels){
				$this->title = '<a href="' . (string) $channels->link . '" title="' . $channels->description . '" rel="external">' . (string) $channels->title . '</a>';
				$this->logo = isset($channels->image) ? '<a href="'.(string) $channels->image->link.'" title="'.(string) $channels->image->title.'"><img src="'.(string) $channels->image->url.'" /></a>' : null;
				foreach ($channels->item as $item) {
					$date = strtotime((string) $item->pubDate);
					$this->items[] = array(
						'entry'	=> (string) $item->title,
						'link'	=> (string) $item->link,
						'date'	=> $date,
						'media'	=> isset($item->enclosure) ? (string) $item->enclosure->attributes()->url : null,
						'desc'	=> htmlspecialchars_decode((string) $item->description)
					);
				}
			}
		}else if($xml->entry){
			// <entry>が含まれる場合は、Atomと判断する。
			$xml->registerXPathNamespace('feed', 'http://www.w3.org/2005/Atom');
			foreach ($xml->link as $link) {
				if ($link->attributes()->type == 'text/html'){
					$href = (string) $link->attributes()->href;
					break;
				}
			}
			$passage = get_passage( strtotime((string) $xml->updated) );
			$this->title = '<a href="'.$href.'" title="'.(string) $xml->subtitle.' '.$passage.'" rel="external">'.(string) $xml->title.'</a>';
			$this->logo = '<a href="'.$href.'><img src="'.(string) $xml->icon.'" /></a>';
			// atom podcastは未対応（複数指定可能ってどんだけー！？）
			// contentタグには未対応
			foreach ($xml->entry as $entry) {
				$date = strtotime((string) $entry->published);
				if ($entry->summary){
					$desc = (string) $entry->summary;
				}else{
					$desc = null;
				}
				
				$this->items[] = array(
					'entry'	=> (string) $entry->title,
					'link'	=> (string) $entry->link->attributes()->href,
					'date'	=> $date,
					'desc'	=> $desc
				);
			}
		}else{
//			$rdf = $xml->channel->items->children('http://www.w3.org/1999/02/22-rdf-syntax-ns#');	// RDF（未使用）
//			$dc = $xml->channel->children('http://purl.org/dc/elements/1.1/');	// ダブリンコア
			// RSS1.xの場合
			$this->title = '<a href="' . $xml->channel->link . '" title="' .   $xml->channel->description . '" rel="external">' . $xml->channel->title . '</a>';
			$this->logo = (isset($xml->channels->image)) ? '<a href="'.$xml->channels->image->link.'" title="'.$xml->channels->image->title.'"><img src="'.$xml->channels->image->url.'" /></a>' : null;

			foreach ($xml->item as $item) {
				$item_dc = $item->children('http://purl.org/dc/elements/1.1/');
				$date = strtotime((string) $item_dc->date);
				$this->items[] = array(
					'entry'	=> (string) $item->title,
					'link'	=> (string) $item->link,
					'date'	=> $date,
					'desc'	=> htmlspecialchars_decode((string) $item->description)
				);
			}
		}
	}


	// エントリの内容
	function format_line($line){
		$desc = mb_strimwidth(preg_replace("/[\r\n]/", ' ', strip_tags($line['desc'])), 0, 255, '...');
		return '<a href="'. $line['link'] .'" title="'.$desc.' '.get_passage($line['date']).'">'.$line['entry'].'</a><br />';
	}

	// エントリの外側
	function format_body($body, $date, $tite, $logo){
		return $body."\n";
	}

	function toString($timestamp){

		$retval = '';
		$rss_body = array();
		
		// エントリの内部を展開
		foreach ($this->items as $item){
			$rss_body[] = $this->format_line($item);
		}

		return '<div class="'.$this->class.'">'.$this->format_body(join("\n",$rss_body), $timestamp, $this->title, $this->logo).'</div>'."\n";

	}
}

class ShowRSS_html_menubar extends ShowRSS_html
{
	function format_line($line){
		$desc = mb_strimwidth(preg_replace("/[\r\n]/", ' ', strip_tags($line['desc'])), 0, 255, '...');
		return '<li><a href="'. $line['link'] .'" title="'.$desc.' '.get_passage($line['date']).'">'.$line['entry'].'</a></li>';
	}

	function format_body($body, $date, $tite, $logo){
		return '<ul>' . "\n" . $body . '</ul>' . "\n";
	}
}

class ShowRSS_html_recent extends ShowRSS_html
{
	function format_line($line){
		$desc = mb_strimwidth(preg_replace("/[\r\n]/", ' ', strip_tags($line['desc'])), 0, 255, '...');
		return '<li><a href="'. $line['link'] .'" title="'.$desc.' '.get_passage($line['date']).'">'.$line['entry'].'</a></li>';
	}

	function format_body($body, $date, $tite, $logo){
		return '<strong>' . $date . '</strong>' . "\n" .
			'<ul class="recent_list">' . "\n" . $str . '</ul>' . "\n";
	}
}

// Get and save RSS
function plugin_showrss_get_rss($target, $cachehour)
{
	$buf  = '';
	$time = NULL;

	if ($cachehour) {
		// Get the cache not expired
		$filename = CACHE_DIR . md5($target) . '.xml';
		
		// Remove expired cache
		plugin_showrss_cache_expire($cachehour);

		if (is_readable($filename)) {
			$buf  = join('', file($filename));
			$time = filemtime($filename);
		}else{
			// Newly get RSS
			$data = pkwk_http_request($target);
			if ($data['rc'] !== 200)
				return array(FALSE, 0);

			$buf = $data['data'];
			$time = UTIME;

			pkwk_touch_file($filename);
			$fp = fopen($filename, 'w');
			fwrite($fp, $buf);
			fclose($fp);
		}
		$xml = simplexml_load_file($filename);
	}else{
		$time = UTIME;
		$xml = simplexml_load_file($target);
	}
	
	return array($xml,$time);
}

// Remove cache if expired limit exeed
function plugin_showrss_cache_expire($cachehour)
{
	$expire = $cachehour * 60 * 60; // Hour
	$dh = dir(CACHE_DIR);
	while (($file = $dh->read()) !== FALSE) {
		if (substr($file, -4) != '.xml') continue;
		$file = CACHE_DIR . $file;
		$last = time() - filemtime($file);
		if ($last > $expire) unlink($file);
	}
	$dh->close();
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
?>
