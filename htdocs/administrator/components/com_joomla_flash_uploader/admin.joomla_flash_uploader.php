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
define('_VALID_TWG', '42');
error_reporting(E_ALL ^ E_NOTICE);

$id = JRequest::getVar('cid', array(0) );
  if (!is_array( $id )) {
    $id = array(0);
  }

require_once(JApplicationHelper::getPath('class'));
require_once(JApplicationHelper::getPath('admin_html'));

if  ( JRequest::getVar('no_html','') != 1) {
  JFUHelper::printCss(''); // no extra path needed.
}

$skip_error_handling = "true"; // avoids that the jfu logfile is used for everything!		
$debug_file = '';
global $m;
include_once("tfu/tfu_helper.php");

$act = JRequest::getVar('act');
$task = JRequest::getVar('task');


JSubMenuHelper::addEntry(JText::_('JFU_M_1'), 'index.php?option=com_joomla_flash_uploader&act=upload');
JSubMenuHelper::addEntry(JText::_('JFU_M_2'), 'index.php?option=com_joomla_flash_uploader&act=config');
JSubMenuHelper::addEntry(JText::_('JFU_M_3'), 'index.php?option=com_joomla_flash_uploader&act=user');
JSubMenuHelper::addEntry(JText::_('JFU_M_4'), 'index.php?option=com_joomla_flash_uploader&act=help');


  if ($task) {
    $act = $task;
  }
  
  //  echo "a:" . $act . " ID " . $id[0];
  switch ($act) {
    case "upload": showUpload(); break;
    case "config": showConfig(); break;
    case "edit": showConfigUser($id[0]); break;
    case "deleteConfig": deleteConfig($id); break;
    case "newConfig": newConfig(); ; break;
    case "saveConfig": saveConfig(); break;
    case "saveMainConfig": saveMainConfig(); break;
    case "copyConfig": copyConfig($id); break;
    case "addUser": addUser(); break;
    case "deleteUser": deleteUser($id); break;
    case "cancel": cancel();; break; 
    case "register": register(); break; 
    case "dellic": deleteLicense(); break; 
    case "help": showHelpRegister() ; break;
    case "user": showUser($id); break;  
    case "createhtaccess" : createHtaccess(); break;
    case "deletehtaccess" : deleteHtaccess(); break;
    case "changeProfile" : changeProfile(); break;
    case "changeMaster" : changeMaster(); break;
    case "testFolder" : testFolder(); break;
    case "chmod777" : chmod_tfu(0777); break;
    case "chmod666" : chmod_tfu(0666); break;
    case "chmod755" : chmod_tfu(0755); break;
    case "chmod644" : chmod_tfu(0644); break;
    default: showUpload(); break;
  }
  
// we remove the JFU error handler
if ($old_error_handler) {
  set_error_handler($old_error_handler);
} else { // no other error handler set
  set_error_handler('on_error_no_output');
}

  
function checkAccess($database, $current_right, $type) {
  $current_right = strtolower($current_right); 
  $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = '".$type."'");
  $backend_access = $database->loadObjectList();
  $right = strtolower($backend_access[0]->value);
  return (($current_right == "super administrator") || ($current_right == $right) || 
          ($current_right == "administrator" && $right == "manager"));
}

