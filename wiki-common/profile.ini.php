<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone
// $Id: profile.ini.php,v 0.1 2013/03/18 18:42:00 Logue Exp $
// Copyright (C)
//   2012-2013 PukiWiki Advance Developers Team
//   2005-2007 PukiWiki Plus! Team
//   2002-2007 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//

/////////////////////////////////////////////////
// User-Agent settings
//
// If you want to ignore embedded browsers for rich-content-wikisite,
// remove (or comment-out) all 'mobile' settings.
//
// If you want to to ignore desktop-PC browsers for simple wikisite,
// copy mobile.ini.php to default.ini.php and customize it.

return array(
// pattern: A regular-expression that matches device(browser)'s name and version
// profile: A group of browsers
	// Game machine browsers
	// PS2: Mozilla/4.0 (PS2; PlayStation BB Navigator 1.0) NetFront/3.0
	array('pattern'=>'#\b(NetFront)/([0-9\.]+)\b#',		'csssname'=>'netfront',	'profile'=>'default'),
	
	// Wii
	// Sample: Opera/9.10 (Nintendo Wii; U; ; 1621; ja)
	array('pattern'=>'#\b(Opera)/([0-9\.]+) \(Nintendo Wii\b#',		'css'=>'netfront',	'profile'=>'default'),
	
	array('pattern'=>'#\b(Opera)/([0-9\.]+)\b#',		'css'=>'netfront',	'profile'=>'default'),
    // Desktop-PC browsers

	// Opera (for desktop PC, not embedded) -- See BugTrack/743 for detail
	// NOTE: Keep this pattern above MSIE and Mozilla
	// Sample: "Opera/9.80 (Windows NT 6.1; U; ja) Presto/2.7.62 Version/11.01"
	array('pattern'=>'#\b(Presto)[/ ]([0-9\.]+)\b#',	'css'=>'presto2',	'profile'=>'default'),
	array('pattern'=>'#\b(Opera)[/ ]([0-9\.]+)\b#',		'css'=>'presto',	'profile'=>'default'),	// legacy Opera

	// MSIE: Microsoft Internet Explorer (or something disguised as MSIE)
	// Sample: "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)"
	array('pattern'=>'#\b(MSIE) ([0-9\.]+)\b#',			'css'=>'ie',		'profile'=>'default'),

	// Mozilla Firefox
	// NOTE: Keep this pattern above Mozilla
	// Sample: "Mozilla/5.0 (Windows; U; Windows NT 5.0; ja-JP; rv:1.7) Gecko/20040803 Firefox/0.9.3"
	array('pattern'=>'#\b(Firefox|Netscape)/([0-9\.]+)\b#',	'css'=>'gecko',		'profile'=>'default'),
	
	// Mobile
	array('pattern'=>'#\b(iPhone|iPhone|iPad|iPod|Android|IEMobile)+\b#',	'profile'=>	'mobile'),
	
	// Safari / Chrome (WebKit)
	array('pattern'=>'#\b(AppleWebKit)/([0-9\.]+)\b#',	'css'=>'default',	'profile'=>'default'),

	// Loose default: Including something Mozilla
	array('pattern'=>'#^([a-zA-z0-9 ]+)/([0-9\.]+)\b#',	'profile'=>'default'),

	array('pattern'=>'#^#',	'profile'=>'default'),	// Sentinel
);

/* End of file profile.ini.php */
/* Location: ./wiki-common/profile.ini.php */