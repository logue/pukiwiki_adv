<?php if (!defined('BB2_CORE')) die('I said no cheating!');

require_once("bad-behavior/responses.inc.php");

function bb2_admin_pages() {
	global $wp_db_version;

	if (current_user_can('manage_options')) {
		add_options_page(__("Bad Behavior"), __("Bad Behavior"), 'manage_options', 'bb2_options', 'bb2_options');
		add_options_page(__("Bad Behavior Whitelist"), __("Bad Behavior Whitelist"), 'manage_options', 'bb2_whitelist', 'bb2_whitelist');
		add_management_page(__("Bad Behavior Log"), __("Bad Behavior Log"), 'manage_options', 'bb2_manage', 'bb2_manage');
		@session_start();
	}
}

function bb2_clean_log_link($uri) {
	foreach (array("paged", "ip", "key", "blocked", "request_method", "user_agent") as $arg) {
		$uri = remove_query_arg($arg, $uri);
	}
	return $uri;
}

function bb2_httpbl_lookup($ip) {
	// NB: Many of these are defunct
	$engines = array(
		1 => "AltaVista",
		2 => "Teoma/Ask Crawler",
		3 => "Baidu Spide",
		4 => "Excite",
		5 => "Googlebot",
		6 => "Looksmart",
		7 => "Lycos",
		8 => "msnbot",
		9 => "Yahoo! Slurp",
		10 => "Twiceler",
		11 => "Infoseek",
		12 => "Minor Search Engine",
	);
	$settings = bb2_read_settings();
	$httpbl_key = $settings['httpbl_key'];
	if (!$httpbl_key) return false;

	$r = $_SESSION['httpbl'][$ip];
	$d = "";
	if (!$r) {	// Lookup
		$find = implode('.', array_reverse(explode('.', $ip)));
		$result = gethostbynamel("${httpbl_key}.${find}.dnsbl.httpbl.org.");
		if (!empty($result)) {
			$r = $result[0];
			$_SESSION['httpbl'][$ip] = $r;
		}
	}
	if ($r) {	// Interpret
		$ip = explode('.', $r);
		if ($ip[0] == 127) {
			if ($ip[3] == 0) {
				if ($engines[$ip[2]]) {
					$d .= $engines[$ip[2]];
				} else {
					$d .= "Search engine ${ip[2]}<br/>\n";
				}
			}
			if ($ip[3] & 1) {
				$d .= "Suspicious<br/>\n";
			}
			if ($ip[3] & 2) {
				$d .= "Harvester<br/>\n";
			}
			if ($ip[3] & 4) {
				$d .= "Comment Spammer<br/>\n";
			}
			if ($ip[3] & 7) {
				$d .= "Threat level ${ip[2]}<br/>\n";
			}
			if ($ip[3] > 0) {
				$d .= "Age ${ip[1]} days<br/>\n";
			}
		}
	}
	return $d;
}

function bb2_donate_button($thispage) {
	return
	'		<div style="float: right; clear: right; width: 200px; border: 1px solid #e6db55; color: #333; background-color: lightYellow; padding: 0 10px">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<p>Bad Behavior is an important tool in the fight against web spam. Show your support by donating<br/>
	<select name="amount">
	<option value="2.99">$2.99 USD</option>
	<option value="4.99">$4.99 USD</option>
	<option value="9.99">$9.99 USD</option>
	<option value="19.99">$19.99 USD</option>
	<option value="">Other...</option>
	</select><br/>
			<input type="hidden" name="cmd" value="_donations">
			<input type="hidden" name="business" value="EAZGZZV7RE4QJ">
			<input type="hidden" name="lc" value="US">
			<input type="hidden" name="item_name" value="Bad Behavior '.BB2_VERSION.' (WordPress)">
			<input type="hidden" name="currency_code" value="USD">
			<input type="hidden" name="no_note" value="0">
			<input type="hidden" name="cn" value="Comments about Bad Behavior">
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="rm" value="1">
			<input type="hidden" name="return" value="'.$thispage.'">
			<input type="hidden" name="cancel_return" value="'.$thispage.'">
			<input type="hidden" name="currency_code" value="USD">
			<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHosted">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</p>
			</form>
			</div>
';
}

