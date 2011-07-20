<?php
/*
JS-AIO-Packer - Javascript File Condenser
Copyright (C) 2006-2007  Matthew Glinski and XtraFile.com
Link: http://www.xtrafile.com/JS-AIO-Packer
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
######[          WARNING!!!          ]######
######[------------------------------]######
######[ Only edit the folloring code ]######
######[ if you know exactly what you ]######
######[          are doing!          ]######
############################################


####################
### Code Section ###
####################

// Load up JS-Min for PHP
require('jsmin.php');

//Spit out crappy processing header, v2 will look much nicer!
echo "<pre><code>// JS-AIO-Packer \n// Pack all individual JS files in a folder into one to save bandwith and server load\n// By: Matthew Glinski\n################################################
################################################
";

// Declare some needed global variables
$script = $skipped = array();
$loaded = array();

// Preload thses files!
if($loadFirst){ loadFiles($loadFirst); }

// Load the specified folder
$temp = @opendir($loadDir);

// Skip These Files
$arr = array('index.php', 'run.php');
foreach(explode(',', $loadFirst) as $filePreLoad)
{
	$arr[] = $filePreLoad;
}

foreach(explode(',', $skip_file) as $filePreLoad)
{
	$arr2[] = $filePreLoad;
}

// Look in the folder for javascript files
while ($file = @readdir($temp)){
	if (!in_array($file,$arr) and !is_dir('./' . $file)){
		if (substr($file, -6, 6) == 'min.js'){
			if (!in_array($file,$arr2)){
				$skipped[] = token_get_all(loadFiles($file));
			}
		} else if (substr($file, -3, 3) == '.js'){
			// Load Found File
			if (!in_array($file,$arr2)){
				$script[] = loadFiles($file);
			}
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

$javascript = join("\n",$skipped);
$javascript .= JSMin::minify(join("\n",$script));

// Output a minified version of the js file.
file_put_contents('../../'.$filePrefix.'.js', $javascript);

// If GZip, create GZip file
if($runGZip){
	file_put_contents('../../'.$filePrefix.'.gz', gzdeflate($javascript, 9));
}

// Create php file to load javascript
file_put_contents('../../'.$filePrefix.'.js.php', $php);


echo "-> Javascript Files combined into ".$filePrefix.".js!
################################################
################################################";
echo "</code></pre>";

// End Of Execution



########################
### Function Section ###
########################

/**
*  Function: loadFiles()
*  Param: $file -> The name of the file to load
*  
*  Return: void()
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
			echo "-> Loaded File: ".$fileN."\n";
			return file_get_contents($loadDir.'/'.$fileN)."\n\n\n";
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
?>