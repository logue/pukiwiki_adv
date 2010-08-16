<?php
/**
 * Google Analytics プラグイン
 *
 * @copyright   Copyright &copy; 2007,2009-2010, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: google_analytics.inc.php,v 0.3 2010/05/09 03:36:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 *
 */

defined('GOOGLE_ANALYTICS_UACCT') or define('GOOGLE_ANALYTICS_UACCT', 'UA-0000000-1');
defined('GOOGLE_ANALYTICS_SKIN')  or define('GOOGLE_ANALYTICS_SKIN', '0');

function plugin_google_analytics_convert()
{
	global $foot_tags;
	if (GOOGLE_ANALYTICS_SKIN) return '';
	if (GOOGLE_ANALYTICS_UACCT == 'UA-0000000-1') {
		return '<p>ERROR: GOOGLE_ANALYTICS_UACCT must be set.</p>';
	}

	$foot_tags[] = google_analytics_put_code();
}

function google_analytics_put_code()
{
	static $google_analytics = false;
	if (GOOGLE_ANALYTICS_UACCT == 'UA-0000000-1') return '';
	if ($google_analytics) return '';

	// FIXME
	$type = 0;
	$subdomain = 'blogdns.net';
	switch($type) {
	case 0:
		// 単一のドメイン(デフォルト)
		$setDomainName = $setAllowLinker = '';
		break;
	case 1:
		// 複数のサブドメインがある １ つのドメイン
		$setDomainName = 'pageTracker._setDomainName(".'.$subdomain.'");';
		$setAllowLinker = '';
		break;
	case 2:
		// 複数のトップ レベル ドメイン
		$setDomainName = 'pageTracker._setDomainName("none");';
		$setAllowLinker = 'pageTracker._setAllowLinker(true);';
		break;
	}

	$WebPropertyID = GOOGLE_ANALYTICS_UACCT;
	return <<<EOD
<script type="text/javascript">
 var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
 document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
 try {
  var pageTracker = _gat._getTracker("{$WebPropertyID}");{$setDomainName}{$setAllowLinker}
  pageTracker._trackPageview();
 } catch(err) {}
</script>

EOD;
	$google_analytics = true;
}
?>
