<?php
/*
 * Operating System
 *
 * @copyright   Copyright (c)2010       PukiWiki Advance Developer Team.
 *                           2004-2006, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: ua_operating_systems.cls.php,v 0.3 2010/06/24 00:00:03 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * o 参考にしたコード(AWStats)
 *   http://awstats.sf.net/
 *   Copyright (C) 2000-2010 - Laurent Destailleur - eldy@users.sf.net
 *   awstats-7.0/wwwroot/cgi-bin/lib/operating_systems.pm (Rev. 1.31)
 */

namespace PukiWiki\UA;

class Os
{
	private static $OSHashID = array(
		# Windows OS family
		array('windows[_+ ]nt[_+ ]6\.2',	'winlong'),	// windows8
		array('windows[_+ ]?2008',		'winlong'),	// win7.png
		array('windows[_+ ]nt[_+ ]6\.1',	'winlong'),
		array('windows[_+ ]?2005',		'winlong'),	// winlong.png
		array('windows[_+ ]nt[_+ ]6',	'winlong'),	
		array('windows[_+ ]?2003',		'win2003'),	// win2003.png
		array('windows[_+ ]nt[_+ ]5\.2',	'win2003'),	
		array('windows[_+ ]xp',			'winxp'),	// winxp.png
		array('windows[_+ ]nt[_+ ]5\.1',	'winxp'),	
		array('windows[_+ ]me',			'winme'),	// winme.png
		array('win[_+ ]9x',			'winme'),	
		array('windows[_+ ]?2000',		'win2000'),	// win2000.png
		array('windows[_+ ]nt[_+ ]5',		'win2000'),	
		array('winnt',				'winnt'),	// winnt.png
		array('windows[_+ \-]?nt',		'winnt'),	
		array('win32',				'winnt'),	
		array('win(.*)98',			'win98'),	// win98.png
		array('win(.*)95',			'win95'),	// win95.png
		array('win(.*)16',			'win16'),	// win16.png
		array('windows[_+ ]3',			'win16'),	
		array('win(.*)ce',			'wince'),	// wince.png
		# Macintosh OS family
		array('iphone[_+ ]os',		'iphone'),	// iPhone OS must set before MacOS.
		array('ios',				'ios'),
		array('mac[_+ ]os[_+ ]x',	'macosx'),	// macosx.png
		array('mac[_+ ]?p',			'macintosh'),	// macintosh.png
		array('mac[_+ ]68',			'macintosh'),	
		array('macweb',				'macintosh'),	
		array('macintosh',			'macintosh'),
		# Linux family
		array('linux(.*)android',		'linuxandroid'),	// Android
		array('linux(.*)asplinux',		'linixasplinux'),
		array('linux(.*)centos',		'linuxcentos'),
		array('linux(.*)debian',		'linuxdebian'),
		array('linux(.*)fedora',		'linuxfedora'),
		array('linux(.*)gentoo',		'linuxgentoo'),
		array('linux(.*)mandr',			'linuxmandr'),
		array('linux(.*)momonga',		'linuxmomonga'),
		array('linux(.*)pclinuxos',		'linuxpclinuxos'),
		array('linux(.*)red[_+ ]hat',	'linuxredhat'),
		array('linux(.*)suse',			'linuxsuse'),
		array('linux(.*)ubuntu',		'linuxubuntu'),
		array('linux(.*)vector',		'linuxvector'),
		array('linux(.*)vine',			'linuxvine'),
		array('linux(.*)white\sbox',	'linux'),
		array('linux(.*)zenwalk',		'linuxzenwalk'),
		array('linux',				'linux'),
		# Hurd family
		array('gnu.hurd',			'gnu'),
		# BSDs family
		array('bsdi',				'bsdi'),        // bsdi.png
		array('gnu.kfreebsd',		'bsdkfreebsd'),	// Must be before freebsd
		array('freebsd',			'freebsd'),     // freebsd.png
		array('openbsd',			'openbsd'),     // openbsd.png
		array('netbsd',				'netbsd'),      // netbsd.png
		array('dragonfly',			'bsddflybsd'),
		# Other Unix, Unix-like
		array('aix',				'aix'),		// aix.png
		array('sunos',				'sunos'),	// sunos.png
		array('irix',				'irix'),	// irix.png
		array('osf',				'osf'),		// osf.png
		array('hp\-ux',				'hpux'),	// hpux.png
		//array('gnu',				'gnu'),		// gnu.png
		array('unix',				'unix'),	// unix.png
		array('x11',				'unix'),	
		array('gnome\-vfs',			''),
		# Other famous OS
		array('beos',				'beos'),	// beos.png
		array('os/2',				'os2'),		// os2.png
		array('amiga',				'amigaos'),	// amigaos.png
		array('atari',				'atari'),	// atari.png
		array('vms',				'vms'),		// vms.png
		array('commodore',			'commodore'),	// commodore.png
		array('qnx',				'qnx'),
		array('inferno',			'inferno'),
		array('palmos',				'palmos'),
		array('syllable',			'syllable'),
		# Miscellanous OS
		array('blackberry',			'blackberry'),
		array('cp/m',				'cpm'),		// cpm.png
		array('crayos',				'crayos'),	
		array('dreamcast',			'dreamcast'),	// dreamcast.png
		array('risc[_+ ]?os',		'riscos'),	// riscos.png
		array('symbian',			'symbian'),	// symbian.png
		array('webtv',				'webtv'),	// webtv.png
		array('playstation',		'psp'),		// psp.png
		array('xbox',				'xbox'),	// xbox.png
		array('wii',				'wii'),
		array('vienna',				''),
		array('newsfire',			''),
		array('applesyndication',	'apple'),
		array('akregator',			''),
		array('plagger',			''),
		array('syndirella',			''),
		array('j2me',				'j2me'),
		array('java',				'java'),
		array('microsoft',			'win'),		// Pushed down to prevent mis-identification
		array('msie[_+ ]',			'win'),		// by other OS spoofers.
		array('ms[_+ ]frontpage',	'win'),
		array('windows',			'win'),
	);

	public static function get_icon($ua)
	{
		foreach(self::$OSHashID as $x) {
			$pat = "'".$x[0]."'si";
			if (preg_match($pat,$ua,$regs)) {
				return $x[1];
			}
		}
		return null;
	}
}

/* End of file ua_operating_systems.cls.php */
/* Location: ./wiki-common/lib/ua/ua_operating_systems.cls.php */