function showUpload() {
  global $mainframe;
  $database = &JFactory::getDBO();
  $my =& JFactory::getUser();
  if (checkAccess($database, $my->usertype, 'backend_access_upload' )) {
      $row = new joomla_flash_uploader($database);
      $row->load(1);
      $uploadfolder = $row->folder;
      // we go back to the main folder!
      if ($uploadfolder == "") {
        $folder =  "./../../../..";
        $filefolder = "";
      } else {
        $folder =  "./../../../../" . $uploadfolder;
        $filefolder =  "./../" . $uploadfolder;
      } 
      // settings for the flash
      JFUHelper::setJFUSession($row, $folder);
      $_SESSION["TFU_FILE_CHMOD"] = JFUHelper::getVariable($database, 'file_chmod');
      $_SESSION["TFU_DIR_CHMOD"] = JFUHelper::getVariable($database, 'dir_chmod');
      
      $_SESSION["IS_ADMIN"] = "TRUE"; 
      unset($_SESSION["IS_FRONTEND"]); 
      $my = $mainframe->getUser();
      $_SESSION["TFU_USER"] = $my->username . " (backend)";
      $_SESSION["TFU_USER_ID"] = $my->id;
      store_temp_session();
      JFUHelper::fixSession();
      HTML_joomla_flash_uploader::showUpload($row, $uploadfolder, $filefolder);
  } else {
      HTML_joomla_flash_uploader::errorRights();
  }
}
  
  
/*
  Creates a new default profile
*/
function newConfig() {
	$database = &JFactory::getDBO();
		$my =& JFactory::getUser();
    if (checkAccess($database, $my->usertype, 'backend_access_config' )) {
  
	$row = new joomla_flash_uploader($database);	
	$row->config_name = "New";
	$row->folder ="";
	$row->description ="";
	$row->text_title="Titel";
	$row->text_top="Text before flash";
	$row->text_bottom="Text after flash";
	$row->text_title_lang="false";
	$row->text_top_lang="false";
	$row->text_bottom_lang="false";
	$row->maxfilesize="";
	$row->resize_show="true";
	$row->resize_data="10000,1024";
	$row->resize_label="Original,1024";
	$row->resize_default="0";
	$row->allowed_file_extensions="jpg,gif,png";
	$row->forbidden_file_extensions="true";
	$row->hide_remote_view="false";
	$row->show_delete ="true";
	$row->enable_folder_browsing ="true";
	$row->enable_folder_creation="true";
	$row->enable_folder_deletion="true";
	$row->enable_folder_rename="true";
	$row->enable_file_rename="true";
	$row->keep_file_extension="true";
	$row->enable_file_download="true";
	$row->sort_files_by_date="false";
	$row->warning_setting="all";
	$row->show_size="false";
	$row->enable_setting="true";
	$row->creation_date=date("Y-m-d");
	$row->last_modified_date=date("Y-m-d");
	$row->fix_overlay="false";
	$row->flash_title="Joomla Flash Uploader";
	$row->hide_directory_in_title="false";
	
	$row->upload_notification_email = "";
	$row->upload_notification_email_from = "";
	$row->upload_notification_email_subject = "A file was uploaded by the TWG Flash Uploader";
	$row->upload_notification_email_text = "A file was uploaded by the TWG Flash Uploader";
	$row->upload_finished_js_url = "";
	$row->preview_select_js_url = "";
	$row->delete_js_url = "";
	$row->js_change_folder = "";
	$row->directory_file_limit = "100000";
	$row->queue_file_limit = "100000";
	$row->queue_file_limit_size = "100000";
	$row->display_width = "650";
	// 2.7
	$row->enable_folder_movecopy="false";
  $row->enable_file_movecopy="false";
  $row->preview_textfile_extensions="txt,log";
  $row->edit_textfile_extensions="";
  $row->js_create_folder="";
  $row->js_rename_folder="";
  $row->js_delete_folder="";
  $row->js_copymove="";
    // 2.8
    $row->language_dropdown = 'de,en,es,br,cn,ct,da,fr,it,jp,nl,no,pl,pt,ru,se,sk,tw';
    $row->use_image_magic = "false";
    $row->image_magic_path = "convert";
    $row->exclude_directories = 'data.pxp,_vti_cnf,.svn,CVS,thumbs';
    $row->normalise_file_names = 'true';
    // reg 2.8
    $row->download_multiple_files_as_zip = 'false'; 
    $row->allowed_view_file_extensions = 'all';
    $row->forbidden_view_file_extensions = '';
    // reg prof
    $row->description_mode = 'false';
    $row->description_mode_show_default = 'true';
    $row->description_mode_store = 'email'; 
    $row->master_profile = 'false';
    $row->master_profile_mode = 'login';
    $row->master_profile_lowercase = 'true';
    // 2.8.3
    $row->normalise_directory_names = 'false';
    $row->direct_download = 'false';
    $row->fix_utf8='';
    // new 2.9
    $row->normalizeSpaces='false';
    $row->overwrite_files = 'false';
    $row->description_mode_mandatory = 'false';
    $row->show_full_url_for_selected_file = 'false';
  
	HTML_joomla_flash_uploader::showConfig($row);
	 } else {
     HTML_joomla_flash_uploader::errorRights();
  }
}
  
