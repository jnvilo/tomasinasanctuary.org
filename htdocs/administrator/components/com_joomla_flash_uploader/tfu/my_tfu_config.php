<?php
/**
 * Joomla Flash uploader 2.9 Freeware - for Joomla 1.0.x and Joomla 1.5.x - based on TWG Flash uploader 2.8
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 * 
 *  This is the config file where sll the JFU stuff from the wrapper is set.
 *  Since 2.8 99% of the TFU addoptions needed to use TFU for JFU are in this file! 
 *  
 *  The commented settings cannot be set by the backend - if you want to set them you 
 *  have to uncomment it an set it    
 * 
 *   Have fun using JFU
 */
/** ensure this file is being included by a parent file */
defined( '_VALID_TWG' ) or die( 'Direct Access to this location is not allowed.' );

/*
    Joomla related settings
*/
$joomla_config =  dirname(__FILE__) . "/../../../../configuration.php";
if (file_exists($joomla_config)) {
  include $joomla_config; 
}

if (isset($_SESSION["IS_ADMIN"])) {
  $joomla_path = "components/com_joomla_flash_uploader/tfu/";
} else if (isset($_SESSION["IS_FRONTEND"])) {
  $joomla_path = "administrator/components/com_joomla_flash_uploader/tfu/";
} else {
  debug("Illegal direct access or missing session settings - browser has to be closed to get a new session: " .   session_name() . "=". session_id() );
  return;
}

/*
    TFU CONFIGURATION
*/

$login = "true"; // The login flag - has to set by yourself below "true" is logged in, "auth" shows the login form, "reauth" should be set if the authentification has failed. "false" if the flash should be disabled.  
$folder = $_SESSION["TFU_FOLDER"]; // this is the root upload folder. 

$maxfilesize = ($_SESSION["TFU_MAXFILESIZE"] !="") ?  ((getMaximumUploadSize() > $_SESSION["TFU_MAXFILESIZE"]) ? $_SESSION["TFU_MAXFILESIZE"] : getMaximumUploadSize()) : getMaximumUploadSize();
$resize_show = ($_SESSION["TFU_RESIZE_SHOW"] =="true") ? is_gd_version_min_20() : "false";
$resize_data = $_SESSION["TFU_RESIZE_DATA"];  
$resize_label = $_SESSION["TFU_RESIZE_LABEL"]; 
$resize_default = $_SESSION["TFU_RESIZE_DEFAULT"];             
$allowed_file_extensions = $_SESSION["TFU_ALLOWED_FILE_EXTENSIONS"]; 
$forbidden_file_extensions = $_SESSION["TFU_FORBIDDEN_FILE_EXTENSIONS"]; 
// Enhanced features - this are only defaults! if TFU detects that this is not possible this functions are disabled! 
$hide_remote_view = ($_SESSION["TFU_HIDE_REMOTE_VIEW"] == "true") ? 'true' : '';
//    $show_preview = is_gd_version_min_20(); // Show the small preview. Valid is 'true' and 'false' (Strings!) - the function is_gd_version_min_20 checks if the minimum requirements for resizing images are there!
//    $show_big_preview = 'true'; // Show the big preview - clicking on the preview image shows a bigger preview
$show_delete = $_SESSION["TFU_SHOW_DELETE"];              
$enable_folder_browsing = $_SESSION["TFU_ENABLE_FOLDER_BROWSING"];
$enable_folder_creation = $_SESSION["TFU_ENABLE_FOLDER_CREATION"]; 
$enable_folder_deletion = $_SESSION["TFU_ENABLE_FOLDER_DELETION"];  
$enable_folder_rename = $_SESSION["TFU_ENABLE_FOLDER_RENAME"];      
$enable_file_rename = $_SESSION["TFU_ENABLE_FILE_RENAME"];    
$keep_file_extension =$_SESSION["TFU_KEEP_FILE_EXTENSION"];      
// $timezone - This setting can be found at the top of tfu_helper.php
    
      // some optional things
//    $login_text = ''; // e.g. 'Please login';  // Login Text
//    $relogin_text = ''; // e.g. 'Wrong Username/Password. Please retry'; // Retry login text
//    $upload_file = 'tfu_upload.php'; // Upload php file - this is relative to he flash
$base_dir = $joomla_path;              // this is the base dir of the other files - tfu_read_Dir, tfu_file and the lang folder. since 2.6 there are no seperate settings for tfu_readDir and tfu_file anymore because it's actually not needed.
$sort_files_by_date = ($_SESSION["TFU_SORT_FILES_BY_DATE"] == "true");
$warning_setting = $_SESSION["TFU_WARNING_SETTING"]; // the warning is shown if remote files do already exist - can be set to all,once,none
// $split_extension = 'FALSE'; // This is the extension when you upload splitted files - tfu can merge them after upload. A splited file has to ge like: file.extension.part1, file.extension.part2 ... - the file extension cannot be empty - if emptpy the default is part! to disable splited uploads use 'FALSE';
$show_size = ($_SESSION["TFU_SHOW_SIZE"] == 'true') ? 'true' : '';
$hide_directory_in_title=$_SESSION["TFU_HIDE_DIRECTORY_IN_TITLE"];       // You can disalbe the display of the upload dir in the title bar if you set this to true

