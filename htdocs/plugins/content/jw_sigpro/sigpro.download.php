<?php
/*
// JoomlaWorks "Simple Image Gallery PRO" Plugin for Joomla! 1.5.x - Version 2.0.4
// Copyright (c) 2006 - 2009 JoomlaWorks Ltd.
// This code cannot be redistributed without permission from JoomlaWorks
// More info at http://www.joomlaworks.gr
// Designed and developed by JoomlaWorks
// ***Last update: May 4th, 2009***
*/

// Set flag that this is a parent file
define( '_JEXEC', 1 );

define( 'DS', DIRECTORY_SEPARATOR );
define('JPATH_BASE', '..'.DS.'..'.DS.'..');
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe =& JFactory::getApplication('site');
$option = JRequest::getCmd('option');

// Paths without the slash in the end
$mosConfig_absolute_path = JPATH_SITE;
$mosConfig_live_site     = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);

// define error handling
$nogo = JText::_('Sorry, download unavailable or wrong file path set!<br /><br />Please contact the administrator of this site.<br /><br /><a href="javascript:history.go(-1);">Return</a>');

if($option=="com_k2"){
	// block any attempt to explore the filesystem - check if images are included in the "media/K2/galleries" folder
	$ref = $mosConfig_live_site.'/'.substr($_GET['file'],0,strlen('media/K2/galleries/'));
	$check = $mosConfig_live_site."/media/K2/galleries/";
} else {
	// block any attempt to explore the filesystem - check if images are included in the "images" folder
	$ref = $mosConfig_live_site.'/'.substr($_GET['file'],0,strlen('images/'));
	$check = $mosConfig_live_site."/images/";	
}

if( isset($_GET['file']) && $ref===$check){
	$getfile = $_GET['file'];
} else {
	$getfile = NULL;
}

if (!$getfile) {
	// go no further if filename not set
	echo $nogo;
} else {
	// define the pathname to the file
	$filepath = $mosConfig_absolute_path.'/'.$getfile;
	// check that it exists and is readable
	if (file_exists($filepath) && is_readable($filepath)) {
		// get the file's size and send the appropriate headers
		$size = filesize($filepath);
		header('Content-Type: application/octet-stream');
		header('Content-Length: '.$size);
		header('Content-Disposition: attachment; filename="'.basename($getfile).'"');
		header('Content-Transfer-Encoding: binary');
		// open the file in binary read-only mode
		// suppress error messages if the file can't be opened
		$file = @ fopen($filepath, 'rb');
		if ($file) {
			// stream the file and exit the script when complete
			fpassthru($file);
			exit;
		} else {
			echo $nogo;
		}
	} else {
		echo $nogo;
	}
}

?>