function bb2_manage() {
	global $wpdb;

	$request_uri = $_SERVER["REQUEST_URI"];
	if (!$request_uri) $request_uri = $_SERVER['SCRIPT_NAME'];	# IIS
	$settings = bb2_read_settings();
	$rows_per_page = 100;
	$where = "";

	// Get query variables desired by the user with input validation
	$paged = 0 + $_GET['paged']; if (!$paged) $paged = 1;
	if ($_GET['key']) $where .= "AND `key` = '" . $wpdb->escape($_GET['key']) . "' ";
	if ($_GET['blocked']) $where .= "AND `key` != '00000000' ";
	else if ($_GET['permitted']) $where .= "AND `key` = '00000000' ";
	if ($_GET['ip']) $where .= "AND `ip` = '" . $wpdb->escape($_GET['ip']) . "' ";
	if ($_GET['user_agent']) $where .= "AND `user_agent` = '" . $wpdb->escape($_GET['user_agent']) . "' ";
	if ($_GET['request_method']) $where .= "AND `request_method` = '" . $wpdb->escape($_GET['request_method']) . "' ";

	// Query the DB based on variables selected
	$r = bb2_db_query("SELECT COUNT(id) FROM `" . $settings['log_table']);
	$results = bb2_db_rows($r);
	$totalcount = $results[0]["COUNT(id)"];
	$r = bb2_db_query("SELECT COUNT(id) FROM `" . $settings['log_table'] . "` WHERE 1=1 " . $where);
	$results = bb2_db_rows($r);
	$count = $results[0]["COUNT(id)"];
	$pages = ceil($count / 100);
	$r = bb2_db_query("SELECT * FROM `" . $settings['log_table'] . "` WHERE 1=1 " . $where . "ORDER BY `date` DESC LIMIT " . ($paged - 1) * $rows_per_page . "," . $rows_per_page);
	$results = bb2_db_rows($r);

	// Display rows to the user
?>
<div class="wrap">
<?php
	echo bb2_donate_button(admin_url("tools.php?page=bb2_manage"));
?>
<h2><?php _e("Bad Behavior Log"); ?></h2>
<form method="post" action="<?php echo admin_url("tools.php?page=bb2_manage") ?>">
	<p>For more information please visit the <a href="http://www.bad-behavior.ioerror.us/">Bad Behavior</a> homepage.</p>
	<p>See also: <a href="<?php echo admin_url("options-general.php?page=bb2_options") ?>">Settings</a> | <a href="<?php echo admin_url("options-general.php?page=bb2_whitelist") ?>">Whitelist</a></p>
<div class="tablenav">
<?php
	$page_links = paginate_links(array('base' => add_query_arg("paged", "%#%"), 'format' => '', 'total' => $pages, 'current' => $paged));
	if ($page_links) echo "<div class=\"tablenav-pages\">$page_links</div>\n";
?>
<div class="alignleft">
<?php if ($count < $totalcount): ?>
Displaying <strong><?php echo $count; ?></strong> of <strong><?php echo $totalcount; ?></strong> records filtered by:<br/>
<?php if ($_GET['key']) echo "Status [<a href=\"" . esc_url( remove_query_arg(array("paged", "key"), $request_uri) ) . "\">X</a>] "; ?>
<?php if ($_GET['blocked']) echo "Blocked [<a href=\"" . esc_url( remove_query_arg(array("paged", "blocked", "permitted"), $request_uri) ) . "\">X</a>] "; ?>
<?php if ($_GET['permitted']) echo "Permitted [<a href=\"" . esc_url( remove_query_arg(array("paged", "blocked", "permitted"), $request_uri) ) . "\">X</a>] "; ?>
<?php if ($_GET['ip']) echo "IP [<a href=\"" . esc_url( remove_query_arg(array("paged", "ip"), $request_uri) ) . "\">X</a>] "; ?>
<?php if ($_GET['user_agent']) echo "User Agent [<a href=\"" . esc_url( remove_query_arg(array("paged", "user_agent"), $request_uri) ) . "\">X</a>] "; ?>
<?php if ($_GET['request_method']) echo "GET/POST [<a href=\"" . esc_url( remove_query_arg(array("paged", "request_method"), $request_uri) ) . "\">X</a>] "; ?>
<?php else: ?>
Displaying all <strong><?php echo $totalcount; ?></strong> records<br/>
<?php endif; ?>
<?php if (!$_GET['key'] && !$_GET['blocked']) { ?><a href="<?php echo esc_url( add_query_arg(array("blocked" => "1", "permitted" => "0", "paged" => false), $request_uri) ); ?>">Show Blocked</a> <?php } ?>
<?php if (!$_GET['key'] && !$_GET['permitted']) { ?><a href="<?php echo esc_url( add_query_arg(array("permitted" => "1", "blocked" => "0", "paged" => false), $request_uri) ); ?>">Show Permitted</a> <?php } ?>
</div>
</div>

<table class="widefat">
	<thead>
	<tr>
	<th scope="col" class="check-column"><input type="checkbox" onclick="checkAll(document.getElementById('request-filter'));" /></th>
	<th scope="col"><?php _e("IP/Date/Status"); ?></th>
	<th scope="col"><?php _e("Headers"); ?></th>
	<th scope="col"><?php _e("Entity"); ?></th>
	</tr>
	</thead>
	<tbody>
<?php
	$alternate = 0;
	if ($results) foreach ($results as $result) {
		$key = bb2_get_response($result["key"]);
		$alternate++;
		if ($alternate % 2) {
			echo "<tr id=\"request-" . $result["id"] . "\" valign=\"top\">\n";
		} else {
			echo "<tr id=\"request-" . $result["id"] . "\" class=\"alternate\" valign=\"top\">\n";
		}
		echo "<th scope=\"row\" class=\"check-column\"><input type=\"checkbox\" name=\"submit[]\" value=\"" . $result["id"] . "\" /></th>\n";
		$httpbl = bb2_httpbl_lookup($result["ip"]);
		$host = @gethostbyaddr($result["ip"]);
		if (!strcmp($host, $result["ip"])) {
			$host = "";
		} else {
			$host .= "<br/>\n";
		}
		echo "<td><a href=\"" . esc_url( add_query_arg("ip", $result["ip"], remove_query_arg("paged", $request_uri)) ) . "\">" . $result["ip"] . "</a><br/>$host<br/>\n" . $result["date"] . "<br/><br/><a href=\"" . esc_url( add_query_arg("key", $result["key"], remove_query_arg(array("paged", "blocked", "permitted"), $request_uri)) ) . "\">" . $key["log"] . "</a>\n";
		if ($httpbl) echo "<br/><br/><a href=\"http://www.projecthoneypot.org/ip_{$result['ip']}\">http:BL</a>:<br/>$httpbl\n";
		echo "</td>\n";
		$headers = str_replace("\n", "<br/>\n", htmlspecialchars($result['http_headers']));
		if (@strpos($headers, $result['user_agent']) !== FALSE) $headers = substr_replace($headers, "<a href=\"" . esc_url( add_query_arg("user_agent", rawurlencode($result["user_agent"]), remove_query_arg("paged", $request_uri)) ) . "\">" . $result['user_agent'] . "</a>", strpos($headers, $result['user_agent']), strlen($result['user_agent']));
		if (@strpos($headers, $result['request_method']) !== FALSE) $headers = substr_replace($headers, "<a href=\"" . esc_url( add_query_arg("request_method", rawurlencode($result["request_method"]), remove_query_arg("paged", $request_uri)) ) . "\">" . $result['request_method'] . "</a>", strpos($headers, $result['request_method']), strlen($result['request_method']));
		echo "<td>$headers</td>\n";
		echo "<td>" . str_replace("\n", "<br/>\n", htmlspecialchars($result["request_entity"])) . "</td>\n";
		echo "</tr>\n";
	}
?>
	</tbody>
</table>
<div class="tablenav">
<?php
	$page_links = paginate_links(array('base' => add_query_arg("paged", "%#%"), 'format' => '', 'total' => $pages, 'current' => $paged));
	if ($page_links) echo "<div class=\"tablenav-pages\">$page_links</div>\n";
?>
<div class="alignleft">
</div>
</div>
</form>
</div>
<?php
}


