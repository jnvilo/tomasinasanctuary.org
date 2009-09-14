<?php
/**
* @version		$Id: rokcandy_system.php 10906 2008-09-05 07:27:34Z rhuk $
* @package		RokCandy
* @copyright	Copyright (C) 2008 - 2009 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rokcandy'.DS.'helpers'.DS.'rokcandyhelper.php' );

class plgSystemRokCandy_System extends JPlugin {
    
    var $_initialized = false;
	var $_instanceId = 0;
	var $_library;
	var $_debug;

	function plgSystemRokCandy_System(& $subject, $config) {
		parent :: __construct($subject, $config);
		$this->_instanceId = rand(1000, 9999);
		
		$this->_debug = JRequest::getVar('debug_rokcandy') == true ? true : false;
    }
	
	function onAfterRoute() {
		$this->_initialize();
	}
	
	function _PHP4() {
  		if (version_compare( phpversion(), '5.0' ) < 0) {
  			if (!$this->_initialized) {
  			  //Something is wrong with PHP4... let's repeat the work...	
  				$this->_instanceId = rand(1000, 9999);
  				$this->_initialize();
  			}
  			return true;
  		} else {
  		  return false;
  		}  		
    }
    
    function _initialize() {
        
		if ($this->_initialized) {
			JError::raiseWarning( '1' , 'RokCandy instanceId=' . $this->_instanceId . ' was initialized already');
			return true;
		}
		
		$document = & JFactory :: getDocument();
		$doctype = $document->getType();

		// Only render for HTML output
		if ($doctype !== 'html') {
			return false;
		}		
		
		$this->_library = RokCandyHelper::getMacros();
        
    }
    
    // Do BBCode replacements on the whole page
	function onAfterRender() {
	    
		// don't run if disabled overrides are true
	    if ($this->_shouldProcess()) return;
	    
		$this->_PHP4();

		$document = & JFactory::getDocument();
		$doctype = $document->getType();
		if ($doctype == 'html') {
			$body = JResponse::getBody();
			if ($this->_replaceCode($body)) {
				JResponse::setBody($body);
			}
		}
	}
	
	function _shouldProcess() {
	    global $mainframe;
	    
	    $params =& JComponentHelper::getParams('com_rokcandy');
	    
	    //don't run if in edit mode and flag enabled
	    if (JRequest::getCmd('task') == 'edit' && $params->get('editenabled',0) == 0) return true;
	    
	    // don't process if in list view:
	    if (JRequest::getCmd('task') == 'list' && JRequest::getCmd('option') == "com_rokcandy") return true;	  
	      
	    //don't run in admin
		if ($mainframe->isAdmin() && $params->get('adminenabled',0)==0) return true;

	    // process manual overrides
	    $flag = false;
	    $is_disabled = $params->get('disabled');
	    if ($is_disabled != "") {
	        $disabled_entries = explode ("\n",$params->get('disabled'));
	        foreach ($disabled_entries as $entries) {
	            $checks = explode ("&",$entries);
	            if (count($checks) > 0) {
	                $flag = true;
    	            foreach ($checks as $check) {
    	                $bits = explode ("=",$check);
    	                if ((count($bits) == 2) && ($bits[1] != "") && (JRequest::getVar($bits[0]) == $bits[1])) {
    	                    $flag = true;
    	                }
    	                else {
    	                    $flag = false;
    	                    break;
    	                }
    	                
    	            }
                }
	        }
	    }
	    return $flag;
	}
	
	function _replaceCode(&$body) {
  

	    foreach ($this->_library as $key => $val) {
	        $search         = array();
            $replace        = array();
	        
	        $key            = str_replace("[","\[",$key);
	        $key            = str_replace("]","\]",$key);
	        $tokens         = array();
	        $key_regexp = '%'.preg_replace('%\{([a-zA-Z0-9_]+)\}%', '(?P<$1>.*?)',$key).'%s';
	        
	        if ($this->_debug) var_dump ($key_regexp);
	        preg_match_all($key_regexp,$body,$results);
	        if (!empty($results[0])) {
	            if ($this->_debug) var_dump ($results);
    	        $search = array_merge($search, $results[0]);
    	        foreach ($results as $k => $v) {
    	            if (!is_numeric($k)) {
    	                $tokens[] = $k;
    	            }
    	        }
                for($i=0;$i< count($results[0]);$i++) {
                    $tmpval = $val;
                    foreach ($tokens as $token) {
                        $tmpval = str_replace("{".$token."}",$results[$token][$i],$tmpval);
                    }
                    $replace[] = $tmpval;
                }
	        }
	        $body = str_replace($search,$replace,$body);
	    }
        
        return true;
	}
    
    
    
    function _readIniFile($path, $library) {
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
		return $library;
    }

}