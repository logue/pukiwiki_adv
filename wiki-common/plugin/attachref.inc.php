<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
// $Id: attachref.inc.php,v 0.15.15 2015/12/06 00:20:00 Logue Exp $
// Copyright (C)
//   2011-2013,2015 PukiWiki Advance Developers Team
//   2005-2006,2008 PukiWiki Plus! Team
//   2002-2004 sha
//
// File attach & ref plugin
use PukiWiki\Factory;
use PukiWiki\Utility;
use PukiWiki\Router;
use PukiWiki\Renderer\PluginRenderer;

defined('ATTACHREF_UPLOAD_MAX_FILESIZE') or define('ATTACHREF_UPLOAD_MAX_FILESIZE', '4M'); // default: 4MB
// max file size for upload on PHP(PHP default 2MB)
ini_set('upload_max_filesize', ATTACHREF_UPLOAD_MAX_FILESIZE);
// max file size for upload on script of PukiWiki(default 2MB)
defined('ATTACHREF_MAX_FILESIZE')        or define('ATTACHREF_MAX_FILESIZE',(2048 * 1024));
// Admin Only for upload/delete
defined('ATTACHREF_UPLOAD_ADMIN_ONLY')   or define('ATTACHREF_UPLOAD_ADMIN_ONLY', FALSE); // FALSE or TRUE
// Requied password for upload/delete (ATTACHREF_UPLOAD_ADMIN_ONLY gives priority)
defined('ATTACHREF_PASSWORD_REQUIRE')    or define('ATTACHREF_PASSWORD_REQUIRE', FALSE);  // FALSE or TRUE
// Text wrapping
defined('PLUGIN_ATTACHREF_WRAP_TABLE')   or define('PLUGIN_ATTACHREF_WRAP_TABLE', FALSE); // FALSE or TRUE

function plugin_attachref_init()
{
	global $_string;
	$messages = array(
		'_attachref_messages' => array(
			// copy of attach.inc.php
			'msg_upload'	=> T_("Upload to $1"),
			'msg_maxsize'	=> T_("Maximum file size is <var>%s</var>."),
			'msg_adminpass'	=> T_("Administrator password"),
			'msg_password'	=> T_("password"),
			'msg_file'		=> T_("Attach file"),
			'btn_upload'	=> T_("Upload"),

			// original attachref.inc.php
			'btn_submit'			=> T_("[Upload]"),
			'msg_title'				=> T_("Attach and Ref to $1"),
			'msg_title_collided'	=> $_string['title_collided'],
			'msg_collided'			=> $_string['msg_collided']
		),
	);
	
	PluginRenderer::getPluginInfo('attach', true);
	if (!exist_plugin('attach') or !function_exists('attach_upload'))
	{
		return array('msg'=>'attach.inc.php not found or not correct version.');
	}

	set_plugin_messages($messages);
}

function plugin_attachref_options(&$extra_options, $args)
{
	global $vars;
	static $numbers = array();
	static $no_flag = 0;
	$button = 0;

	if (!isset($numbers[$vars['page']])) {
		$numbers[$vars['page']] = 0;
	}

	$attachref_no = $numbers[$vars['page']]++;

	$options = array();
	foreach ($args as $opt) {
		switch ($opt) {
		case 'button':
			$button = 1;
			break;
		case 'number':
			$no_flag = 1;
			break;
		case 'nonumber':
			$no_flag = 0;
			break;
		default:
			array_push($options, $opt);
		}
	}

	$extra_options['button'] = $button;
	$extra_options['refnum'] = $attachref_no;
	$extra_options['text'] = $no_flag ? '['.$attachref_no.']':'';
	return $options;
}