function deleteConfig($cid) {
global $mainframe;
   $database = &JFactory::getDBO();
   	$my =& JFactory::getUser();
    if (checkAccess($database, $my->usertype, 'backend_access_config' )) {
  
   $cids = implode( ',', $cid );
   $database->setQuery( "DELETE FROM #__joomla_flash_uploader WHERE id IN ($cids) AND id != 1" );
   $database->query();
   $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=config" );
    } else {
     HTML_joomla_flash_uploader::errorRights();
  }
} 

function saveMainConfig() {
global $mainframe;
  $database = &JFactory::getDBO();
  	$my =& JFactory::getUser();
    if (checkAccess($database, $my->usertype, 'backend_access_config' )) {
  
  $kt = JRequest::getVar('keep_tables', 'true' );
  $uj = JRequest::getVar('use_js_include', 'true' );
  $ac = JRequest::getVar('backend_access_config', 'Manager' );
  $au = JRequest::getVar('backend_access_upload', 'Manager' );
  $mo = JRequest::getVar('file_chmod', '' );
  $do = JRequest::getVar('dir_chmod', '' );
  

  $database->setQuery( "UPDATE #__joomla_flash_uploader_conf SET value='".$kt."' WHERE key_id='keep_tables'");
  $database->query();
  $database->setQuery( "UPDATE #__joomla_flash_uploader_conf SET value='".$uj."' WHERE key_id='use_js_include'");
  $database->query();
  $database->setQuery( "UPDATE #__joomla_flash_uploader_conf SET value='".$ac."' WHERE key_id='backend_access_config'");
  $database->query();
  $database->setQuery( "UPDATE #__joomla_flash_uploader_conf SET value='".$au."' WHERE key_id='backend_access_upload'");
  $database->query();
  $database->setQuery( "UPDATE #__joomla_flash_uploader_conf SET value='".$mo."' WHERE key_id='file_chmod'");
  $database->query();
  $database->setQuery( "UPDATE #__joomla_flash_uploader_conf SET value='".$do."' WHERE key_id='dir_chmod'");
  $database->query();
  cleanMessageQueue();
  $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=config", JText::_('MES_SAVED'));
   } else {
     HTML_joomla_flash_uploader::errorRights();
  }
}

function copyConfig($cid) {
global $mainframe;
  	$database = &JFactory::getDBO();
	$my =& JFactory::getUser();
    if (checkAccess($database, $my->usertype, 'backend_access_config' )) {
  	
  	if (count($cid) == 0) {
  	      cleanMessageQueue();
	  	  $mainframe->redirect("index2.php?option=com_joomla_flash_uploader&act=config", JText::_('MES_COPY_NONE'));
	  	  return;
  	}
  	if (count($cid) > 1) {
  	  cleanMessageQueue();
  	  $mainframe->redirect("index2.php?option=com_joomla_flash_uploader&act=config", JText::_('MES_COPY_ONE'));
  	  return;
  	}
  	$row = new joomla_flash_uploader($database);
    $row->load($cid[0]);
    $row->id=null;
    $row->description = "Copy of " . $row->description;
   	$row->text_title_lang="false";
	  $row->text_top_lang="false";
	  $row->text_bottom_lang="false";
    $row->last_modified_date=date("Y-m-d");
    $row->creation_date=date("Y-m-d");
    $row->store();
    cleanMessageQueue();
    $mainframe->redirect("index2.php?option=com_joomla_flash_uploader&act=config", JText::_('MES_COPY_OK'));
   } else {
     HTML_joomla_flash_uploader::errorRights();
  }
}

