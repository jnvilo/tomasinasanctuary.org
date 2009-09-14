<?php
/**
 * Joomla Flash uploader 2.8.x Freeware - Plugin
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
if (!defined('_VALID_TWG')) {
  define('_VALID_TWG', '42');
}

global $mainframe;
$mainframe->registerEvent( 'onPrepareContent', 'botJoomlaFlashUploader' );

function botJoomlaFlashUploader( &$row, &$params, $page=0 ) 
{
   global $mainframe;
 
 $regex = '/\{joomla_flash_uploader.*}/';
 $regexhide = '/<!--\s*\{joomla_flash_uploader.*}\s*-->/';
 
 $plugin =& JPluginHelper::getPlugin('content', 'joomla_flash_uploader');
 $pluginParams = new JParameter( $plugin->params );
 
 // check if the plugin has been published 
 if (!$pluginParams->get( 'enabled', 1 )) {
    $row->text = preg_replace( $regex, '', $row->text );
    return true;
 }

 
 if (!file_exists("administrator/components/com_joomla_flash_uploader/joomla_flash_uploader.class.php")) {
   $mycode .= "<div style='padding:10px; margin:10px; border: 1px solid #555555;color: #000000;background-color: #f8f8f8; text-align:center; width:360px;'><b>Installation error</b><br>The Joomla Flash Uploader component can not be found. This component is required. Please install Jommla Flash Uploader first before you use this plugin.</div>";
   $row->text = preg_replace ($regex, $mycode, $row->text);
   return;
}
   $skip_error_handling = "true"; // avoids that the jfu logfile is used for everything!
   $debug_file = '';
   require_once("administrator/components/com_joomla_flash_uploader/tfu/tfu_helper.php");
   require_once("components/com_joomla_flash_uploader/joomla_flash_uploader.html.php");
   require_once("administrator/components/com_joomla_flash_uploader/joomla_flash_uploader.class.php");
  	
   JFUHelper::printCss();	
  	
	// find all instances of mambot and put in $matches
	preg_match_all( $regex, $row->text, $matches );

	// Number of mambots
 	$count = count( $matches[0] );
 	// only processes if there are any instances of the plugin in the text
   if ( $count ) {
       JPlugin::loadLanguage( 'com_joomla_flash_uploader' );
 	   preg_match ( '/{joomla_flash_uploader.*id=([\w_-]*).*}/', $row->text, $treffer ); 
 	   $id = $treffer[1];
       preg_match ( '/{joomla_flash_uploader.*type=([0,1]{1}).*}/', $row->text, $treffer ); 
 	   $selector = $treffer[1];
 	  
      echo '<!-- JFU type: \'' . $selector . '\' id: \'' .  $id . '\' -->';   
      
 	  if (isset($selector) && isset($id)) { 	  
 	      if ($selector == "0" && $id == "1") { // admin profile!
            HTML_joomla_flash_uploader::wrongId($id);
          } else {
              $user	=& JFactory::getUser();
      		  $old_error = error_reporting(0);
      		  $myId = JFUHelper::getProfileId($selector, $id, $user);
      		  error_reporting($old_error);
              if ($myId >=0) {
        		     $mycode = showFlashPlugin($myId);
        		  } else {
        		     $mycode = HTML_joomla_flash_uploader::wrongId($id, true); 
              }
          }
    } else { 
      $mycode = "<div class='errordiv'>". JText::_("ERR_PLUGIN") ."</div>";
    }
      // Replace the text
      $row->text = preg_replace ($regexhide, $mycode, $row->text);
      $row->text = preg_replace ($regex, $mycode, $row->text);
    }
    
    // we remove the JFU error handler
    if ($old_error_handler) {
      set_error_handler($old_error_handler);
    } else { // no other error handler set
      set_error_handler('on_error_no_output');
    }
}

function showFlashPlugin($id) {
	 $database =& JFactory::getDBO();
	 $row = new joomla_flash_uploader($database);
	 $row->load($id);
	 if (!$row->resize_show) { // no profile found or no id!
	    return HTML_joomla_flash_uploader::wrongId($id, true);
	 } else {
	   $uploadfolder = $row->folder;
	   $user =& JFactory::getUser();
        // we check if we have a master profile!
       if ($row->master_profile == 'true') {
	      if ($user->id != 0) {
              if ($row->master_profile_mode == 'id') {
                 $uploadfolder = $uploadfolder . '/' . $user->id;
              } else {
                  if ($row->master_profile_lowercase == 'true') {
                   $uploadfolder = $uploadfolder . '/' . strtolower($user->username);
                 } else {
                   $uploadfolder = $uploadfolder . '/' . $user->username;
                 }
              }
              // we check if the folder exists - if not it is created!
              if (!file_exists($uploadfolder) && $uploadfolder != "") {
                mkdir($uploadfolder);  
                // if the copy directory exists we copy everything!
                $extra_dir = "components/com_joomla_flash_uploader/default";
                if (file_exists($extra_dir)) {
                  JFUHelper::dir_copy($extra_dir, $uploadfolder);
                } 
              }
          } else {
              HTML_joomla_flash_uploader::noUser($id);
              return;
          }
       }
	   // we go back to the main folder!
       if ($uploadfolder == "") {
         $folder =  "./../../../..";
       } else {
         $folder =  "./../../../../" . $uploadfolder;
       }
       JFUHelper::setJFUSession($row, $folder); 
       unset($_SESSION["IS_ADMIN"]);
       $_SESSION["IS_FRONTEND"] = "TRUE";
       if ($user->id != 0) {
         $_SESSION["TFU_USER"] = $user->username;
         $_SESSION["TFU_USER_ID"] = $user->id;
       } else {
         unset($_SESSION["TFU_USER"]);
         unset($_SESSION["TFU_USER_ID"]);
       }
       
        // we check if the flash should be included with js oder the object tag
       $use_js_include = JFUHelper::check_js_include($database);
       
       store_temp_session();
       JFUHelper::fixSession();
       return  HTML_joomla_flash_uploader::showFlash( $row, $uploadfolder, $use_js_include, true );
	 }
}

?>