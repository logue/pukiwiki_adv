<?php
/*
 * Browser
 *
 * @copyright   Copyright &copy; 2004-2006, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version	 $Id: ua_browsers.cls.php,v 0.7.1 2012/03/21 15:15:00 Logue Exp $
 * @license	 http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * o 参考にしたコード(AWStats)
 *   http://awstats.sourceforge.net/
 *   Copyright (C) 2000-2010 - Laurent Destailleur - eldy@users.sourceforge.net
 *   awstats-7.0/wwwroot/cgi-bin/awstats.pl (Rev. 1.969)
 *   awstats-7.0/wwwroot/cgi-bin/lib/browsers.pm (Rev. 1.66)
 */

namespace PukiWiki\UA;

class Browsers{
	//sub UnCompileRegex {
	//	shift =~ /\(\?[-\w]*:(.*)\)/;
	//	return $1;
	//}

	// ブラウザ分類用
	var $browsers_id = array(

		'ia_archiver',
		'crazy.\bbrowser',
		'feedreader',
		'funwebproducts',
		'hotbar',
		'kddi',
		'lunascape',
		'msnbot',
		'turnitinbot',
		'Yahoo!.\bSlurp',

		# 有名なブラウザは、最初に定義しておけばヒット率があがる
		'elinks',
		'firebird',
		'firefox',
		'go!zilla',
		'icab',
		'konqueror',
		'links',
		'lynx',
		'omniweb',
		'opera',	# 携帯に実装された Opera があるんだよなぁ
		
		# Other standard web browsers
		'22acidownload',
		'abrowse',
		'aol\-iweng',
		'amaya',
		'amigavoyager',
		'arora',
		'aweb',
		'charon',
		'donzilla',
		'seamonkey',
		'flock',
		'minefield',
		'bonecho',
		'granparadiso',
		'songbird',
		'strata',
		'sylera',
		'kazehakase',
		'prism',
		'icecat',
		'iceape',
		'iceweasel',
		'w3clinemode',
		'bpftp',
		'camino',
		'chimera',
		'cyberdog',
		'dillo',
		'xchaos_arachne',
		'doris',
		'dreamcast',
		'xbox',
		'downloadagent',
		'ecatch',
		'emailsiphon',
		'encompass',
		'epiphany',
		'friendlyspider',
		'fresco',
		'galeon',
		'flashget',
		'freshdownload',
		'getright',
		'leechget',
		'netants',
		'headdump',
		'hotjava',
		'ibrowse',
		'intergo',
		'k\-meleon',
		'k\-ninja',
		'linemodebrowser',
		'lotus\-notes',
		'macweb',
		'multizilla',
		'ncsa_mosaic',
		'netcaptor',
		'netpositive',
		'nutscrape',
		'msfrontpageexpress',
		'contiki',
		'emacs\-w3',
		'phoenix',
		'shiira',				# Must be before safari
		'tzgeturl',
		'viking',
		'webfetcher',
		'webexplorer',
		'webmirror',
		'webvcr',
		'qnx\svoyager',
		
		# Site grabbers
		'teleport',
		'webcapture',
		'webcopier',
		
		# Music only browsers
		'real',
		'winamp',			# これは、winampmpeg および winamp3httprdr に左右する
		'windows\-media\-player',
		'audion',
		'freeamp',
		'itunes',
		'jetaudio',
		'mint_audio',
		'mpg123',
		'mplayer',
		'nsplayer',
		'qts',
		'quicktime',
		'sonique',
		'uplayer',
		'xaudio',
		'xine',
		'xmms',
		'gstreamer',
		
		# RSS Readers
		'abilon',
		'aggrevator',
		'aiderss',
		'akregator',
		'applesyndication',
		'betanews_reader',
		'blogbridge',
		'cyndicate',
		'feeddemon', 
		'feedreader', 
		'feedtools',
		'greatnews',
		'gregarius',
		'hatena_rss', 
		'jetbrains_omea', 
		'liferea',
		'netnewswire', 
		'newsfire', 
		'newsgator', 
		'newzcrawler',
		'plagger',
		'pluck', 
		'potu',
		'pubsub\-rss\-reader',
		'pulpfiction', 
		'rssbandit', 
		'rssreader',
		'rssowl', 
		'rss\sxpress',
		'rssxpress',
		'sage', 
		'sharpreader', 
		'shrook', 
		'straw', 
		'syndirella', 
		'vienna',
		'wizz\srss\snews\sreader',
		
		'livedoorCheckers',		# add upk
		'Bookmark\sRenewal\sCheck\sAgent', # add upk
		'webpatrol',			# add upk

		# PDA/携帯電話 ブラウザ
		'alcatel',				# Alcatel
		'lg\-',					# LG
		'mot\-',				# Motorola
		'nokia',				# Nokia
		'panasonic',			# Panasonic
		'philips',				# Philips
		'sagem',				# Sagem
		'samsung',				# Samsung
		'sie\-',				# SIE
		'sec\-',				# SonyEricsson
		'sonyericsson',			# SonyEricsson
		'ericsson',				# Ericsson (sonyericsson の後に定義すること)
		'mmef',
		'mspie',
		'vodafone',
		'wapalizer',
		'wapsilon',
		'wap',					# Generic WAP phone (must be after 'wap*')
		'webcollage',
		'up\.',					# これは、UP.Browser および UP.Link に左右する

		# PDA/携帯電話 I-Mode ブラウザ
		'android',
		'blackberry',
		'cnf2',
		'docomo',
		'ipcheck',
		'iphone',
		'portalmmm',
		'j\-phone',				# add upk
		'ddipocket',			# Opera 対応 で前出 // add upk

		# Others (TV)
		'webtv',
		'democracy',

		# Other kind of browsers
		'adobeair',
		'apt',
		'analogx_proxy',
		'gnome\-vfs',
		'neon',
		'curl',
		'csscheck',
		'httrack',
		'fdm',
		'javaws',
		'wget',
		'fget',
		'chilkat',
		'webdownloader\sfor\sx',
		'w3m',
		'wdg_validator',
		'w3c_validator',
		'jigsaw',
		'webreaper',
		'webzip',
		'staroffice',
		'gnus', 
		'nikto', 
		'download\smaster',
		'microsoft\-webdav\-miniredir', 
		'microsoft\sdata\saccess\sinternet\spublishing\sprovider\scache\smanager',
		'microsoft\sdata\saccess\sinternet\spublishing\sprovider\sdav',
		'POE\-Component\-Client\-HTTP',

		# UPK
		'harbot.\bgatestation',
		'sleipnir.\b',
		'wwwc\/',
		
		# 一番最後に定義すべきもの
		'mozilla',			# 大多数のブラウザは、mozila 文字列を含んでいる
		'libwww',			# libwww を利用するブラウザは、ブラウザ識別子と libwww の両方を含むため
		'lwp',
	);