function changeProfile() {
global $mainframe;
    $database = &JFactory::getDBO();    
    $type =  JRequest::getVar('type','' );
    $profile =  JRequest::getVar( 'profile','' );
   	$row = new joomla_flash_uploader($database);
    $row->load($profile);
    if ($type == "enable") {
      $row->enable_setting = "true";
    } else if ($type == "disable") { // we do 2 checks to make sure that only the given values work
      $row->enable_setting = "false";
    }
    // and than we save it again.
    $row->store();
    echo "JFU:OUTPUT:1";
}

function changeMaster() {
global $mainframe;
    $database = &JFactory::getDBO();    
    $type =  JRequest::getVar('type','' );
    $profile =  JRequest::getVar( 'profile','' );
   	$row = new joomla_flash_uploader($database);
    $row->load($profile);
    if ($type == "enable") {
      $row->master_profile = "true";
    } else if ($type == "disable") { // we do 2 checks to make sure that only the given values work
      $row->master_profile = "false";
    }
    // and than we save it again.
    $row->store();
    echo "JFU:OUTPUT:1";
}

function testFolder() {
    $folder =  JRequest::getVar('folder','xxx' );
    if (endswith($folder, '/') || endswith($folder, '\\') || startswith($folder, '/') || startswith($folder, '\\')) {
       echo 'JFU:OUTPUT:0';
       return;
    }
    if (file_exists("../" . $folder)) {
      if (is_writable("../" . $folder)) {
        // We create a file - check if it exists and delete it again. Possible safemode problems can be detected!
        $testfile = "../" . $folder . '/xxx_jfu_testfile.test';
        $fh = fopen($testfile , 'w');
        fclose($fh);
        clearstatcache();
        if (file_exists($testfile)) {
          echo 'JFU:OUTPUT:1';
          @unlink($testfile);
        } else { // file could not be created
          echo 'JFU:OUTPUT:2';
        }
      } else {
        echo 'JFU:OUTPUT:2';
      }
    } else {
      echo 'JFU:OUTPUT:0';
    }   
}

