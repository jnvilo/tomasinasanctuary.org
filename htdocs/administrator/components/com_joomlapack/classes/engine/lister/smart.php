<?php
/**
* @package		JoomlaPack
* @copyright	Copyright (C) 2006-2008 JoomlaPack Developers. All rights reserved.
* @version		$Id$
* @license 		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @since		1.3
**/
defined('_JEXEC') or die('Restricted access');

if (function_exists('php_uname'))
	define('JPISWINDOWS', stristr(php_uname(), 'windows'));
else
	define('JPISWINDOWS', DS == '\\');

/**
 * A filesystem scanner which uses opendir() and is smart enough to make large directories
 * be scanned inside a step of their own.
 * 
 * The idea is that if it's not the first operation of this step and the number of contained
 * directories AND files is more than double the number of allowed files per fragment, we should
 * break the step immediately.
 *
 */
class JoomlapackListerSmart extends JoomlapackCUBELister
{
	function &getFiles($folder)
	{
		// Initialize variables
		$arr = array();
		$false = false;

		if(!is_dir($folder)) return $false;
		
		$counter = 0;
		$registry =& JoomlapackModelRegistry::getInstance();
		$maxCounter = $registry->get('mnMaxFragmentFiles',50) * 2;
		
		$cube =& JoomlapackCUBE::getInstance();
		$allowBreakflag = ($cube->operationCounter != 0);

		$handle = @opendir($folder);
		// If directory is not accessible, just return FALSE
		if ($handle === FALSE) {
			JoomlapackLogger::WriteLog(_JP_LOG_WARNING, 'Unreadable directory '.$dirName);
			return $false;
		}
		
		while ( (($file = @readdir($handle)) !== false) && (!$this->BREAKFLAG) )
		{
			$dir = $folder . DS . $file;
			$isDir = is_dir($dir);
			if (!$isDir) {
				$data = JPISWINDOWS ? JoomlapackHelperUtils::TranslateWinPath($dir) : $dir;
				if($data) $arr[] = $data; 
			}
			$counter++;
			if($counter >= $maxCounter) $this->BREAKFLAG = (true && $allowBreakflag);			
		}
		@closedir($handle);		

		return $arr;
	}

	function &getFolders($folder)
	{
		// Initialize variables
		$arr = array();
		$false = false;

		if(!is_dir($folder)) return $false;
		
		$counter = 0;
		$registry =& JoomlapackModelRegistry::getInstance();
		$maxCounter = $registry->get('mnMaxFragmentFiles',50) * 2;

		$cube =& JoomlapackCUBE::getInstance();
		$allowBreakflag = ($cube->operationCounter != 0);
		
		$handle = @opendir($folder);
		// If directory is not accessible, just return FALSE
		if ($handle === FALSE) {
			JoomlapackLogger::WriteLog(_JP_LOG_WARNING, 'Unreadable directory '.$dirName);
			return $false;
		}
		
		while ( (($file = @readdir($handle)) !== false) && (!$this->BREAKFLAG) )
		{
			$dir = $folder . DS . $file;
			$isDir = is_dir($dir);
			if ($isDir && ($file != '.') && ($file != '..') ) {
				$data = JPISWINDOWS ? JoomlapackHelperUtils::TranslateWinPath($dir) : $dir;
				if($data) $arr[] = $data; 
			}
			$counter++;
			if($counter >= $maxCounter) $this->BREAKFLAG = (true && $allowBreakflag);			
		}
		@closedir($handle);

		return $arr;
	}
}