function bb2_whitelist()
{
	$whitelists = bb2_read_whitelist();
	if (empty($whitelists)) {
		$whitelists = array();
		$whitelists['ip'] = array();
		$whitelists['url'] = array();
		$whitelists['useragent'] = array();
	}

	$request_uri = $_SERVER["REQUEST_URI"];
	if (!$request_uri) $request_uri = $_SERVER['SCRIPT_NAME'];	# IIS

	if ($_POST) {
		$_POST = array_map('stripslashes_deep', $_POST);
		if ($_POST['ip']) {
			$whitelists['ip'] = array_filter(preg_split("/\s+/m", $_POST['ip']));
		} else {
			$whitelists['ip'] = array();
		}
		if ($_POST['url']) {
			$whitelists['url'] = array_filter(preg_split("/\s+/m", $_POST['url']));
		} else {
			$whitelists['url'] = array();
		}
		if ($_POST['useragent']) {
			$whitelists['useragent'] = array_filter(preg_split("/[\r\n]+/m", $_POST['useragent']));
		} else {
			$whitelists['useragent'] = array();
		}
		update_option('bad_behavior_whitelist', $whitelists);
?>
	<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php
	}
?>
	<div class="wrap">
<?php
	echo bb2_donate_button(admin_url("options-general.php?page=bb2_whitelist"));
?>
	<h2><?php _e("Bad Behavior Whitelist"); ?></h2>
	<form method="post" action="<?php echo admin_url("options-general.php?page=bb2_whitelist"); ?>">
	<p>Inappropriate whitelisting WILL expose you to spam, or cause Bad Behavior to stop functioning entirely! DO NOT WHITELIST unless you are 100% CERTAIN that you should.</p>
	<p>For more information please visit the <a href="http://www.bad-behavior.ioerror.us/">Bad Behavior</a> homepage.</p>
	<p>See also: <a href="<?php echo admin_url("options-general.php?page=bb2_options") ?>">Settings</a> | <a href="<?php echo admin_url("tools.php?page=bb2_manage"); ?>">Log</a></p>

	<h3><?php _e('IP Address'); ?></h3>
	<table class="form-table">
	<tr><td><label>IP address or CIDR format address ranges to be whitelisted (one per line)<br/><textarea cols="24" rows="6" name="ip"><?php echo implode("\n", $whitelists['ip']); ?></textarea></td></tr>
	</table>

	<h3><?php _e('URL'); ?></h3>
	<table class="form-table">
	<tr><td><label>URL fragments beginning with the / after your web site hostname (one per line)<br/><textarea cols="48" rows="6" name="url"><?php echo implode("\n", $whitelists['url']); ?></textarea></td></tr>
	</table>

	<h3><?php _e('User Agent'); ?></h3>
	<table class="form-table">
	<tr><td><label>User agent strings to be whitelisted (one per line)<br/><textarea cols="48" rows="6" name="useragent"><?php echo implode("\n", $whitelists['useragent']); ?></textarea></td></tr>
	</table>

	<p class="submit"><input class="button" type="submit" name="submit" value="<?php _e('Update &raquo;'); ?>" /></p>
	</form>
<?php
}


