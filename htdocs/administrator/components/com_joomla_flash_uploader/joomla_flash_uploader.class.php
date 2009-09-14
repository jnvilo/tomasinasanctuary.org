<?php
/**
 * Joomla Flash uploader 2.8 Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class joomla_flash_uploader extends JTable {
    var $id = null;
    var $gid = null;
    var $config_name = null;
    var $description = null;
    var $folder = null;
    var $text_title = null;
    var $text_top = null;
    var $text_bottom = null;
    var $text_title_lang = null;
    var $text_top_lang = null;
    var $text_bottom_lang = null;
    var $maxfilesize = null;
    var $resize_show = null;
    var $resize_data = null;
    var $resize_label = null;
    var $resize_default = null;
    var $allowed_file_extensions = null;
    var $forbidden_file_extensions = null;
    var $hide_remote_view = null;
    var $show_delete = null;
    var $enable_folder_browsing = null;
    var $enable_folder_creation = null;
    var $enable_folder_deletion = null;
    var $enable_folder_rename = null;
    var $enable_file_rename = null;
    var $keep_file_extension = null;
    var $enable_file_download = null;
    var $sort_files_by_date = null;
    var $warning_setting = null;
    var $show_size = null;
    var $enable_setting = null;
    var $creation_date = null;
    var $last_modified_date = null;
    var $fix_overlay = null;
    var $flash_title = null;
    var $hide_directory_in_title = null;
    var $swf_text = null;
    var $split_extension = null;
	var $upload_notification_email = null;
	var $upload_notification_email_from = null;
	var $upload_notification_email_subject = null;
	var $upload_notification_email_text = null;
	var $upload_finished_js_url = null;
	var $preview_select_js_url = null;
	var $delete_js_url = null;
	var $js_change_folder = null;
	var $directory_file_limit = null;
	var $queue_file_limit = null;
	var $queue_file_limit_size = null;
	var $display_width = null;
	var $enable_folder_movecopy = null;
	var $enable_file_movecopy = null;
	var $preview_textfile_extensions = null;
	var $edit_textfile_extensions = null;
	var $js_create_folder= null;
  var $js_rename_folder = null;
  var $js_delete_folder = null;
  var $js_copymove = null;
  // new 2.8
  var $language_dropdown = null;
  var $use_image_magic = null;
  var $image_magic_path = null;
  var $exclude_directories = null;
  var $normalise_file_names  = null;
  var $download_multiple_files_as_zip = null;
  var $allowed_view_file_extensions  = null;
  var $forbidden_view_file_extensions  = null;
  var $description_mode = null;
  var $description_mode_show_default = null;
  var $description_mode_store  = null;
  var $master_profile  = null;
  var $master_profile_mode  = null;
  var $master_profile_lowercase = null;
  // new 2.8.3
  var $normalise_directory_names = null;
  var $direct_download = null;
  var $fix_utf8=null;
  // new 2.9
  var $overwrite_files = null;
  var $description_mode_mandatory = null;
  var $show_full_url_for_selected_file = null;
  var $normalize_spaces = null;
	
   function __construct(&$db)
	{
		parent::__construct('#__joomla_flash_uploader', 'id', $db);
    }
}

class joomla_flash_uploader_user extends JTable {
    var $id = null;
    var $profile = null;
    var $user = null;
  
    function __construct(&$db)
	   {
		   parent::__construct('#__joomla_flash_uploader_user', 'id', $db);
     }
}

class tfuHTML extends JHTML {

	function truefalseRadioList( $tag_name, $tag_attribs, $selected, $yes = false, $no = false) {
	  if (!$yes) { $yes = JText::_('C_W_YES'); }
      if (!$no)  { $no =  JText::_('C_W_NO');  }
		$arr[] = JHTML::_('select.option', 'true', $yes );
		$arr[] = JHTML::_('select.option', 'false', $no );
		return JHTML::_('select.radiolist', $arr, $tag_name, $tag_attribs,'value', 'text',$selected);
	}
	function modeRadioList( $tag_name, $tag_attribs, $selected) {
		$arr[] = JHTML::_('select.option', 'email', JText::_('C_W_EMAIL') );
		$arr[] = JHTML::_('select.option', 'text',  JText::_('C_W_TEXT'));
		return JHTML::_('select.radiolist', $arr, $tag_name, $tag_attribs,'value', 'text',$selected);
	}
	
	function mastermodeRadioList( $tag_name, $tag_attribs, $selected) {
		$arr[] = JHTML::_('select.option', 'id', JText::_('C_W_ID') );
		$arr[] = JHTML::_('select.option', 'login',  JText::_('C_W_NAME') );
		return JHTML::_('select.radiolist', $arr, $tag_name, $tag_attribs,'value', 'text',$selected);
	}
	
	function warningRadioList( $tag_name, $tag_attribs, $selected) {
			$arr = array(
			JHTML::_('select.option', 'all', JText::_('C_W_ALL') ),
			JHTML::_('select.option', 'once', JText::_('C_W_ONCE')),
			JHTML::_('select.option', 'none', JText::_('C_W_NONE')),			
			);
			return JHTML::_('select.radiolist', $arr, $tag_name, $tag_attribs,'value', 'text', $selected );
	}
	
	function downloadRadioList( $tag_name, $tag_attribs, $selected) {
			$arr = array(
			  JHTML::_('select.option', 'true', JText::_('C_W_YES') ),
			  JHTML::_('select.option', 'false', JText::_('C_W_NO') ),
			  JHTML::_('select.option', 'button1', JText::_('C_Button_1')),	
              JHTML::_('select.option', 'button',  JText::_('C_Button_2')), 				
			);
			return JHTML::_('select.radiolist', $arr, $tag_name, $tag_attribs,'value', 'text', $selected );
	}
	
	function showSizeRadioList( $tag_name, $tag_attribs, $selected) {
			$arr = array(
			JHTML::_('select.option', 'true', JText::_('C_W_YES') ),	
			JHTML::_('select.option', 'false', JText::_('C_W_NO') )
			);
			if (!$selected) { // needed for backward compability for JFU 2.5.x
              $selected = "false";
            }
			return JHTML::_('select.radiolist', $arr, $tag_name, $tag_attribs,'value', 'text', $selected );
	}
	
	function showAdminSelectBox( $tag_name, $tag_attribs, $selected) {
       $arr = array(
            JHTML::_('select.option', 'Manager', JText::_('C_W_MANAGER') ),
			JHTML::_('select.option', 'Administrator', JText::_('C_W_ADMINISTRATOR') ),	
			JHTML::_('select.option', 'Super Administrator', JText::_('C_W_SUPERADMINISTRATOR') )
			);
       return JHTML::_('Select.genericlist', $arr, $tag_name, $tag_attribs,'value', 'text', $selected );
    }	
}

class JFULanguage {
 function mapLangJoomlatoTFU($joomla) {
   $lang_arr = array ( 
  "en-GB" => "en", 
  "en-EN" => "en", 
  "en-US" => "en", 
  "de-DE" => "de",
  "de-AT" => "de", 
  "de-CH" => "de",  
  "es-ES" => "es", 
  "nl-NL" => "nl",  
  "fr-FR" => "fr", 
  "it-IT" => "it", 
  "no-NO" => "no", 
  "pt-PT" => "pt", 
  "pt-BR" => "br", 
  "zh-TW" => "tw", 
  "zh-CN" => "cn",
  "da-DK" => "da",
  "pl-PL" => "pl",
  "sk-SK" => "sk",
  "ja-JP" => "jp",
  "sv-SE" => "se",
  "ru-RU" => "ru",
  // the following are only if someone provide the proper xml ;).
  "tr-TR" => "tr",
  "fi-FI" => "fi",
  "cs-CZ" => "cz");
  if (isset($lang_arr[$joomla])) { // check if lang exists
    return $lang_arr[$joomla];
  } else {
   return "en"; // default language if an unknow lang was choosen
  }
 }
 
function getLanguage($id, $text, $prefix, $nr) {
   if ($id == "true") {
     $v = "JFU_" . $prefix . "_" . $nr;
     if (JText::_($v) != $v) {
       return JText::_($v);
     } else {
       return "Value ".$v." is not set.";
     }
   } else {
     return $text;
   }
}
}
 
class JFUHelper {
 function setJFUSession($row,$folder) {
    $_SESSION["TFU_FOLDER"] = $folder;
	$_SESSION["TFU_MAXFILESIZE"] = $row->maxfilesize;
	$_SESSION["TFU_RESIZE_SHOW"]	= $row->resize_show;
	$_SESSION["TFU_RESIZE_DATA"]	= $row->resize_data;
	$_SESSION["TFU_RESIZE_LABEL"]	= $row->resize_label;
	$_SESSION["TFU_RESIZE_DEFAULT"]	= $row->resize_default;
	$_SESSION["TFU_ALLOWED_FILE_EXTENSIONS"]	= $row->allowed_file_extensions;
	$_SESSION["TFU_FORBIDDEN_FILE_EXTENSIONS"]	= $row->forbidden_file_extensions;
	$_SESSION["TFU_HIDE_REMOTE_VIEW"]	= $row->hide_remote_view;
	$_SESSION["TFU_SHOW_DELETE"]	= $row->show_delete;
	$_SESSION["TFU_ENABLE_FOLDER_BROWSING"]	= $row->enable_folder_browsing;
	$_SESSION["TFU_ENABLE_FOLDER_CREATION"]	= $row->enable_folder_creation;
	$_SESSION["TFU_ENABLE_FOLDER_DELETION"]	= $row->enable_folder_deletion;
	$_SESSION["TFU_ENABLE_FOLDER_RENAME"]	= $row->enable_folder_rename;
	$_SESSION["TFU_ENABLE_FILE_RENAME"]	= $row->enable_file_rename;
	$_SESSION["TFU_KEEP_FILE_EXTENSION"]	= $row->keep_file_extension;
	$_SESSION["TFU_ENABLE_FILE_DOWNLOAD"]	= $row->enable_file_download;
	$_SESSION["TFU_SORT_FILES_BY_DATE"]	= $row->sort_files_by_date;
	$_SESSION["TFU_WARNING_SETTING"]	= $row->warning_setting;
	$_SESSION["TFU_SHOW_SIZE"]	= $row->show_size;
	$_SESSION["TFU_ENABLE_SETTING"]	= $row->enable_setting;
	$_SESSION["TFU_FLASH_TITLE"]	= $row->flash_title;
	$_SESSION["TFU_HIDE_DIRECTORY_IN_TITLE"]	= $row->hide_directory_in_title;  
	$_SESSION["TFU_NOT_EMAIL"]	= $row->upload_notification_email;  
	$_SESSION["TFU_NOT_EMAIL_FROM"]	= $row->upload_notification_email_from;  
	$_SESSION["TFU_NOT_EMAIL_SUBJECT"]	= $row->upload_notification_email_subject;  
	$_SESSION["TFU_NOT_EMAIL_TEXT"]	= $row->upload_notification_email_text;	
	$_SESSION["TFU_UPLOAD_FINISHED_JS_URL"]	= $row->upload_finished_js_url;
	$_SESSION["TFU_PREVIEW_SELECT_JS_URL"]	= $row->preview_select_js_url;
	$_SESSION["TFU_DELETE_JS_URL"]	= $row->delete_js_url;
	$_SESSION["TFU_JS_CHANGE_FOLDER"]	= $row->js_change_folder;	
	$_SESSION["TFU_DIRECTORY_FILE_LIMIT"] = $row->directory_file_limit;
	$_SESSION["TFU_QUEUE_FILE_LIMIT"]	= $row->queue_file_limit;
    $_SESSION["TFU_QUEUE_FILE_LIMIT_SIZE"]	= $row->queue_file_limit_size;
  // new 2.7
	$_SESSION["TFU_ENABLE_FOLDER_MOVECOPY"]	= $row->enable_folder_movecopy;
	$_SESSION["TFU_ENABLE_FILE_MOVECOPY"]	= $row->enable_file_movecopy;
	$_SESSION["TFU_PREVIEW_TEXTFILE_EXTENSIONS"]	= $row->preview_textfile_extensions;
	$_SESSION["TFU_EDIT_TEXTFILE_EXTENSIONS"]	= $row->edit_textfile_extensions;
	$_SESSION["TFU_JS_CREATE_FOLDER"]	= $row->js_create_folder;
	$_SESSION["TFU_JS_RENAME_FOLDER"]	= $row->js_rename_folder;
	$_SESSION["TFU_JS_DELETE_FOLDER"]	= $row->js_delete_folder;
	$_SESSION["TFU_JS_COPYMOVE"]	= $row->js_copymove;
	// new 2.8
	$_SESSION["TFU_LANGUAGE_DROPDOWN"] = $row->language_dropdown;
    $_SESSION["TFU_USE_IMAGE_MAGIC"] = $row->use_image_magic;
    $_SESSION["TFU_IMAGE_MAGIC_PATH"] = $row->image_magic_path;
    $_SESSION["TFU_EXCLUDE_DIRECTORIES"] = $row->exclude_directories;
    $_SESSION["TFU_NORMALISE_FILE_NAMES"] = $row->normalise_file_names;
    $_SESSION["TFU_DOWNLOAD_MULTIPLE_FILES_AS_ZIP"] = $row->download_multiple_files_as_zip;
    $_SESSION["TFU_ALLOWED_VIEW_FILE_EXTENSIONS"] = $row->allowed_view_file_extensions;
    $_SESSION["TFU_FORBIDDEN_VIEW_FILE_EXTENSIONS"] = $row->forbidden_view_file_extensions;
    $_SESSION["TFU_DESCRIPTION_MODE"] = $row->description_mode;
    $_SESSION["TFU_DESCRIPTION_MODE_SHOW_DEFAULT"] = $row->description_mode_show_default;
    $_SESSION["TFU_DESCRIPTION_MODE_STORE"] = $row->description_mode_store;
    // new 2.8.3
    $_SESSION["TFU_NORMALISE_DIRECTORY_NAMES"] = $row->normalise_directory_names;
    $_SESSION["TFU_DIRECT_DOWNLOAD"] = $row->direct_download;
    $_SESSION["TFU_FIX_UTF8"] = trim($row->fix_utf8);
    // new 2.9
    $_SESSION["TFU_OVERWRITE_FILES"] = $row->overwrite_files;
    $_SESSION["TFU_DESCRIPTION_MODE_MANDATORY"] = $row->description_mode_mandatory;
    $_SESSION["TFU_SHOW_FULL_URL_FOR_SELECTED_FILE"] = $row->show_full_url_for_selected_file;
    $_SESSION["TFU_NORMALIZE_SPACES"] = $row->normalize_spaces;
  }
  
 function getProfileId($sel_id, $id, $my) {
   $database = &JFactory::getDBO();
   if ($sel_id == 1) { // we have to find the right profile!
      // we check if we have a user with a profile mapping
      // get current user
     
      $uid = $my->id;
      $database->setQuery("SELECT j.id FROM #__joomla_flash_uploader j, #__joomla_flash_uploader_user u WHERE j.gid='" . $id . "' AND u.profile=j.id AND  u.user=" . $uid);
      $userprofile = $database->loadObjectList();
      if (count($userprofile) > 0) {
        // found
        $id = $userprofile[0]->id;
      } else {
  	  $database->setQuery("SELECT id FROM #__joomla_flash_uploader  WHERE gid = '" . $id . "'");
  	  $profiles = $database->loadObjectList();
  		if (count($profiles) == 0) {
  		  return -1;
  		} 
  		if ($id!=null) $id = 999999; // setting a value that does not exist!
  		foreach ($profiles as $profile) {
  			 // we get the default profile
  			 $database->setQuery("SELECT username FROM #__users u, #__joomla_flash_uploader_user f WHERE u.id = f.user AND f.profile =" . $profile->id);
  			 $users = $database->loadObjectList("username");
  			 if (count($users) == 0) { // this is the default profile
  				$id = $profile->id; 
  			 }
  		}	        
  	}		        
   }
   return $id;
 }
 
 function fixSession() {
  ob_start();     
  // this is a fix if session are not saved and passed to the config.php
  $HTTP_SESSION_VARS = $_SESSION;
  session_write_close();
  ini_set('session.save_handler', 'files');
  session_start();
  $_SESSION = $HTTP_SESSION_VARS; 
  session_write_close();
  ob_end_clean();
  // end fix ;).  
  }
  
function printCss($frontend = "administrator/") {
       // needed to fix path with seo
      $relative_dir = parse_url(JURI::base());
      $relative_dir = rtrim($relative_dir['path'],"\\/."); // we replace to get a consistent output with different php versions!
    if ($frontend == '') { // we are NOT w3c conform but this works all the time - and for the backend it has to work!
      echo "<link href=\"".$relative_dir."/components/com_joomla_flash_uploader/jfu.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
    } else { // w3c conform  
      echo '<script type = "text/javascript">
		<!--
		var link = document.createElement("link");
		link.setAttribute("href", "'.$relative_dir.'/components/com_joomla_flash_uploader/jfu.css");
		link.setAttribute("rel", "stylesheet");
		link.setAttribute("type", "text/css");
		var head = document.getElementsByTagName("head").item(0);
		head.appendChild(link);
		//-->
		</script>
      ';
    }
  }
  
function check_js_include($database) {
  $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'use_js_include'");
  $settings = $database->loadObjectList();
  return $settings[0]->value == 'true';
}  

function dir_copy( $source, $target ) {
    if ( is_dir( $source ) ) {
        @mkdir( $target );
        $d = dir( $source );
        while ( FALSE !== ( $entry = $d->read() ) )
        {
            if ( $entry == '.' || $entry == '..' ) { continue; }
            $Entry = $source . '/' . $entry;           
            if ( is_dir( $Entry ) ) {
                JFUHelper::dir_copy( $Entry, $target . '/' . $entry );
                continue;
            }
            copy( $Entry, $target . '/' . $entry );
        }
        $d->close();
    } else {
        copy( $source, $target );
    }
}

function getVariable($database, $variable) {
  $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = '".$variable."'");
  $result = $database->loadObjectList();
  return $result[0]->value;
}

function getlatestVersion() {
    if (isset($_SESSION['JFU_LATEST_VERSION'])) {
      return $_SESSION['JFU_LATEST_VERSION'];
    } else if ($fsock = @fsockopen('www.tinywebgallery.com', 80, $errno, $errstr, 10)) {
		$version_info = '';
        @fputs($fsock, "GET /updatecheck/jfu15.txt HTTP/1.1\r\n");
		@fputs($fsock, "HOST: www.tinywebgallery.com\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");
		$get_info = false;
		while (!@feof($fsock))
		{
			if ($get_info)
			{
				$version_info .= @fread($fsock, 1024);
			}
			else
			{
				if (@fgets($fsock, 1024) == "\r\n")
				{
					$get_info = true;
				}
			}
		}
		@fclose($fsock);
		if (!is_numeric(substr( $version_info,0,1))) {
 		  $version_info = -1;
		}
	} else {
     $version_info = -1;
    } 
	$_SESSION['JFU_LATEST_VERSION'] = $version_info;
	return $version_info;
}
} // class

?>