	// ブラウザ分類後のアイコン設定用
	var $browsers_icon = array(
		# Standard web browsers
		'firefox'			=> 'firefox',
		'opera'				=> 'opera',
		'chrome'			=> 'chrome', 
		'safari'			=> 'safari',
		'konqueror'			=> 'konqueror',
		'svn'				=> 'subversion',
		'msie'				=> 'msie',
		'netscape'			=> 'netscape',

		'firebird'			=> 'phoenix',
		'go!zilla'			=> 'gozilla',
		'icab'				=> 'icab',
		'lynx'				=> 'lynx',
		'omniweb'			=> 'omniweb',
		
		# Other standard web browsers
		'amaya'				=> 'amaya',
		'amigavoyager'		=> 'amigavoyager',
		'avantbrowser'		=> 'avant',
		'aweb'				=> 'aweb',
		'bonecho'			=> 'firefox',
		'minefield'			=> 'firefox',
		'granparadiso'		=> 'firefox',
		'donzilla'			=> 'mozilla',
		'songbird'			=> 'mozilla',
		'strata'			=> 'mozilla',
		'sylera'			=> 'mozilla',
		'kazehakase'		=> 'mozilla',
		'prism'				=> 'mozilla',
		'iceape'			=> 'mozilla',
		'seamonkey'			=> 'seamonkey',
		'flock'				=> 'flock',
		'icecat'			=> 'icecat',
		'iceweasel'			=> 'iceweasel',
		'bpftp'				=> 'bpftp',
		'camino'			=> 'chimera',
		'chimera'			=> 'chimera',
		'cyberdog'			=> 'cyberdog',
		'dillo'				=> 'dillo',
		'doris'				=> 'doris',
		'dreamcast'			=> 'dreamcast',
		'xbox'				=> 'winxbox',
		'ecatch'			=> 'ecatch',
		'encompass'			=> 'encompass',
		'epiphany'			=> 'epiphany',
		'fresco'			=> 'fresco',
		'galeon'			=> 'galeon',
		'flashget'			=> 'flashget',
		'freshdownload'		=> 'freshdownload',
		'getright'			=> 'getright',
		'leechget'			=> 'leechget',
		'hotjava'			=> 'hotjava',
		'ibrowse'			=> 'ibrowse',
		'k\-meleon'			=> 'kmeleon',
		'lotus\-notes'		=> 'lotusnotes',
		'macweb'			=> 'macweb',
		'multizilla'		=> 'multizilla',
		'msfrontpageexpress'			=> 'fpexpress',
		'ncsa_mosaic'		=> 'ncsa_mosaic',
		'netpositive'		=> 'netpositive',
		'phoenix'			=> 'phoenix',
		# Site grabbers
		'teleport'			=> 'teleport',
		'webcapture'		=> 'adobe',
		'webcopier'			=> 'webcopier',
		# Media only browsers
		'real'				=> 'real',
		'winamp'			=> 'mediaplayer',		# Works for winampmpeg and winamp3httprdr
		'windows\-media\-player'			=> 'mplayer',
		'audion'			=> 'mediaplayer',
		'freeamp'			=> 'mediaplayer',
		'itunes'			=> 'mediaplayer',
		'jetaudio'			=> 'mediaplayer',
		'mint_audio'		=> 'mediaplayer',
		'mpg123'			=> 'mediaplayer',
		'mplayer'			=> 'mediaplayer',
		'nsplayer'			=> 'netshow',
		'qts'				=> 'mediaplayer',
		'sonique'			=> 'mediaplayer',
		'uplayer'			=> 'mediaplayer',
		'xaudio'			=> 'mediaplayer',
		'xine'				=> 'mediaplayer',
		'xmms'				=> 'mediaplayer',
		# RSS Readers
		'abilon'			=> 'abilon',
		'aggrevator'		=> 'rss',
		'aiderss'			=> 'rss',
		'akregator'			=> 'rss',
		'applesyndication'	=> 'rss',
		'betanews_reader'	=> 'rss',
		'blogbridge'		=> 'rss',
		'feeddemon'			=> 'rss',
		'feedreader'		=> 'rss',
		'feedtools'			=> 'rss',
		'greatnews'			=> 'rss',
		'gregarius'			=> 'rss',
		'hatena_rss'		=> 'rss',
		'jetbrains_omea'	=> 'rss',
		'liferea'			=> 'rss',
		'netnewswire'		=> 'rss',
		'newsfire'			=> 'rss',
		'newsgator'			=> 'rss',
		'newzcrawler'		=> 'rss',
		'plagger'			=> 'rss',
		'pluck'				=> 'rss',
		'potu'				=> 'rss',
		'pubsub\-rss\-reader'			=> 'rss',
		'pulpfiction'		=> 'rss',
		'rssbandit'			=> 'rss',
		'rssreader'			=> 'rss',
		'rssowl'			=> 'rss',
		'rss\sxpress'		=> 'rss',
		'rssxpress'			=> 'rss',
		'sage'				=> 'rss',
		'sharpreader'		=> 'rss',
		'shrook'			=> 'rss',
		'straw'				=> 'rss',
		'syndirella'		=> 'rss',
		'vienna'			=> 'rss',
		'wizz\srss\snews\sreader'			=> 'wizz',
		# PDA/Phonecell browsers
		'alcatel'			=> 'pdaphone',			# Alcatel
		'lg\-'				=> 'pdaphone',			# LG
		'ericsson'			=> 'pdaphone',			# Ericsson
		'mot\-'				=> 'pdaphone',			# Motorola
		'nokia'				=> 'pdaphone',			# Nokia
		'panasonic'			=> 'pdaphone',			# Panasonic
		'philips'			=> 'pdaphone',			# Philips
		'sagem'				=> 'pdaphone',			# Sagem
		'samsung'			=> 'pdaphone',			# Samsung
		'sie\-'				=> 'pdaphone',			# SIE
		'sec\-'				=> 'pdaphone',			# Sony/Ericsson
		'sonyericsson'		=> 'pdaphone',			# Sony/Ericsson
		'mmef'				=> 'pdaphone',
		'mspie'				=> 'pdaphone',
		'vodafone'			=> 'pdaphone',
		'wapalizer'			=> 'pdaphone',
		'wapsilon'			=> 'pdaphone',
		'wap'				=> 'pdaphone',			# Generic WAP phone (must be after 'wap*')
		'webcollage'		=> 'pdaphone',
		'up\.'				=> 'pdaphone',			# Works for UP.Browser and UP.Link
		# PDA/Phonecell browsers
		'android'			=> 'android',
		'blackberry'		=> 'pdaphone',
		'docomo'			=> 'pdaphone',
		'iphone'			=> 'pdaphone',
		'portalmmm'			=> 'pdaphone',
		# Others (TV)
		'webtv'				=> 'webtv',
		# Anonymous Proxy Browsers (can be used as grabbers as well...)
		'cjb\.net'			=> 'cjbnet',
		# Other kind of browsers
		'adobeair'			=> 'adobe',
		'apt'				=> 'apt',
		'analogx_proxy'		=> 'analogx',
		'microsoft\-webdav\-miniredir'			=> 'frontpage',
		'microsoft\sdata\saccess\sinternet\spublishing\sprovider\scache\smanager'			=> 'frontpage',
		'microsoft\sdata\saccess\sinternet\spublishing\sprovider\sdav'			=> 'frontpage',
		'microsoft\sdata\saccess\sinternet\spublishing\sprovider\sprotocol\sdiscovery'			=> 'frontpage',
		'microsoft\soffice\sprotocol\sdiscovery'			=> 'frontpage',
		'microsoft\soffice\sexistence\sdiscovery'			=> 'frontpage',
		'gnome\-vfs'		=> 'gnome', 
		'neon'				=> 'neon', 
		'javaws'			=> 'java',
		'webzip'			=> 'webzip',
		'webreaper'			=> 'webreaper',
		'httrack'			=> 'httrack',
		'staroffice'		=> 'staroffice',
		'gnus'				=> 'gnus',
		'mozilla'			=> 'mozilla'
	);