function bb2_options()
{
	$settings = bb2_read_settings();

	$request_uri = $_SERVER["REQUEST_URI"];
	if (!$request_uri) $request_uri = $_SERVER['SCRIPT_NAME'];	# IIS

	if ($_POST) {
		$_POST = array_map('stripslashes_deep', $_POST);
		if ($_POST['display_stats']) {
			$settings['display_stats'] = true;
		} else {
			$settings['display_stats'] = false;
		}
		if ($_POST['strict']) {
			$settings['strict'] = true;
		} else {
			$settings['strict'] = false;
		}
		if ($_POST['verbose']) {
			$settings['verbose'] = true;
		} else {
			$settings['verbose'] = false;
		}
		if ($_POST['logging']) {
			if ($_POST['logging'] == 'verbose') {
				$settings['verbose'] = true;
				$settings['logging'] = true;
			} else if ($_POST['logging'] == 'normal') {
				$settings['verbose'] = false;
				$settings['logging'] = true;
			} else {
				$settings['verbose'] = false;
				$settings['logging'] = false;
			}
		} else {
			$settings['verbose'] = false;
			$settings['logging'] = false;
		}
		if ($_POST['httpbl_key']) {
			if (preg_match("/^[a-z]{12}$/", $_POST['httpbl_key'])) {
				$settings['httpbl_key'] = $_POST['httpbl_key'];
			} else {
				$settings['httpbl_key'] = '';
			}
		} else {
			$settings['httpbl_key'] = '';
		}
		if ($_POST['httpbl_threat']) {
			$settings['httpbl_threat'] = intval($_POST['httpbl_threat']);
		} else {
			$settings['httpbl_threat'] = '25';
		}
		if ($_POST['httpbl_maxage']) {
			$settings['httpbl_maxage'] = intval($_POST['httpbl_maxage']);
		} else {
			$settings['httpbl_maxage'] = '30';
		}
		if ($_POST['offsite_forms']) {
			$settings['offsite_forms'] = true;
		} else {
			$settings['offsite_forms'] = false;
		}
		if ($_POST['eu_cookie']) {
			$settings['eu_cookie'] = true;
		} else {
			$settings['eu_cookie'] = false;
		}
		if ($_POST['reverse_proxy']) {
			$settings['reverse_proxy'] = true;
		} else {
			$settings['reverse_proxy'] = false;
		}
		if ($_POST['reverse_proxy_header']) {
			$settings['reverse_proxy_header'] = sanitize_text_field(uc_all($_POST['reverse_proxy_header']));
		} else {
			$settings['reverse_proxy_header'] = 'X-Forwarded-For';
		}
		if ($_POST['reverse_proxy_addresses']) {
			$settings['reverse_proxy_addresses'] = preg_split("/[\s,]+/m", $_POST['reverse_proxy_addresses']);
			$settings['reverse_proxy_addresses'] = array_map('sanitize_text_field', $settings['reverse_proxy_addresses']);
		} else {
			$settings['reverse_proxy_addresses'] = array();
		}
		bb2_write_settings($settings);
?>
	<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php
	}
?>
	<div class="wrap">
<?php
	echo bb2_donate_button(admin_url("options-general.php?page=bb2_options"));
?>
	<h2><?php _e("Bad Behavior"); ?></h2>
	<form method="post" action="<?php echo admin_url("options-general.php?page=bb2_options"); ?>">
	<p>For more information please visit the <a href="http://www.bad-behavior.ioerror.us/">Bad Behavior</a> homepage.</p>
	<p>See also: <a href="<?php echo admin_url("tools.php?page=bb2_manage"); ?>">Log</a> | <a href="<?php echo admin_url("options-general.php?page=bb2_whitelist") ?>">Whitelist</a></p>

	<h3><?php _e('Statistics'); ?></h3>
	<?php bb2_insert_stats(true); ?>
	<table class="form-table">
	<tr><td><label><input type="checkbox" name="display_stats" value="true" <?php if ($settings['display_stats']) { ?>checked="checked" <?php } ?>/> <?php _e('Display statistics in blog footer'); ?></label></td></tr>
	</table>

	<h3><?php _e('Logging'); ?></h3>
	<table class="form-table">
	<tr><td><label><input type="radio" name="logging" value="verbose" <?php if ($settings['verbose'] && $settings['logging']) { ?>checked="checked" <?php } ?>/> <?php _e('Verbose HTTP request logging'); ?></label></td></tr>
	<tr><td><label><input type="radio" name="logging" value="normal" <?php if ($settings['logging'] && !$settings['verbose']) { ?>checked="checked" <?php } ?>/> <?php _e('Normal HTTP request logging (recommended)'); ?></label></td></tr>
	<tr><td><label><input type="radio" name="logging" value="false" <?php if (!$settings['logging']) { ?>checked="checked" <?php } ?>/> <?php _e('Do not log HTTP requests (not recommended)'); ?></label></td></tr>
	</table>

	<h3><?php _e('Security'); ?></h3>
	<table class="form-table">
	<tr><td><label><input type="checkbox" name="strict" value="true" <?php if ($settings['strict']) { ?>checked="checked" <?php } ?>/> <?php _e('Strict checking (blocks more spam but may block some people)'); ?></label></td></tr>
	<tr><td><label><input type="checkbox" name="offsite_forms" value="true" <?php if ($settings['offsite_forms']) { ?>checked="checked" <?php } ?>/> <?php _e('Allow form postings from other web sites (required for OpenID; increases spam received)'); ?></label></td></tr>
	</table>

	<h3><?php _e('http:BL'); ?></h3>
	<p>To use Bad Behavior's http:BL features you must have an <a href="http://www.projecthoneypot.org/httpbl_configure.php?rf=24694">http:BL Access Key</a>.</p>
	<table class="form-table">
	<tr><td><label><input type="text" size="12" maxlength="12" name="httpbl_key" value="<?php echo sanitize_text_field($settings['httpbl_key']); ?>" /> http:BL Access Key</label></td></tr>
	<tr><td><label><input type="text" size="3" maxlength="3" name="httpbl_threat" value="<?php echo intval($settings['httpbl_threat']); ?>" /> Minimum Threat Level (25 is recommended)</label></td></tr>
	<tr><td><label><input type="text" size="3" maxlength="3" name="httpbl_maxage" value="<?php echo intval($settings['httpbl_maxage']); ?>" /> Maximum Age of Data (30 is recommended)</label></td></tr>
	</table>

	<h3><?php _e('European Union Cookie'); ?></h3>
	<p>Select this option if you believe Bad Behavior's site security cookie is not exempt from the 2012 EU cookie regulation. <a href="http://bad-behavior.ioerror.us/2012/05/03/bad-behavior-2-2-4/">More info</a></p>
	<table class="form-table">
	<tr><td><label><input type="checkbox" name="eu_cookie" value="true" <?php if ($settings['eu_cookie']) { ?>checked="checked" <?php } ?>/> <?php _e('EU cookie handling'); ?></label></td></tr>
	</table>

	<h3><?php _e('Reverse Proxy/Load Balancer'); ?></h3>
	<p>If you are using Bad Behavior behind a reverse proxy, load balancer, HTTP accelerator, content cache or similar technology, enable the Reverse Proxy option.</p>
	<p>If you have a chain of two or more reverse proxies between your server and the public Internet, you must specify <em>all</em> of the IP address ranges (in CIDR format) of all of your proxy servers, load balancers, etc. Otherwise, Bad Behavior may be unable to determine the client's true IP address.</p>
	<p>In addition, your reverse proxy servers must set the IP address of the Internet client from which they received the request in an HTTP header. If you don't specify a header, <a href="http://en.wikipedia.org/wiki/X-Forwarded-For">X-Forwarded-For</a> will be used. Most proxy servers already support X-Forwarded-For and you would then only need to ensure that it is enabled on your proxy servers. Some other header names in common use include <u>X-Real-Ip</u> (nginx) and <u>Cf-Connecting-Ip</u> (CloudFlare).</p>
	<table class="form-table">
	<tr><td><label><input type="checkbox" name="reverse_proxy" value="true" <?php if ($settings['reverse_proxy']) { ?>checked="checked" <?php } ?>/> <?php _e('Enable Reverse Proxy'); ?></label></td></tr>
	<tr><td><label><input type="text" size="32" name="reverse_proxy_header" value="<?php echo sanitize_text_field($settings['reverse_proxy_header']); ?>" /> Header containing Internet clients' IP address</label></td></tr>
	<tr><td><label>IP address or CIDR format address ranges for your proxy servers (one per line)<br/><textarea cols="24" rows="6" name="reverse_proxy_addresses"><?php echo esc_textarea(implode("\n", $settings['reverse_proxy_addresses'])); ?></textarea></td></tr>
	</table>

	<p class="submit"><input class="button" type="submit" name="submit" value="<?php _e('Update &raquo;'); ?>" /></p>
	</form>
	</div>
<?php
}

add_action('admin_menu', 'bb2_admin_pages');

function bb2_plugin_action_links($links, $file) {
	if ($file == "bad-behavior/bad-behavior-wordpress.php" && function_exists("admin_url")) {
		$log_link = '<a href="' . admin_url("tools.php?page=bb2_manage") . '">Log</a>';
		$settings_link = '<a href="' . admin_url("options-general.php?page=bb2_options") . '">Settings</a>';
		$whitelist_link = '<a href="' . admin_url("options-general.php?page=bb2_whitelist") . '">Whitelist</a>';
		array_unshift($links, $settings_link, $log_link, $whitelist_link);
	}
	return $links;
}
add_filter("plugin_action_links", "bb2_plugin_action_links", 10, 2);