function plugin_attachref_convert()
{
	global $vars,$digest;
	global $_attachref_messages;
	
	if (!isset($vars['page'])) return '';

	$extra_options = array();
	$args = func_get_args();
//	$btn_text = array_pop($args);
//	$btn_text = $btn_text ? $btn_text : $_attachref_messages['btn_submit'];
	$btn_text = $_attachref_messages['btn_submit'];
	$options = plugin_attachref_options($extra_options, $args);
	$button = $extra_options['button'];
	$attachref_no = $extra_options['refnum'];
	$btn_text .= $extra_options['text'];

	$ret = '';
	$dispattach = 1;

	$args = $options;
	if ( count($args) and $args[0] != '' ) {
		require_once(PLUGIN_DIR . 'ref.inc.php');
		$params = plugin_ref_body(func_get_args());
		if (isset($params['_error']) && $params['_error'] != '') {
			$ret = $params['_error'];
			$dispattach = 1;
		} else {
			// start --- copy of plugin_ref_convert()
			if ((PLUGIN_ATTACHREF_WRAP_TABLE && ! isset($params['nowrap'])) || isset($params['wrap'])) {
				$margin = ($params['around'] ? '0px' : 'auto');
				$margin_align = ($params['_align'] == 'center') ? '' : ';margin-' . $params['_align'] . ':0px';
				$params['_body'] = <<<EOD
<table style="margin:$margin$margin_align">
	<tr>
		<td>{$params['_body']}</td>
	</tr>
</table>
EOD;
			}
			if (isset($params['around'])) {
				$style = ($params['_align'] == 'right') ? 'float:right' : 'float:left';
			} else {
				$style = 'text-align:' . $params['_align'];
			}
			// wrapped "figure"
			$ret = '<figure style="' . $style . '">' . $params['_body'] . '</figure>'."\n";
			
			// final --- copy of plugin_ref_convert()
			$dispattach = 0;
		}
	}
	if ( $dispattach ) {
	    // Escape foreign value
	    $s_args = trim(join(",", $args));
	    if ( $button ){
		} else {
			$f_btn_text = preg_replace('/<[^<>]+>/','',$btn_text);
			$btn_url = get_cmd_uri('attachref', null,null,	array(
				'attachref_no'	=> $attachref_no,
				'attachref_opt'	=> $s_args,
				'refer'			=> $vars['page'],
				'digest'		=> $digest
			));
			$ret .= '<a href="'.$btn_url.'" title="'.$f_btn_text.'">'.$btn_text.'</a>';
		}
	}

	return $ret;
}

function plugin_attachref_inline()
{
	global $vars, $digest;
	global $_attachref_messages;
#	static $numbers = array();
#	static $no_flag = 0;

#	if (!array_key_exists($vars['page'],$numbers))
#	{
#		$numbers[$vars['page']] = 0;
#	}
#	$attachref_no = $numbers[$vars['page']]++;

	$ret = '';
	$dispattach = 1;

	$extra_options = array();
	$args = func_get_args();
	$btn_text = array_pop($args);
	$btn_text = $btn_text ? $btn_text : $_attachref_messages['btn_submit'];
	$options = plugin_attachref_options($extra_options, $args);
	$button = $extra_options['button'];
	$attachref_no = $extra_options['refnum'];
	$btn_text .= $extra_options['text'];
#	$button = 0;

	$args = func_get_args();
#   $btn_text = array_pop($args);
#   $btn_text = $btn_text ? $btn_text : $_attachref_messages['btn_submit'];

#   $options = array();
#   foreach ( $args as $opt ){
#	    if ( $opt === 'button' ){
#	        $button = 1;
#	    }
#	    else if ( $opt === 'number' ){
#		$no_flag = 1;
#	    }
#	    else if ( $opt === 'nonumber' ){
#		$no_flag = 0;
#	    }
#	    else {
#	        array_push($options, $opt);
#	    }
#	}
#   $btn_text .= ( $no_flag == 1 ) ? "[$attachref_no]" : '';
	$args = $options;
	if (count($args) && $args[0] != '') {
		require_once(PLUGIN_DIR . 'ref.inc.php');
	    $params = plugin_ref_body($args, $vars['page']);
	    if (isset($params['_error'])) {
			$ret = $params['_error'];
			$dispattach = 1;
	    } else {
			$ret = $params['_body'];
			$dispattach = 0;
	    }
	}

	if ($dispattach) {
		// Escape foreign value
		$s_args = trim(join(",", $args));
		if ($button) {
			$script = Router::get_script_uri();
			$s_args .= ',button';
			$f_page = Utility::htmlsc($vars['page']);
			$f_args = Utility::htmlsc($s_args);
			$ret = <<<EOD
<form action="$script" method="post" class="plugin-attacherf-form">
	<input type="hidden" name="attachref_no" value="$attachref_no" />
	<input type="hidden" name="attachref_opt" value="$f_args" />
	<input type="hidden" name="digest" value="$digest" />
	<input type="hidden" name="cmd" value="attachref" />
	<input type="hidden" name="refer" value="$f_page" />
	$ret
	<input class="btn btn-secondary" type="submit" value="$btn_text" />
</form>
EOD;
		} else {
			$f_btn_text = preg_replace('/<[^<>]+>/','',$btn_text);
			$btn_url = get_cmd_uri('attachref', $vars['page'],	'',	array(
				'attachref_no'	=> $attachref_no,
				'attachref_opt'	=> $s_args,
				'refer'			=> $vars['page'],
				'digest'		=> $digest
			));
			$ret .= '<a href="'.$btn_url.'" title="'.$f_btn_text.'"><small><span class="fa fa-paperclip">'.$btn_text.'</span></small></a>';
	    }
	}
	return $ret;
}

