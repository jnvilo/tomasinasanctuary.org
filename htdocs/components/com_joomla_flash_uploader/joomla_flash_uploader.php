<?php
/**
 * Joomla Flash uploader 2.8.x Freeware - for Joomla 1.0.x
 *
 * Copyright (c) 2004-2007 TinyWebGallery
 * written by Michael Dempfle
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
define('_VALID_TWG', '42');

// session_start();
global $Itemid,$m,$mainframe;

// Parameters
// $params = $mainframe->getParams('com_joomla_flash_uploader');
$params = &JComponentHelper::getParams( 'com_joomla_flash_uploader' );
$id  = $params->get( 'tfu_id','');
$sel_id = $params->get( 'tfu_sel_id','');

require_once(JApplicationHelper::getPath( 'front_html' ) );
require_once(JApplicationHelper::getPath('class'));
$skip_error_handling = "true"; // avoids that the jfu logfile is used for everything!	
$debug_file = '';
include_once("administrator/components/com_joomla_flash_uploader/tfu/tfu_helper.php");
 
JFUHelper::printCss();

 // The administrator profile was selected and because of security issue it is not allowed to use this profile in the frontend. If you really like to use a profile that has access to the full installation please create a new profile and set the folder like in the administration profile.
$my =& JFactory::getUser();
$_SESSION["TFU_USER"] = $my->username;

echo '<!-- JFU sel_id: \'' . $sel_id . '\' id: \'' .  $id . '\' -->'; 

if ($sel_id == "0" && $id == "1") { // admin profile!
  HTML_joomla_flash_uploader::wrongId($id);
} else {
  $myId = JFUHelper::getProfileId($sel_id, $id, $my);
  if ($myId >=0) {
    showFlashComponent($myId);
  } else {
    HTML_joomla_flash_uploader::wrongId($id); 
  }
}

// we remove the JFU error handler
if ($old_error_handler) {
  set_error_handler($old_error_handler);
} else { // no other error handler set
  set_error_handler('on_error_no_output');
}

function showFlashComponent($id) {
     $database = &JFactory::getDBO();
	 $row = new joomla_flash_uploader($database);
	 $row->load($id);
	 if (!$row->resize_show) { // no profile found or no id!
	    HTML_joomla_flash_uploader::wrongId($id);
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
       
       // we go back to the main folder! path has to be relativ to the tfu upload folder!
       if ($uploadfolder == "") {
         $folder =  "./../../../..";
       } else {
         $folder =  "./../../../../" . $uploadfolder;
       }
       JFUHelper::setJFUSession($row, $folder); 
       $_SESSION["TFU_FILE_CHMOD"] = JFUHelper::getVariable($database, 'file_chmod');
       $_SESSION["TFU_DIR_CHMOD"] = JFUHelper::getVariable($database, 'dir_chmod'); 
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
       HTML_joomla_flash_uploader::showFlash( $row, $uploadfolder, $use_js_include, false);
	 }
}

?>