<?php
// $Id: online.inc.php,v 1.12.2 2013/04/03 17:55:00 Logue Exp $
// Copyright (C)
//   2010-2013 PukiWiki Advance Developers Team
//   2002-2005, 2007 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Online plugin -- Just show the number 'users-on-line'

define('PLUGIN_ONLINE_TIMEOUT', 60 * 5); // Count users in N seconds

// ----

// List of 'IP-address|last-access-time(seconds)'
define('PLUGIN_ONLINE_USER_CACHE_NAME', 'online');


function plugin_online_convert()
{
	return plugin_online_itself(0);
}

function plugin_online_inline()
{
	return plugin_online_itself(1);
}

function plugin_online_itself($type = 0)
{
	global $cache;
	static $count, $result;

	if (! isset($count)) {
		$host = get_remoteip();
		// Try read
		if (plugin_online_check_online($count, $host)) {
			$result = TRUE;
		} else {
			// Write
			$count = plugin_online_sweep_records($host);
			$result = TRUE;
		}
	}

	if ($result) {
		return (int)$count; // Integer
	} else {
		$error = 'ERROR!';
		if ($type == 0) {
			$error = '#online: ' . $error . '<br />' . "\n";
		} else {
			$error = '&online: ' . $error . ';';
		}
		return $error; // String
	}
}

// Check I am already online (recorded and not time-out)
// & $count == Number of online users
function plugin_online_check_online(& $count, & $host = '')	// 参照渡しはコードがややこしくなるからやめてくれ
{
	global $cache;
	// Init
	$count   = 0;
	$found   = FALSE;
	$matches = array();

	// Read
	foreach ($cache['wiki']->getItem(PLUGIN_ONLINE_USER_CACHE_NAME) as $line){
		// Ignore invalid-or-outdated lines
		list($ahost, $atime) = $line;
		if ( ($atime + PLUGIN_ONLINE_TIMEOUT) <= UTIME || $atime <= UTIME) continue;

		++$count;
		if (! $found && $ahost == $host) $found = TRUE;
	}
	if (! $found && !empty($host)) ++$count; // About you

	return $found;
}

// Cleanup outdated records, Add/Replace new record, Return the number of 'users in N seconds'
// NOTE: Call this when plugin_online_check_online() returnes FALSE
function plugin_online_sweep_records($host = '')
{
	global $cache;

	// Need modify?
	$i = 0;
	$matches = array();
	$dirty   = FALSE;
	$count = 1;

	// Open
	foreach ($cache['wiki']->getItem(PLUGIN_ONLINE_USER_CACHE_NAME) as $line){
		list($ahost, $atime) = $line;
		
		if ( ($atime + PLUGIN_ONLINE_TIMEOUT) <= UTIME || $atime > UTIME || $ahost == $host) {
			
			--$count;
			$dirty = TRUE;
			continue; // Invalid or outdated or invalid date
		}
		$ret[] = $line;
		$i++;
	}
	if (!empty($host)) {
		// Add new, at the top of the record
		$ret[] = array(trim($host), UTIME);
		++$count;
		$dirty = TRUE;
	}

	if ($dirty) {
		// Write
		$cache['wiki']->setItem(PLUGIN_ONLINE_USER_CACHE_NAME, $ret);
	}

	return $count; // Number of lines == Number of users online
}
/* End of file online.inc.php */
/* Location: ./wiki-common/plugin/online.inc.php */