function plugin_attachref_action()
{
	global $vars;
	global $_attachref_messages;

	$retval['msg'] = $_attachref_messages['msg_title'];
	$retval['body'] = '';
	
	$refer = isset($vars['refer']) ? $vars['refer'] : false;
	
	if (isset($_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]) && $refer !== false) {
		$wiki = Factory::Wiki($refer);
		
		if (!$wiki->isValied()){
			Utility::dieMessage('#attachref : invalied page.');
		}

		$file = $_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME];
		$attachname = $file['name'][0];

		$filename = preg_replace('/\..+$/','', $attachname,1);

		// If exist file, add a name '_0', '_1', ...
		$count = '_0';
		while (file_exists(UPLOAD_DIR .encode($refer).'_'.encode($attachname)))
		{
			$attachname = preg_replace('/^[^\.]+/',$filename.$count++,$attachname);
		}
		$file['name'][0] = $attachname;

		$attach_filename = attachref_get_attach_filename($file);
		$pass = isset($vars['pass']) ? md5($vars['pass']) : NULL;
		$retval = attach_upload($refer, $pass);
		
		if ($retval['result'] == TRUE) {
			$retval = attachref_insert_ref($attach_filename);
		}
		Utility::redirect($wiki->uri());
	}
	else
	{
		$retval = attachref_showform();
	}
	return $retval;
}

function attachref_get_attach_filename(&$file)
{
	$type = Utility::getMimeInfo($file['tmp_name'][0]);
	$must_compress = attach_is_compress($type,PLUGIN_ATTACH_UNKNOWN_COMPRESS);

	if ($must_compress && is_uploaded_file($file['tmp_name'][0])) {
		if (PLUGIN_ATTACH_COMPRESS_TYPE == 'TGZ' && exist_plugin('dump')) {
			return $file['name'][0] . '.tgz';
		} else
		if (PLUGIN_ATTACH_COMPRESS_TYPE == 'GZ' && extension_loaded('zlib')) {
			return $file['name'][0] . '.gz';
		} else
		if (PLUGIN_ATTACH_COMPRESS_TYPE == 'BZ2' && extension_loaded('bz2')) {
			return $file['name'][0] . '.bz2';
		} else
		if (PLUGIN_ATTACH_COMPRESS_TYPE == 'ZIP' && class_exists('ZipArchive')) {
			return $file['name'][0] . '.zip';
		}
	}
	return $file['name'][0];
}

function attachref_insert_ref($filename)
{
	global $vars;
	global $_attachref_messages;

	$ret['msg'] = $_attachref_messages['msg_title'];

	$args = explode(',', $vars['attachref_opt']);
	if (count($args)) {
	    $args[0] = './' . $filename;
	    $s_args = join(",", $args);
	} else {
	    $s_args = './' . $filename;
	}
	$msg = '&attachref('.$s_args.')';
	$msg_block = '#attachref('.$s_args.')';
	
	$refer = $vars['refer'];
	$digest = $vars['digest'];
	$wiki = Factory::Wiki($refer);

	$postdata = array();
	$attachref_ct = 0; // count of '#attachref'
	$attachref_no = $vars['attachref_no'];
	$skipflag = 0;
	$postdata_tmp = array();

	// Numbering inline plugin
	foreach ($wiki->get() as $line)
	{
		if ( $skipflag || substr($line,0,1) == ' ' || substr($line,0,2) == '//' ){
//			$postdata .= $line;
			array_push($postdata_tmp, $line);
			continue;
		}
		$ct = preg_match_all('/&attachref(?=[({;])/', $line, $out);
		if ( $ct ){
			for($i=0; $i < $ct; $i++){
				if ($attachref_ct++ == $attachref_no ){
					$line = preg_replace('/&attachref(\([^(){};]*\))?(\{[^{}]*\})?;/',$msg.'$2;',$line,1);
					$skipflag = 1;
					break;
				} else {
					$line = preg_replace('/&attachref(\([^(){};]*\))?(\{[^{}]*\})?;/','&___attachref$1$2___;',$line,1);
				}
			}
			$line = preg_replace('/&___attachref(\([^(){};]*\))?(\{[^{}]*\})?___;/','&attachref$1$2;',$line);
//			$postdata .= "|$ct|$attachref_ct|$attachref_no|$line";
	    }
//		$postdata .= $line;
		array_push($postdata_tmp, $line);
	}

	// Numbering block-type plugin
	foreach($postdata_tmp as $line) {
		if ( $skipflag || substr($line,0,1) == ' ' || substr($line,0,2) == '//' ){
			$postdata[] = $line;
			continue;
		}
		$ct = preg_match_all('/^#attachref/', $line, $out);
		if ( $ct ){
			for($i=0; $i < $ct; $i++){
				if ($attachref_ct++ == $attachref_no ){
					$line = preg_replace('/^#attachref(\([^(){};]*\))?(\{[^{}]*\})?/',$msg_block.'$2',$line,1);
					$skipflag = 1;
					break;
				} else {
					$line = preg_replace('/^#attachref(\([^(){};]*\))?(\{[^{}]*\})?/','#___attachref$1$2___',$line,1);
				}
			}
			$line = preg_replace('/^#___attachref(\([^(){};]*\))?(\{[^{}]*\})?___/','#attachref$1$2',$line);
//			$postdata .= "|$ct|$attachref_ct|$attachref_no|$line";
	    }
		$postdata[] = $line;
	}

	// Detect conflict of update
	if ( $wiki->digest() !== $digest ) {
		$ret['msg']  = $_attachref_messages['msg_title_collided'];
		$ret['body'] = $_attachref_messages['msg_collided'];
	}

//	$postdata .= "<hr />$refer, " . join('/',array_keys($vars)) . ", " . join("/",array_values($vars)) . ", s_args=$s_args";
//	$ret['body'] = $postdata;

	$wiki->set($postdata);

	return $ret;
}

