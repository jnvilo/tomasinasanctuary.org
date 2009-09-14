<?php
/**
 * Joomla Flash uploader 2.8 Freeware - for Joomla 1.0.x
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/
  defined( '_JEXEC' ) or die( 'Restricted access' );
  
  // uninstall screen
define("R_TEXT","<p>Joomla Flash Uploader was uninstalled successful.</p>");
define("R_TEXT_KEEP","<p>The tables used for the profiles and the users mapping where not removed. If you like to remove this tables too please reinstall the component, go to Components -> Joomla Flash Uploader -> Configuration and change the global setting to remove the settings as well. If you only updating JFU you shpuld keep the tables because the setup will update them if needed.</p>");
define("R_TEXT_NOT_KEEP","<p>Removing the tables ...<br>done.<br>If you want to keep your settings the next time (if you only want to update!),  go to Components -> Joomla Flash Uploader -> Configuration and change the global settings.</p>");

  
  function com_uninstall() {
      $database = &JFactory::getDBO();
	
	  $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'keep_tables'");
	  $settings = $database->loadObjectList();
 
	  if ( $settings[0]->value == 'false') {
	    $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader");
	    $database->query();
	    $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader_user");
	    $database->query();
	    $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader_conf");
        $database->query();
        echo R_TEXT_NOT_KEEP;
      } else { // we keep it and store the license data if still a license file exists.
        $database->setQuery("DELETE FROM #__joomla_flash_uploader_conf where key_id='user' OR key_id='domain' OR key_id='code' ");
        if(!$database->query()) { error_log($database->getErrorMsg()); }
        // If we have a license file we add this stuff to the db - we delete the old one first
	      $file = dirname(__FILE__) . "/tfu/twg.lic.php";
        if (file_exists($file)) {
          include($file);
          if(!$database->query()) { error_log($database->getErrorMsg()); }
          $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('user','".$l."')");
  	      if(!$database->query()) { error_log($database->getErrorMsg()); }
          $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('domain','".$d."')");
  	      if(!$database->query()) { error_log($database->getErrorMsg()); }
          $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('code','".$s."')");
  	      if(!$database->query()) { error_log($database->getErrorMsg()); }
	  
      }
        echo R_TEXT_KEEP;
      }
    echo R_TEXT;
  }
?>