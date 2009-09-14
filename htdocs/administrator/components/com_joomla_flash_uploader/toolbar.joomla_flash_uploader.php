<?php
/**
 * Joomla Flash uploader 2.8 Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

if ($task) {
    $act = $task;
}

switch ( $act ) {
	case "edit":
	case "newConfig":
		    TOOLBAR_joomla_flash_uploader::_EDIT();
		break;
	case "config":
	     $database = &JFactory::getDBO();
         $my =& JFactory::getUser();
         if (checkAccess($database, $my->usertype, 'backend_access_upload' )) {
		  TOOLBAR_joomla_flash_uploader::_LIST();	
		} else {
          TOOLBAR_joomla_flash_uploader::_HELP();
        }
		break;
	case "user":
		TOOLBAR_joomla_flash_uploader::_USER();	
		break;	
	case "upload":
		TOOLBAR_joomla_flash_uploader::_UPLOAD();	
		break;
	case "help":
		TOOLBAR_joomla_flash_uploader::_HELP();	
		break;
	default:
		TOOLBAR_joomla_flash_uploader::_DEFAULT();
		break;
}
?>