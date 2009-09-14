<?php
/**
* @version		1.0 RokGZipper
* @package		RokGZipper
* @copyright	Copyright (C) 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filesystem.file' );

/**
 * RokGZipper analytics plugin
 *
 * @author		Andy Miller
 * @package		RokGZipper
 * @subpackage	System
 */
class  plgSystemRokGZipper extends JPlugin
{

	function plgSystemRokGZipper(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
	
	function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }


	function onAfterRender()
	{
		global $mainframe;
		
		if ($mainframe->isAdmin()) return;
		
		$doc =& JFactory::getDocument();

        	
		$stylesheets = array_keys($doc->_styleSheets);
		$scripts = array_keys($doc->_scripts);
		
		plgSystemRokGZipper::processJsFiles($scripts);
		plgSystemRokGZipper::processCSSFiles($stylesheets);

   
	}

    function processCSSFiles($filelist) {
		
		$uri	    =& JURI::getInstance();
		$base	    = $uri->toString( array('scheme', 'host', 'port'));
		$path       = JURI::root(true);
        $cache_time = $this->params->get("cache_time",60);
        $body       = JResponse::getBody();
        
        $ordered_files = array();
        $output = array();
        
        foreach ($filelist as $file) {
            $filepath = plgSystemRokGZipper::getFilePath($file,$base,$path);
            $ordered_files[dirname($filepath)][basename($filepath)] = $file;
		} 
		
		foreach ($ordered_files as $dir=>$files) {
		    
		    $md5sum = "";
		    $path = "";
		    
		    //first trip through to build filename
		    foreach ($files as $file=>$details) {
		        $md5sum .= md5($details);
		        $detailspath = $dir.DS.$file;
		        if (JFile::exists($detailspath)) {
	                $link = "<link rel=\"stylesheet\" href=\"".$details."\" type=\"text/css\" />\n";
    		        $body = str_replace($link,"",$body);
    		        $path = dirname($details);
		        }
		    }
		    
		    $cache_filename = "css-".md5($md5sum).".php";
		    $cache_fullpath = $dir.DS.$cache_filename;
		    
		    //see if file is stale
		    if (JFile::exists($cache_fullpath)) {
    		    $diff = (time()-filectime($cache_fullpath));
    		} else {
    		    $diff = $cache_time+1;
    		} 

    		if ($diff > $cache_time){
		        $outfile = plgSystemRokGZipper::getOutHeader("css");
    		    foreach ($files as $file=>$details) {
    		        $detailspath = $dir.DS.$file;
    		        
    		        if (JFile::exists($detailspath)) {
                        $outfile .= "\n\n/*** " . $detailspath . " ***/\n\n" . JFile::read($detailspath); 
                    } 
    		    }
                JFile::write($cache_fullpath,$outfile);
    		}
		    $output[] = "<link rel=\"stylesheet\" href=\"".$path."/".$cache_filename."\" type=\"text/css\" />";
		    
		}
		
		foreach ($output as $line) {
		    $body = str_replace('<head>',"<head>\n".$line."\n", $body);  
		}
		
		JResponse::setBody($body);
        
    }
    
    function processJsFiles($filelist) {
		
		$uri	    =& JURI::getInstance();
		$base	    = $uri->toString( array('scheme', 'host', 'port'));
		$path       = JURI::Root(true);
        $cache_time = $this->params->get("cache_time",60);
        $body       = JResponse::getBody();
        
        $ordered_files = array();
        $output = array();
        $md5sum = "";
        
        foreach ($filelist as $file) {
            $filepath = plgSystemRokGZipper::getFilePath($file,$base,$path);

            $ordered_files[] = array(dirname($filepath),basename($filepath),$file);
		} 

		foreach ($ordered_files as $files) {

		    $dir = $files[0];
		    $filename = $files[1];
		    $details = $files[2];

	        $md5sum .= md5($filename);
	        $detailspath = $dir.DS.$filename;
	        
	        if (JFile::exists($detailspath)) {
                $link = "<script type=\"text/javascript\" src=\"".$details."\"></script>\n";
    	        $body = str_replace($link,"",$body);
	        }
		    
		}
		
		$cache_filename = "js-".md5($md5sum).".php";
	    $cache_fullpath = JPATH_CACHE.DS.$cache_filename;

	    //see if file is stale
	    if (JFile::exists($cache_fullpath)) {
		    $diff = (time()-filectime($cache_fullpath));
		} else {
		    $diff = $cache_time+1;
		} 

		if ($diff > $cache_time){
	        $outfile = plgSystemRokGZipper::getOutHeader("js");
		    foreach ($ordered_files as $files) {
		        $dir = $files[0];
    		    $filename = $files[1];
    		    $details = $files[2];
		        
		        $detailspath = $dir.DS.$filename;
		        if (JFile::exists($detailspath)) {
		            $jsfile = JFile::read($detailspath);
		            // fix for stupid joolma code
		            if (strpos($filename,'joomla.javascript.js')!==false or strpos($filename,'mambojavascript.js')!==false) {
		                $jsfile = str_replace("// <?php !!", "// ", $jsfile);
		            }
                    $outfile .= "\n\n/*** " . $detailspath . " ***/\n\n" . $jsfile; 
                    
                    
                } 
		    }
            JFile::write($cache_fullpath,$outfile);
		}

		
	    $output[] = "<script type=\"text/javascript\" src=\"".$path."/cache/".$cache_filename."\"></script>\n";
		foreach ($output as $line) {
		    $body = str_replace('<head>',"<head>\n".$line."\n", $body);  
		}
		
		JResponse::setBody($body);
        
    }    
	

	
	
	function getFilePath($url,$base,$path) {
	    
	    if ($url && $base && strpos($url,$base)!==false) $url = str_replace($base,"",$url);
	    if ($url && $path && strpos($url,$path)!==false) $url = str_replace($path,"",$url);
	    $filepath = JPATH_SITE.$url;
	    return $filepath;    
	}
	
	
	function getOutHeader($type="css") {
	    if ($type=="css") {
    	    $header='<?php 
ob_start ("ob_gzhandler");
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " . 
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
                ?>';
        } else {
            $header='<?php 
ob_start ("ob_gzhandler");
header("Content-type: application/x-javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " . 
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
                ?>';
        }
        return $header;
	}
}