function endswith( $str, $sub ) {
  return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

function startswith($Haystack, $Needle){
    // Recommended version, using strpos
    return strpos($Haystack, $Needle) === 0;
}


function cancel() {
global $mainframe;
   $mainframe->redirect( "index2.php" );
}

  
function showConfig() {
	$database = &JFactory::getDBO();
	$my =& JFactory::getUser();
    if (checkAccess($database, $my->usertype, 'backend_access_config' )) {
	$i = 0;
	$database->setQuery("SELECT * FROM #__joomla_flash_uploader where id > 0 ORDER BY id ");
    $rows = $database->loadObjectList();
	if (count($rows) > 0) {
	   foreach ($rows as $row) {
	     if ($row->id ==1) {
		        $rows[$i]->resize_label = JText::_('C_ADMINS_ONLY');
		     } else {	         
		        $database->setQuery("SELECT username FROM #__users u, #__joomla_flash_uploader_user f WHERE u.id = f.user AND f.profile =" . $row->id ." order by username");
		        $users = $database->loadObjectList("username");
		        
		        $rows[$i]->resize_label = "<a href=\"#user\" onclick=\"return submitform('user')\">";
		        
		        if (count($users) == 0) {
		           if ($rows[$i]->gid != "") {
		             $rows[$i]->resize_label .= JText::_('C_DEFAULT_PROFILE') . '</a>';  
		           } else {
                             $rows[$i]->resize_label = JText::_('C_NO_GROUP');  
		           }
		        } else {
		            $ret = array();
		            foreach ($users as $user) {
		             array_push($ret,$user->username); 
		           }
		           $rows[$i]->resize_label .= implode (", ", $ret);
		           $rows[$i]->resize_label .= "</a>"; 
		        }
         }
	     $i++;
	     }     
	}
	
  $jfu_config= array();
  $jfu_config['keep_tables']= JFUHelper::getVariable($database, 'keep_tables');
  $jfu_config['use_js_include']= JFUHelper::getVariable($database, 'use_js_include');
  $jfu_config['backend_access_upload']= JFUHelper::getVariable($database, 'backend_access_upload');
  $jfu_config['backend_access_config']= JFUHelper::getVariable($database, 'backend_access_config');
  $jfu_config['version']= JFUHelper::getVariable($database, 'version');
  $jfu_config['file_chmod']= JFUHelper::getVariable($database, 'file_chmod');
  $jfu_config['dir_chmod']= JFUHelper::getVariable($database, 'dir_chmod');
  
  HTML_joomla_flash_uploader::listConfig($rows, $jfu_config);
  } else {
     HTML_joomla_flash_uploader::errorRights();
  }
}

function showConfigUser($uid) {
	$database = &JFactory::getDBO();
	$row = new joomla_flash_uploader($database);
    $row->load($uid);
	HTML_joomla_flash_uploader::showConfig($row);
}

function showUser($id) {
$database = &JFactory::getDBO();
$my =& JFactory::getUser();
  if (checkAccess($database, $my->usertype, 'backend_access_config' )) {	
	
	$database->setQuery("SELECT u.id as myid, config_name, username FROM #__joomla_flash_uploader f, #__joomla_flash_uploader_user u, #__users us where u.user=us.id and u.profile=f.id ORDER BY u.profile,username");	
	$rows = $database->loadObjectList();
	
	// now I create the dropdowns for users and for profiles!
	  $database->setQuery("SELECT * FROM #__users u order by username");
	  $users = $database->loadObjectList();
	  $num_users = count($users);
	  if ($num_users > 10) { $num_users = 10; }
	  $data['users'] = JHTML::_('select.genericlist', $users, 'user[]', 'size="'.$num_users.'" multiple="multiple" ', 'id', 'username', 0 );  
	  $database->setQuery("SELECT * FROM #__joomla_flash_uploader WHERE id != 1");
	  $profiles = $database->loadObjectList();
	  
	  $last_profile = 0;
	  if (isset($_SESSION['LAST_PROFILE'])) {
	    $last_profile = $_SESSION['LAST_PROFILE'];
	  }
	  $data['profiles'] = JHTML::_('select.genericlist', $profiles, 'profile', 'size="1"', 'id', 'config_name', $last_profile);
	  HTML_joomla_flash_uploader::listUsers($rows, $data);
	  } else {
         HTML_joomla_flash_uploader::errorRights();
      }
}

function saveConfig() {
global $mainframe;
$database = &JFactory::getDBO();
$row = new joomla_flash_uploader($database);
// if magic quotes is on we remove slashes forst because store does quote automatically!
if(get_magic_quotes_gpc())
{
  $row->bind(array_map("stripslashes",$_POST));
} else {
  $row->bind($_POST);
}
$row->last_modified_date=date("Y-m-d");
$row->store();
cleanMessageQueue();
$mainframe->redirect("index2.php?option=com_joomla_flash_uploader&act=config", JText::_('MES_SAVED'));
}

function addUser() {
global $mainframe;
$database = &JFactory::getDBO();
$error_num = 0;
cleanMessageQueue();
if (!isset($_POST['user']) || !isset($_POST['profile'])) {
  $mainframe->redirect("index2.php?option=com_joomla_flash_uploader&act=user", JText::_('MES_MAP_NOSEL'));
}

foreach ($_POST['user'] AS $singleuser) {
   $row = new joomla_flash_uploader_user($database);
   $row->profile = $_POST['profile'];
   $row->user = $singleuser;  
   if (!$row->store()) {
     $error_num++;
   }
}
$row->bind($_POST);
$_SESSION['LAST_PROFILE'] = $row->profile;
if ($error_num > 0) {
  $mainframe->redirect("index2.php?option=com_joomla_flash_uploader&act=user", $error_num . JText::_('MES_EXISTS'));
} else {
  $mainframe->redirect("index2.php?option=com_joomla_flash_uploader&act=user", JText::_('MES_MAP_SAVED'));
}
}

function deleteUser($cid) {
global $mainframe;
   $database = &JFactory::getDBO();
   $cids = implode( ',', $cid );
   $database->setQuery( "DELETE FROM #__joomla_flash_uploader_user WHERE id IN ($cids)" );
   $database->query(); 
   cleanMessageQueue();
   $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=user", JText::_('MES_MAP_REM'));
} 

function createHtaccess() {
global $mainframe;
  $filename = dirname(__FILE__) . "/tfu/.htaccess";
  $file = fopen($filename, 'w');
  fputs($file, "SecFilterEngine Off\nSecFilterScanPOST Off");
  fclose($file);
  cleanMessageQueue();
  if (file_exists($filename)) {
     $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=upload", JText::_('MES_HTACCESS_CREATED') ); 
  }  else {
     $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=upload", JText::_('MES_HTACCESS_NOT_CREATED') );
  }
}

function deleteHtaccess() {
global $mainframe;
  $file = dirname(__FILE__) . "/tfu/.htaccess";
  @unlink($file);
  cleanMessageQueue();
  $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=upload", JText::_('MES_HTACCESS_DELETED') );
}

function showHelpRegister() {
$database = &JFactory::getDBO();
$my =& JFactory::getUser();
  if (checkAccess($database, $my->usertype, 'backend_access_config' )) {
    HTML_joomla_flash_uploader::showHelpRegister();
  } else {
    HTML_joomla_flash_uploader::errorRights();
  }
}

function deleteLicense() {
 global $mainframe;
  $file = dirname(__FILE__) . "/tfu/twg.lic.php";
  @unlink($file);
  cleanMessageQueue();
  $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=help", JText::_('MES_LICENSE_DELETED') );
}

function chmod_tfu($mode) {
  global $mainframe;
  $database = &JFactory::getDBO();
  $my =& JFactory::getUser();
  if (checkAccess($database, $my->usertype, 'backend_access_config' )) {
    chmod(dirname(__FILE__) . "/tfu/tfu_config.php",$mode);
    chmod(dirname(__FILE__) . "/tfu/tfu_login.php", $mode);
    chmod(dirname(__FILE__) . "/tfu/tfu_file.php",  $mode);
    chmod(dirname(__FILE__) . "/tfu/tfu_upload.php",$mode);
    cleanMessageQueue();
    $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=help", JText::_('H_L_CHMOD_MES') );
  } else {
    HTML_joomla_flash_uploader::errorRights();
  }  
}

function register() {
global $mainframe;
 $l =  trim(JRequest::getVar( 'l','' ));
 $d =  trim(JRequest::getVar( 'd','' ));
 $s =  trim(JRequest::getVar( 's','' ));
 
  // we remove all "
  $l = str_replace('"','',$l );
  $d = str_replace('"','',$d );
  $s = str_replace('"','',$s );
 
  $filename = dirname(__FILE__) . "/tfu/twg.lic.php";
  $file = fopen($filename, 'w');
	fputs($file, "<?php\n");
	fputs($file, "\$l=\"".$l."\";\n");
	fputs($file, "\$d=\"".$d."\";\n");
	fputs($file, "\$s=\"".$s."\";\n");
	fputs($file, "?>");	
  fclose($file);
  
  if (!file_exists($filename)) {
      $text = JText::_('MES_LICENSE_NOT_CREATED');
  } else {
      // we now check if the file can be renamed.
      $m = is_renameable();
      if ($m == "s" || $m =="w" ) {
        $text = JText::_('MES_LICENSE_WRONG');
        @unlink($filename);
      } else {
        $text = JText::_('MES_LICENSE_OK');
      } 
  } 
  cleanMessageQueue();
  $mainframe->redirect( "index2.php?option=com_joomla_flash_uploader&act=help", $text );
}

// I delete the message queue because it seems to be buggy in some versions !
function cleanMessageQueue() {
  $session =& JFactory::getSession();
  $sessionQueue = $session->get('application.queue');
  if (count($sessionQueue)) {
    $session->set('application.queue', null);
  }
}

?>