// Show upload form
function attachref_showform()
{
	global $vars;
	global $_attachref_messages;

	$vars['page'] = $vars['refer'];
	$body = ini_get('file_uploads') ? attachref_form($vars['page']) : _('file_uploads disabled.');

	return array('msg'=>$_attachref_messages['msg_upload'], 'body'=>$body);
}

// Create html of upload form
function attachref_form($page)
{
	global $vars;
	global $_attachref_messages;

	if (!(bool)ini_get('file_uploads')) return '';

	$s_page = Utility::htmlsc($page);

	$f_digest = isset($vars['digest']) ? $vars['digest'] : '';
	$f_no = (isset($vars['attachref_no']) && is_numeric($vars['attachref_no'])) ?
		$vars['attachref_no'] + 0 : 0;

	$maxsize = ATTACHREF_MAX_FILESIZE;
	$msg_maxsize = sprintf($_attachref_messages['msg_maxsize'],number_format($maxsize/1000)."KB");
	
	$ret[] = '<form enctype="multipart/form-data" action="' . get_script_uri() . '" method="post" class="plugin-attachref-form form-inline">';
	$ret[] = '<input type="hidden" name="attachref_no" value="' . $f_no . '" />';
	$ret[] = '<input type="hidden" name="attachref_opt" value="' . $vars['attachref_opt'] . '" />';
	$ret[] = '<input type="hidden" name="digest" value="' . $f_digest . '" />';
	$ret[] = '<input type="hidden" name="max_file_size" value="' . $maxsize . '" />';
	$ret[] = '<input type="hidden" name="pcmd" value="post" />';
	$ret[] = '<input type="hidden" name="cmd" value="attachref" />';
	$ret[] = '<input type="hidden" name="refer" value="' . $s_page . '" />';
	$ret[] = '<p></p>';
	$ret[] = '<div class="form-group">';
	$ret[] = '<label for="attachref-attach-file" class="sr-only">' . $_attachref_messages['msg_file'] . ':</label>';
	$ret[] = '<input type="file" id="attachref-attach-file"  class="form-control" name="' . PLUGIN_ATTACH_FILE_FIELD_NAME . '[0]" />';
	$ret[] = '</div>';
	if (ATTACHREF_PASSWORD_REQUIRE or ATTACHREF_UPLOAD_ADMIN_ONLY) {
		$ret[] =  '<div class="form-group">' . ($_attachref_messages[ATTACHREF_UPLOAD_ADMIN_ONLY ? 'msg_adminpass' : 'msg_password'])
			 . ': <input type="password"  class="form-control" name="pass" size="8" /></div>';
	}
	$ret[] = '<input class="btn btn-primary" type="submit" value="' . $_attachref_messages['btn_upload'] . '" />';
	$ret[] = '</form>';
	$ret[] = '<ul class="plugin-attachref-ul">';
	$ret[] = '<li>' .$msg_maxsize . '</li>';
	$ret[] = '</ul>';
	return join("\n",$ret);
}
/* End of file attachref.inc.php */
/* Location: ./wiki-common/plugin/attachref.inc.php */