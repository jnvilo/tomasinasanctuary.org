<?php
/**
* @version		$Id: rokcandyhelper.php 10906 2008-09-05 07:27:34Z rhuk $
* @package		RokCandy
* @copyright	Copyright (C) 2008 - 2009 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );



class RokCandyHelper {
    
    function getMacros() {
        $params =& JComponentHelper::getParams('com_rokcandy');
        $cache = & JFactory::getCache('com_rokcandy');
        
        if ($params->get('forcecache',0)==1) $cache->setCaching(true);
        $usermacros = $cache->call(array('RokCandyHelper','getUserMacros'));
        $overrides = RokCandyHelper::getTemplateOverrides();
    
        return array_merge($usermacros,$overrides);
    }
    
    function getUserMacros() {
        $db	=& JFactory::getDBO();
        $sql = 'SELECT * FROM #__rokcandy WHERE published=1';
        $db->setQuery($sql);
        $macros = $db->loadObjectList(); 
        
        $library = array();
        foreach ($macros as $macro) {
            $library[trim($macro->macro)] = trim($macro->html);
        }
        return $library;
    }
    
    
    function getTemplateOverrides() {
        global $mainframe;
        
        $params =& JComponentHelper::getParams('com_rokcandy');
		$cache = & JFactory::getCache('com_rokcandy');
		if ($params->get('forcecache',0)==1) $cache->setCaching(true);
	    $library = $cache->call(array('RokCandyHelper','readIniFile'));

	    return $library;
    }


    function readIniFile() {
        global $mainframe;
        
        $template = $mainframe->isAdmin() ? RokCandyHelper::getCurrentTemplate() : $mainframe->getTemplate();
		$path = JPATH_SITE.DS."templates".DS.$template.DS."html".DS."com_rokcandy".DS."default.ini";
		
        $library = array();
        
        if (file_exists($path)) {
            jimport( 'joomla.filesystem.file' );
            $content = JFile::read($path);
            $data = explode("\n",$content);
            

    		foreach ($data as $line) {
    		    //skip comments
    		    if (strpos($line,"#")!==0 and trim($line)!="" ) {
    		       $div = strpos($line,"]=");
    		       $library[substr($line,0,$div+1)] = substr($line,$div+2);
    		    }
    		}
    	}
		return $library;
    }
    
    function getCurrentTemplate() {
        $db	=& JFactory::getDBO();
        $sql = 'SELECT template FROM #__templates_menu WHERE client_id=0 and menuid=0';
        $db->setQuery($sql);
        $template = $db->loadResult();
        return $template;
    }

    
}


?>