	//  $regvermsie		= qr/msie([+_ ]|)([\d\.]*)/i; // ex
	var $regvermsie		= "'msie([+_ ]|)([\d\.]*)'si";
	var $regverfirefox	= "'firefox\/([\d\.]*)'si";
	var $regversvn		= "'svn\/([\d\.]*)'si";
	var $regnotie		= "'webtv|omniweb|opera'si";
	var $regvernetscape	= "'netscape.?\/([\d\.]*)'si";
	var $regvermozilla	= "'mozilla(\/|)([\d\.]*)'si";
	var $regnotnetscape	= "'gecko|compatible|opera|galeon'si";
	var $regwebkit		= "'safari|chrome'si";
	var $id;

	// ブラウザを識別
	function get_id($ua) {
		$x = $this->set_browsers_id($ua);
		// 以下は除去して戻す
		foreach(array('.\b','\/','\-') as $_pat) {
			$x = str_replace($_pat,'',$x);
		}
		return $x;
	}
	// ブラウザのアイコンを設定
	function get_icon($ua)
	{
		return $this->set_browsers_icon($ua);
	}

	// ブラウザ識別
	function set_browsers_id($ua)
	{
		foreach ($this->browsers_id as $x) {
			$pat = "'".$x."'si";
			if (preg_match($pat,$ua,$regs)) return $x;
		}
		return '';
	}

