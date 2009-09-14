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

// load the language file for the install
$params   = JComponentHelper::getParams('com_languages');
$backend_lang = $params->get('admin', 'en-GB');
$language = JLanguage::getInstance($backend_lang);
$language = &JFactory::getLanguage();
$language->load('com_joomla_flash_uploader');

function com_install() { 
  $database = &JFactory::getDBO();
  $cur_version = '2.9.1';
  
  $error=false;
  echo JText::_('I_CHECK');
  
  // we check if we have a previous version
  $database->setQuery("CREATE TABLE IF NOT EXISTS  #__joomla_flash_uploader_conf (key_id TEXT, value TEXT)");
  if(!$database->query()) echo $database->getErrorMsg().'<br />';
  $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'version'");
  $version = $database->loadObjectList();
  // end version check 
  if(!$version) { // tables do not exist
	echo  JText::_('I_CHECK_NONE');
    install_db($cur_version); // we install basic version.
    $error = update_db(true); // and then we update to current version.
 } else {
    $ver = $version[0]->value;
    if ($ver == '' || version_compare($ver,$cur_version, "<")) { 
      echo JText::_('I_CHECK_OLD_FOUND') . $ver . JText::_('I_CHECK_OLD_FOUND_2');
      $error = update_db(false);
      // we update old settings of the table because the default of the flash has changed after 2.7.4
      $database->setQuery("UPDATE #__joomla_flash_uploader SET allowed_file_extensions='jpg,jpeg,gif,png' WHERE allowed_file_extensions=''");
      if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }   
    } else {
      echo JText::_('I_CHECK_FOUND');
    }
 }
  
  if (!$error) {
       // we update the version of the db table
	  $database->setQuery("UPDATE #__joomla_flash_uploader_conf SET value='".$cur_version."' WHERE key_id='version'");
	  if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
  
      // we try to the license now
       $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'user'");
       $user = $database->loadObjectList();
       if (count($user) > 0) {
         $us = $user[0]->value;
         $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'domain'");
         $domain = $database->loadObjectList();
         $do = $domain[0]->value;
         $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'code'");
         $code = $database->loadObjectList();
         $co = $code[0]->value;
         // write twg.lic.twg !
         $filename = dirname(__FILE__) . "/tfu/twg.lic.php";
          $file = fopen($filename, 'w');
        	fputs($file, "<?php\n");
        	fputs($file, "\$l=\"".$us."\";\n");
        	fputs($file, "\$d=\"".$do."\";\n");
        	fputs($file, "\$s=\"".$co."\";\n");
        	fputs($file, "?>");	
          fclose($file);
       }
      echo "&nbsp;<br />";
      echo "<div style='text-align:left;'>";
      echo JText::_('I_TEXT');
      echo JText::_('I_DESC'); 
      echo "</div>";
    }
  }
  
  function install_db($cur_version){
  $database = &JFactory::getDBO();
     
	  $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader");
	  $database->query();
	  $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader_user");
	  $database->query();
	  $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader_conf");
	  $database->query();
	  
	  $database->setQuery("CREATE TABLE #__joomla_flash_uploader_user ( id INT NOT NULL AUTO_INCREMENT, 
	  profile INT,
	  user INT,
	  PRIMARY KEY (id),
	  UNIQUE (profile,user)
	  )");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	   
	  $database->setQuery("CREATE TABLE #__joomla_flash_uploader (
	              id INT NOT NULL AUTO_INCREMENT,gid TEXT NOT NULL,
	              config_name TEXT NOT NULL,folder TEXT NOT NULL DEFAULT '',
	              description TEXT NOT NULL DEFAULT '',text_title TEXT DEFAULT '',
	              text_title_lang TEXT DEFAULT '',text_top TEXT DEFAULT '',
	  			  text_top_lang TEXT DEFAULT '',text_bottom TEXT DEFAULT '',
	              text_bottom_lang TEXT DEFAULT '',maxfilesize TEXT NOT NULL,
	              resize_show TEXT NOT NULL,resize_data TEXT ,
	              resize_label TEXT ,resize_default TEXT ,
	              allowed_file_extensions TEXT,forbidden_file_extensions TEXT,
	              hide_remote_view TEXT DEFAULT '',show_delete TEXT NOT NULL,
	              enable_folder_browsing TEXT NOT NULL,enable_folder_creation TEXT NOT NULL,
	              enable_folder_deletion TEXT NOT NULL,enable_folder_rename TEXT NOT NULL,
	              enable_file_rename TEXT NOT NULL,keep_file_extension TEXT NOT NULL,
	              enable_file_download TEXT NOT NULL,sort_files_by_date TEXT NOT NULL,
	              warning_setting TEXT NOT NULL,show_size TEXT DEFAULT '',
	              enable_setting TEXT NOT NULL,creation_date DATE NOT NULL,
	              last_modified_date DATE NOT NULL,fix_overlay TEXT NOT NULL,
	              flash_title TEXT NOT NULL, hide_directory_in_title TEXT NOT NULL, 
	              swf_text TEXT, split_extension TEXT,
	              upload_notification_email TEXT,upload_notification_email_from TEXT,
	              upload_notification_email_subject TEXT,upload_notification_email_text TEXT,
	              upload_finished_js_url TEXT,preview_select_js_url TEXT,
	              delete_js_url TEXT,js_change_folder TEXT,
	              directory_file_limit TEXT,queue_file_limit TEXT,
	              queue_file_limit_size TEXT,display_width TEXT,
	              enable_folder_movecopy TEXT, enable_file_movecopy TEXT,
	              preview_textfile_extensions TEXT,edit_textfile_extensions TEXT,
	              PRIMARY KEY (id))");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	 
	  $database->setQuery("CREATE TABLE #__joomla_flash_uploader_conf (key_id TEXT, value TEXT)");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	 
      // we set the version
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('version','".$cur_version."')");
      if(!$database->query()) echo $database->getErrorMsg().'<br />';
	  
	  $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('keep_tables','true')");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	  
	  $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('use_js_include','true')");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	   
      // admin
	  $database->setQuery("INSERT INTO #__joomla_flash_uploader(
       config_name, folder, description, text_title,
	   text_top, text_bottom, text_title_lang, text_top_lang,
	   text_bottom_lang, maxfilesize, resize_show, resize_data,
	   resize_label, resize_default, allowed_file_extensions, forbidden_file_extensions,
	   hide_remote_view, show_delete, enable_folder_browsing, enable_folder_creation,
	   enable_folder_deletion, enable_folder_rename, enable_file_rename, keep_file_extension,
	   enable_file_download, sort_files_by_date, warning_setting, show_size,
	   enable_setting, creation_date, last_modified_date, fix_overlay,
	   flash_title, hide_directory_in_title, upload_notification_email, upload_notification_email_from,
	   upload_notification_email_subject, upload_notification_email_text, upload_finished_js_url, preview_select_js_url,
	   delete_js_url, js_change_folder, directory_file_limit, queue_file_limit, 
       queue_file_limit_size, display_width, enable_folder_movecopy, enable_file_movecopy,
       preview_textfile_extensions, edit_textfile_extensions
     ) 
	   values 
	   ('admin', '','Administration profile','Title',
	   'Text before flash',  'Text after flash',  'true',  'true',
	   'true',  '',  'true',  '10000,1000',
	   'Original,1000',  '0',  'all',  '',
	   'false',  'true',  'true',  'true',
	   'true',  'true',  'true',  'true',
	   'true',  'false',  'once',  '',
	   'true',  NOW(),  NOW(),  'false',
	   'Joomla Flash Uploader',  'false',  '',  '',
	   'Files were uploaded by the Joomla Flash Uploader',  'The following files where uploaded by %s: %s',  '',  '',
	   '',  '',  '100000',  '100000',
	   '100000',  '650',  'true', 'true',
       'txt,log,php,css,htm,html,js,bak','txt,log,php,css,htm,html,js,bak' );
	   ");
	 if(!$database->query()) echo $database->getErrorMsg().'<br />';
	 
     // frontend user
     $database->setQuery("INSERT INTO #__joomla_flash_uploader(
       config_name, folder, description, text_title,
	   text_top, text_bottom, text_title_lang, text_top_lang,
	   text_bottom_lang, maxfilesize, resize_show, resize_data,
	   resize_label, resize_default, allowed_file_extensions, forbidden_file_extensions,
	   hide_remote_view, show_delete, enable_folder_browsing, enable_folder_creation,
	   enable_folder_deletion, enable_folder_rename, enable_file_rename, keep_file_extension,
	   enable_file_download, sort_files_by_date, warning_setting, show_size,
	   enable_setting, creation_date, last_modified_date, fix_overlay,
	   flash_title, hide_directory_in_title, upload_notification_email, upload_notification_email_from,
	   upload_notification_email_subject, upload_notification_email_text, upload_finished_js_url, preview_select_js_url,
	   delete_js_url, js_change_folder, directory_file_limit, queue_file_limit, 
       queue_file_limit_size, display_width, enable_folder_movecopy, enable_file_movecopy,
       preview_textfile_extensions, edit_textfile_extensions
       ) 
	    values 
	    ('frontend','images/stories','Example frontend profile','Title',
	    'Text before flash','Text below flash','false','false',
	    'false', '1000','true','10000,1000',
        'Original,1000', '0','jpg,jpeg,gif,png' ,'' ,
        'false','true','true','false',
        'false','false', 'false','true',
        'false', 'false','all','',
        'true', NOW(), NOW(), 'false',
        'Joomla Flash Uploader','false', '','',
	    'Files were uploaded by the Joomla Flash Uploader','The following files where uploaded by %s: %s','','',
		'','','100000','100000',
	    '100000','650','false','false',
        'txt,log', '' );
     ");
  if(!$database->query()) echo $database->getErrorMsg().'<br />';
  }
  
  
  /*
    This function checks if a column does already exists. If not it is created.
    This makes it easy to do a update from older versions 
  */
  function update_db($newinstall){
   $database = &JFactory::getDBO();
    $error = false;
      // we create the new columns for 2.7
      $error = addColumn($database, 'preview_textfile_extensions', '', $error);
      $error = addColumn($database, 'edit_textfile_extensions', '', $error);
      $error = addColumn($database, 'js_create_folder', '', $error);
      $error = addColumn($database, 'js_rename_folder', '', $error);
      $error = addColumn($database, 'js_delete_folder', '', $error);
      $error = addColumn($database, 'js_copymove', '', $error);      
	  // new 2.8
	  $error = addColumn($database, 'language_dropdown', 'de,en,es,br,cn,ct,da,fr,it,jp,nl,no,pl,pt,se,sk,tw', $error);
	  $error = addColumn($database, 'use_image_magic', 'false', $error);
	  $error = addColumn($database, 'image_magic_path', 'convert', $error);
	  $error = addColumn($database, 'exclude_directories', 'data.pxp,_vti_cnf,.svn,CVS,thumbs', $error);
	  $error = addColumn($database, 'normalise_file_names', 'false', $error);
	  $error = addColumn($database, 'download_multiple_files_as_zip', 'false', $error);
	  $error = addColumn($database, 'allowed_view_file_extensions', 'all', $error);
	  $error = addColumn($database, 'forbidden_view_file_extensions', '', $error);
	  $error = addColumn($database, 'description_mode', 'false', $error);
	  $error = addColumn($database, 'description_mode_show_default', 'true', $error);
	  $error = addColumn($database, 'description_mode_store', 'email', $error);
	  $error = addColumn($database, 'master_profile', 'false', $error);
	  $error = addColumn($database, 'master_profile_mode', 'login', $error);
	  $error = addColumn($database, 'master_profile_lowercase', 'true', $error);
	  // new 2.8.3
	  $error = addColumn($database, 'normalise_directory_names', 'false', $error);
	  $error = addColumn($database, 'direct_download', 'false', $error);
	  $error = addColumn($database, 'fix_utf8', '', $error);
	  
	  // new 2.9
	  if (!testEntry('use_js_include')) {
        $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('use_js_include','true')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('backend_access_upload')) {
        $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('backend_access_upload','Manager')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('backend_access_config')) {
        $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('backend_access_config','Manager')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('file_chmod')) {
        $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('file_chmod','')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
      $error = addColumn($database, 'overwrite_files', 'true', $error);
	  $error = addColumn($database, 'description_mode_mandatory', 'false', $error); 
      $error = addColumn($database, 'show_full_url_for_selected_file', 'false', $error);
      $error = addColumn($database, 'normalize_spaces', 'false', $error);
      
      if (!testEntry('dir_chmod')) {
        $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('dir_chmod','')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
      
    if ($error) {
      echo "<p class='error'>";
      echo JText::_('I_UPDATE_ERROR');
      return true;
      echo "</p>";
    } else {
      if (!$newinstall) {
        echo JText::_('I_UPDATE_OK');
      }
      return false;
    }            
  }
  
  /*
    Checks if a field is a table exists - true: exist; false: does not exist
  */
  function testField($field, $table) {
     $database = &JFactory::getDBO();
     $database->setQuery("show columns from $table like '$field'");
     return (count ($database->loadObjectList()) > 0) ? true : false;
  }
  /*
    Checks if a entry in a table exists - true: exist; false: does not exist
  */
  function testEntry($entry) {
     $database = &JFactory::getDBO();
     $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = '".$entry."'");
     return (count ($database->loadObjectList()) > 0) ? true : false;
  }
  
  /*
    Adds a new column - first it checks is the table exists and if not it is added + the default value is set.
  */
  function addColumn($database, $field, $defaultValue, $error) {
    if (!testField($field, "#__joomla_flash_uploader")) {
	    $database->setQuery("ALTER TABLE #__joomla_flash_uploader ADD ".$field." TEXT DEFAULT ''");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	    // now we have to set the default value because before mysql 5.1.2 this is not possible
        $database->setQuery( "UPDATE #__joomla_flash_uploader SET ".$field."='".$defaultValue."'");
        if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	 return $error; 
  }
  
?>