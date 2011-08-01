<?php
/*!
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 *
 * JavaScript auto marger and auto YUI compressor.
 * Copyright (c) 2010-2011 PukiWiki Advance Developer Team
 *
 * Based on JS-AIO-Packer - Javascript File Condenser
 * Copyright (c) 2006-2007 Matthew Glinski and XtraFile.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//The file name you want to give all your outputed files, eg: NAME.js, NAME.gz, NAME.php
$output_filename = 'skin';

$output_path = '../../';

// Comma seperated list of files to load first, before all others. 
// Useful for jQuery, where you are most likely to load plugins that need jquery first
$loadFirst = ''; 

// The folder to load relative to this file, use '.' if you place this file in the 
// folder you want to combine.
$loadDir = '../';

// show log?
$verbose     = true;

/**************************************************************************************************/

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

// Look in the folder for javascript files
while ($file = @readdir($temp)){
	if (!in_array($file,$arr) and !is_dir('./' . $file)){
		if (substr($file, -3, 3) == '.js'){
			// Load Found File
			if (!in_array($file,$arr)){
				$script[] = loadFiles($file);
			}
		}
	}
}
@closedir ($temp);

$js = join("\n",$script);
file_put_contents('./temp.js', $js);

unlink($output_path.$output_filename.'.js');

$command = 'java -jar yuicompressor-2.4.6.jar -v --charset "utf-8" -o '.$output_path.$output_filename.'.js temp.js';
if ($verbose) {
	echo "Execute YUI Compressor...\n";
	echo "command: {$command}\n\n";
}

if (isWindows()) {
	$fp = fopen('mini.bat', 'w'); // or die

	fwrite($fp, $command . "\n");
	if ($verbose) {
		fwrite($fp, "pause\n");
	}
	fwrite($fp, "exit\n");
	fclose($fp);

	shell_exec('start mini.bat');

	unlink('mini.bat');
} else {
	shell_exec($command);
}
unlink('temp.js');

// Do you haz GZip?
if(function_exists('gzdeflate'))
{
	if ($verbose) {
		echo "Compressing...\n";
	}
	$compressed = file_get_contents($output_path.$output_filename.'.js');
	$filename = $output_filename.'.gz';
	
	// The Gzip Inflating Magic! :D
	$php = '<'.'?php 
header("Content-type: text/javascript; charset: UTF-8");
header("Content-Encoding: deflate");
header("Content-Length: " . filesize("'.$filename.'"));
header("Cache-Control: must-revalidate");
header("Expires: " .gmdate("D, d M Y H:i:s",time() + (60 * 60)) . " GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime("'.$filename.'"))." GMT");
readfile("'.$filename.'");';
	if ($verbose) {
		echo "Finish.\n";
	}
	file_put_contents($output_path.$output_filename.'.gz', gzdeflate($compressed, 9));
}else{
	$filename = $output_filename.'.js';
	// The Regular Crap, no GZip :(
	$php = '<'.'?php
header("Content-type: text/javascript; charset: UTF-8");
header("Content-Length: " . filesize("'.$filename.'"));
header("Cache-Control: must-revalidate");
header("Expires: " .gmdate("D, d M Y H:i:s",time() + (60 * 60)) . " GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime("'.$filename.'"))." GMT");
readfile("'.$filename.'");';
	if ($verbose) {
		echo "Finish.\n";
	}
}

// Create php file to load javascript
file_put_contents($output_path.$output_filename.'.js.php', $php);

/**************************************************************************************************/
function loadFiles($file)
{
	global $script, $loaded, $loadDir, $verbose;
	$arr = explode(',',$file);
	foreach($arr as $fileN)
	{
		$fileN = trim($fileN);
		if(!in_array($fileN, $loaded))
		{
			$loaded[] = $fileN;
			if ($verbose) {
				echo "-> Loaded File: ".$fileN."\n";
			}
			return file_get_contents($loadDir.'/'.$fileN)."\n\n\n";
		}
	}
}

function isWindows() { // @return Boolean:
    return substr(PHP_OS, 0, 3) == 'WIN';
}
?>