// the text of the email is stored in the tfu_upload.php if you like to change it :)
$upload_notification_email = $_SESSION["TFU_NOT_EMAIL"];
$upload_notification_email_from = $_SESSION["TFU_NOT_EMAIL_FROM"];
$upload_notification_email_subject = $_SESSION["TFU_NOT_EMAIL_SUBJECT"];
$upload_notification_email_text = $_SESSION["TFU_NOT_EMAIL_TEXT"];

//    $keep_internal_session_handling = false;  // new 2.7.5 - TFU can detect servers with session problems. And it removes the session_cache folder it it is not needed. If you set this to true the session_Cache folder is not removed automatically. You should set this to true if you have only sometimes problems with the upload!
/**
 * Extra settings for the registered version
 */
$titel = $_SESSION["TFU_FLASH_TITLE"];
//    $remote_label = ''; // 'Remote' This is a optional setting - you can change the display string above the file list if you want to use a different header - can only be changed in the registered version! - if you want to have a & you have to urlencode the string!
//    $preview_label = ''; // 'Preview' This is a optional setting - you can change the display string of the header if you don't use the preview but maybe this function to determine the selection in the remote file list - can only be changed in the registered version!  - if you want to have a & you have to urlencode the & !
$upload_finished_js_url = $_SESSION["TFU_UPLOAD_FINISHED_JS_URL"]; 
$preview_select_js_url = $_SESSION["TFU_PREVIEW_SELECT_JS_URL"]; 
$delete_js_url = $_SESSION["TFU_DELETE_JS_URL"];  
$js_change_folder = $_SESSION["TFU_JS_CHANGE_FOLDER"]; 
$js_create_folder = $_SESSION["TFU_JS_CREATE_FOLDER"];            
$js_rename_folder = $_SESSION["TFU_JS_RENAME_FOLDER"];          
$js_delete_folder = $_SESSION["TFU_JS_DELETE_FOLDER"];               
$js_copymove      = $_SESSION["TFU_JS_COPYMOVE"];
$show_full_url_for_selected_file = $_SESSION["TFU_SHOW_FULL_URL_FOR_SELECTED_FILE"];
$directory_file_limit = $_SESSION["TFU_DIRECTORY_FILE_LIMIT"]; 
$queue_file_limit = $_SESSION["TFU_QUEUE_FILE_LIMIT"]; 
$queue_file_limit_size = $_SESSION["TFU_QUEUE_FILE_LIMIT_SIZE"]; 
//    $hide_help_button = 'true'; // since TFU 2.5 it is possible to turn off the ? (no extra flash like before is needed anymore!) - it is triggered now by the license file! professional licenses and above and old licenses that contain a TWG_NO_ABOUT in the domain (=license for 20 Euro) enable that this switch is read - possible settings are 'true' and 'false'
$enable_file_download = $_SESSION["TFU_ENABLE_FILE_DOWNLOAD"];   
$enable_folder_move=$_SESSION["TFU_ENABLE_FOLDER_MOVECOPY"];       
$enable_file_copymove=$_SESSION["TFU_ENABLE_FILE_MOVECOPY"];        
$preview_textfile_extensions = $_SESSION["TFU_PREVIEW_TEXTFILE_EXTENSIONS"]; 
$edit_textfile_extensions = $_SESSION["TFU_EDIT_TEXTFILE_EXTENSIONS"];  

// new Joomla settings for 2.8 - some where not in the config before some are new from TFU 2.8
$language_dropdown = $_SESSION["TFU_LANGUAGE_DROPDOWN"];
$use_image_magic = ($_SESSION["TFU_USE_IMAGE_MAGIC"] == "true");
$image_magic_path = $_SESSION["TFU_IMAGE_MAGIC_PATH"];
$exclude_directories = array_map("trim", explode(",", $_SESSION["TFU_EXCLUDE_DIRECTORIES"])); // we need an array here and trim spaces too.
$normalise_file_names = ($_SESSION["TFU_NORMALISE_FILE_NAMES"] == "true");
$download_multiple_files_as_zip = $_SESSION["TFU_DOWNLOAD_MULTIPLE_FILES_AS_ZIP"];
$allowed_view_file_extensions = $_SESSION["TFU_ALLOWED_VIEW_FILE_EXTENSIONS"];
$forbidden_view_file_extensions = $_SESSION["TFU_FORBIDDEN_VIEW_FILE_EXTENSIONS"];
$description_mode = $_SESSION["TFU_DESCRIPTION_MODE"];
$description_mode_show_default = $_SESSION["TFU_DESCRIPTION_MODE_SHOW_DEFAULT"];
$description_mode_store = $_SESSION["TFU_DESCRIPTION_MODE_STORE"]; 
// new 2.8.3
$normalise_directory_names = ($_SESSION["TFU_NORMALISE_DIRECTORY_NAMES"]  == "true");
$direct_download = $_SESSION["TFU_DIRECT_DOWNLOAD"];
$fix_utf8 = $_SESSION["TFU_FIX_UTF8"];
// new 2.9
$overwrite_files= $_SESSION["TFU_OVERWRITE_FILES"];
$description_mode_mandatory = $_SESSION["TFU_DESCRIPTION_MODE_MANDATORY"];
$normalizeSpaces = $_SESSION["TFU_NORMALIZE_SPACES"];
$file_chmod=($_SESSION["TFU_FILE_CHMOD"] == '') ? 0 : octdec($_SESSION["TFU_FILE_CHMOD"]);
$dir_chmod=($_SESSION["TFU_DIR_CHMOD"] == '') ? 0 : octdec($_SESSION["TFU_DIR_CHMOD"]);

$zip_folder = $folder; // has to be set again!

?>