	function set_browsers_icon($ua)
	{
		$this->id = $this->set_browsers_id($ua);

		if ($this->id == 'ddipocket')
			return $this->browsers_icon[$this->id];

		# IE ?
		if (preg_match($this->regvermsie,$ua,$regs)
		&& !preg_match($this->regnotie,$ua,$tmp)) {
			return $this->browsers_icon['msie'];
		}

		# Firefox ?
		if (preg_match($this->regverfirefox,$ua,$regs)) {
			return $this->browsers_icon['firefox'];
		}

		# Subversion ?
		if (preg_match($this->regversvn,$ua,$regs)) {
			return $this->browsers_icon['svn'];
		}

		# Netscape 6.x, 7.x ... ?
		if (preg_match($this->regvernetscape,$ua,$regs)) {
			return $this->browsers_icon['netscape'];
		}

		# Netscape 3.x, 4.x ... ?
		if (preg_match($this->regvermozilla,$ua,$regs)
		&& !preg_match($this->regnotnetscape,$ua,$tmp)) {
			return $this->browsers_icon['netscape'];
		}
		
		# WebKit... ?
		if (preg_match($this->regwebkit,$ua,$regs)){
			return $this->browsers_icon[strtolower($regs[0])];
		}

		// ブラウザ識別のアイコンがある場合は、それを設定
		if (isset($this->browsers_icon[$this->id]))
			return $this->browsers_icon[$this->id];
		return 'unknown';
	}
}
/* End of file ua_browsers.cls.php */
/* Location: ./wiki-common/lib/ua/ua_browsers.cls.php */
