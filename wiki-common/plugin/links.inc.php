<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: links.inc.php,v 1.24.3 2010/12/26 17:22:00 Logue Exp $
//
// Update link cache plugin

// Message setting
function plugin_links_init()
{
	$messages = array(
		'_links_messages'=>array(
			'title_update'  => T_("Cache update"),
			'msg_adminpass' => T_("Administrator password"),
			'btn_submit'    => T_("Exec"),
			'msg_done'      => T_("The update of cashe was completed."),
			'msg_error'		=> T_("The update of cashe was failure. Please check password."),
			'msg_usage1'	=> 
T_("* Content of processing\n") .
T_(":Cache update|\n") .
T_("All pages are scanned, whether on which page certain pages have been linked is investigated, and it records in the cache.\n\n") .
T_("* CAUTION\n") .
T_("It is likely to drive it for a few minutes in execution.") .
T_("Please wait for a while after pushing the execution button.\n\n"),
			'msg_usage2'	=> 
T_("* EXEC\n") .
T_("Please input the Administrator password, and click the [Exec] button.\n")
		),
	);
	set_plugin_messages($messages);
}

function plugin_links_action()
{
	global $post, $vars, $foot_explain;
	global $_links_messages;

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits this');
	if (auth::check_role('readonly')) die_message( _("PKWK_READONLY prohibits this") );

	$admin_pass = (empty($post['adminpass'])) ? '' : $post['adminpass'];
	if ( isset($vars['menu']) && (! auth::check_role('role_adm_contents') || pkwk_login($admin_pass) )) {
		set_time_limit(0);
		$msg  = & $_links_messages['title_update'];
		if (links_init() !== false){
			$foot_explain = array(); // Exhaust footnotes
			$body = & $_links_messages['msg_done'    ];
			return array('msg'=>$msg, 'body'=>$body);
		}else{
			return array('msg'=>$msg, 'body'=>$_links_messages['msg_failure'    ]);
		}
	}

	$msg   = & $_links_messages['title_update'];
	$body  = convert_html( sprintf($_links_messages['msg_usage1']) );
	$script = get_script_uri();
	$body .= <<<EOD
<form method="post" action="$script">
	<input type="hidden" name="plugin" value="links" />
	<input type="hidden" name="menu" value="1" />
		<div class="links_form">
EOD;
	if (auth::check_role('role_adm_contents')) {
		$body .= convert_html( sprintf($_links_messages['msg_usage2']) );
		$body .= <<<EOD
		<label for="_p_links_adminpass">{$_links_messages['msg_adminpass']}</label>
		<input type="password" name="adminpass" id="_p_links_adminpass" size="20" value="" />
EOD;
	}
	$body .= <<<EOD
		<input type="submit" value="{$_links_messages['btn_submit']}" />
	</div>
</form>
EOD;

	return array('msg'=>$msg, 'body'=>$body);
}
?>
