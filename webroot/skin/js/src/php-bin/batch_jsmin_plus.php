<?php
/*
JS-AIO-Packer - Javascript File Condenser
Copyright (C) 
	2010	   PukiWiki Advance Developers Team
	2006-2007  Matthew Glinski and XtraFile.com
-----------------------------------------------------------------
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program(LICENSE.txt); if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

######################
### Config Section ###
######################

$comment_prefix = 'PukiWiki Advance combined skin script.';

//The file name you want to give all your outputed files, eg: NAME.js, NAME.gz, NAME.php
$filePrefix = 'skin';

// Comma seperated list of files to load first, before all others. 
// Useful for jQuery, where you are most likely to load plugins that need jquery first
$loadFirst = ''; 

// The folder to load relative to this file, use '.' if you place this file in the 
// folder you want to combine.
$loadDir = '../';

// No compress file
$skip_file = 'modernizr.min.js';

############################################
######[		  WARNING!!!		  ]######
######[------------------------------]######
######[ Only edit the folloring code ]######
######[ if you know exactly what you ]######
######[		  are doing!		  ]######
############################################


####################
### Code Section ###
####################

// Load up JS-Min for PHP
require('JSMinPlus.php');
//require('jsmin.php');

//Spit out crappy processing header, v2 will look much nicer!
echo <<< HTML
<pre><code>
// JS-AIO-Packer Plus
// Pack all individual JS files in a folder into one to save bandwith and server load
// By: Matthew Glinski
// Modified by PukiWiki Advance Developers Team
################################################
################################################

HTML;

// Declare some needed global variables
$script = '';
$loaded = array();

// Preload thses files!
if($loadFirst){ loadFiles($loadFirst); }

// Load the specified folder
$temp = @opendir($loadDir);

// Skip These Files
$arr = array('index.php', 'run.php');
foreach(explode(',', $loadFirst) as $filePreLoad){
	$arr[] = $filePreLoad;
}

// Look in the folder for javascript files
while ($file = @readdir($temp)){
	if (!in_array($file,$arr) and !is_dir($loadDir . $file) and (substr($file, -3, 3) == '.js')){
		// Load Found File
		if (!in_array($file,$arr)){
			$scriptdata[] = loadFiles($file);
		}else{
			$skipped[] = loadFiles($file);
		}
	}
}
@closedir ($temp);

// Check for GZip
$runGZip = false;

// The Regular Crap, no GZip :(
$php = '<'.'?php 
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
header("Expires: " .gmdate("D, d M Y H:i:s",time() + (60 * 60)) . " GMT");
readfile("'.$filePrefix.'.js");';

// Do you haz GZip?
if(function_exists('gzdeflate'))
{
	// YAY!
	$runGZip = true;
	
	// The Gzip Inflating Magic! :D
	$php = '<'.'?php 
	header("Content-type: text/javascript; charset: UTF-8");
	header("Content-Encoding: deflate");
	header("Cache-Control: must-revalidate");
	header("Expires: " .gmdate("D, d M Y H:i:s",time() + (60 * 60)) . " GMT");
	readfile("'.$filePrefix.'.gz");';
}

$rawdata = join("\n\n",$scriptdata)."\n".join('',$skipped);;

$rawdata = '/* Generated in '.gmdate("Y\/m\/d H:i:s",time()). 'GMT */'."\n".$rawdata;
if($comment_prefix){ $rawdata = '/* '.$comment_prefix.' */'."\n".$rawdata; }

echo "\n".'Combining to '.$filePrefix.'.js...'."\n";
file_put_contents('../../'.$filePrefix.'.js', $rawdata);

// If GZip, create GZip file
if($runGZip){
	echo 'Gzip to '.$filePrefix.'.js...'."\n";
	file_put_contents('../../'.$filePrefix.'.gz', gzdeflate($rawdata, 9));
	// file_put_contents('../../'.$filePrefix.'.gz', gzencode($rawdata, 9));
}

// Create php file to load javascript
file_put_contents('../../'.$filePrefix.'.js.php', $php);

echo "-> Javascript Files combined into ".$filePrefix.".js!

################################################
################################################";
echo "</code></pre>\n";

// End Of Execution



########################
### Function Section ###
########################

/**
*  Function: loadFiles()
*  Param: $file -> The name of the file to load
*  
*  
*/
function loadFiles($file)
{
	global $script, $loaded, $loadDir;
	$arr = explode(',',$file);
	foreach($arr as $fileN)
	{
		$fileN = trim($fileN);
		if(!in_array($fileN, $loaded))
		{
			$loaded[] = $fileN;
			echo "Loaded File: ".$fileN. " -> ";
			$file = str_replace(pack("CCC",0xef,0xbb,0xbf), "", file_get_contents($loadDir.'/'.$fileN));
			
			if (!preg_match('/min\./',$fileN)){
				$result = JSMinPlus::minify($file);
//				$result = JSMin::minify($file);
				if ($result === false){
					return remove_comments($file);
				}else{
					echo "OK!\n";
					return $result;
				}
			}else{
				echo "Pass\n";
				return remove_comments($file);
			}
		}
	}
}

/**
*  Function: file_put_contents
*  PHP4 Equvliant of the PHP5 Function with the same name
*/
if(!function_exists('file_put_contents'))
{
	function file_put_contents($fileName, $data)
	{
		$fp = fopen($fileName, 'w');
		if($fp)
		{
			fwrite($fp, $data);
			fclose($fp);
		}
		else
		{
			return false;
		}
	}
}

function remove_comments($data){

	$ret = array();
	$tokens = token_get_all($data);
	foreach ($tokens as $token){
		if (is_array($token)){
			switch ($token[0]){
				case T_COMMENT:
					break;
					
				case T_DOC_COMMENT:
				default:
					$ret[] = $token[1];
					break;
			}
		}else{
			$ret[] = $token;
		}
	}
	return join("\n",$ret);
/*
	$pattern = <<<EOF
/("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\/(?!\*)(?:\\.|[^\/\\])+\/(?:[im]{0,3}|\b)(?=[^\/\\a-hj-ln-zA-Z0-9<>\*+%'"\(\{\[$_-]|[\s=|,;:?\)\}\]&]|$))|(?:\/{2,}[^\n]*|\/\*[^\*]*\*+([^\/\*][^\*]*\*+)*\/|^(?:\s*|$))/im
EOF;
	return preg_replace($pattern,'',$data);
*/
}
?>