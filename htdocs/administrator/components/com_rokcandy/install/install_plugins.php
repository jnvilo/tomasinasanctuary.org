<?php

/**
 * @version $Id$
 * @package RocketWerx
 * @subpackage	RokCandy
 * @copyright Copyright (C) 2008 RocketWerx. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
 // no direct access
defined('_JEXEC') or die('Restricted access');

function rok_plugin_install(&$installer, $pluginname, $title, $group='search', &$db) { 
	$status = new Status();
	$status->status = $status->STATUS_FAIL;
	
	// Set the installation path
	$element =& $installer->manifest->getElementByPath($pluginname.'/files');
	if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
	    $files =& $element->children();
	    foreach ($files as $file) {
	        if ($file->attributes('plugin')) {
	            $pname = $file->attributes('plugin');
	            break;
	        }
	    }
	}
	
	if (!empty ($pname) && !empty($group)) {
	    $installer->parent->setPath('extension_root', JPATH_ROOT.DS.'plugins'.DS.$group);
	} else {
		$status->errmsg[]=JText::_('No plugin file specified');
		return $status;
	}

	/**
	 * ---------------------------------------------------------------------------------------------
	 * Filesystem Processing Section
	 * ---------------------------------------------------------------------------------------------
	 */
	
	// If the plugin directory does not exist, lets create it
	$created = false;
	if (!file_exists($installer->parent->getPath('extension_root'))) {
	    if (!$created = JFolder::create($installer->parent->getPath('extension_root'))) {
	    	$status->errmsg[]=JText::_('Failed to create directory');
			return $status;
	    }
	}

	/*
	 * If we created the plugin directory and will want to remove it if we
	 * have to roll back the installation, lets add it to the installation
	 * step stack
	 */
	if ($created) {
	    $installer->parent->pushStep(array ('type' => 'folder', 'path' => $installer->parent->getPath('extension_root')));
	}

	// Copy all necessary files
	if ($installer->parent->parseFiles($element, -1) === false) {
	    // Install failed, roll back changes
	    $installer->parent->abort();
	    $status->errmsg[]=JText::_('Unable to parse files for copying');
		return $status;
	}

	/**
	 * ---------------------------------------------------------------------------------------------
	 * Database Processing Section
	 * ---------------------------------------------------------------------------------------------
	 */	
	// Check to see if a plugin by the same name is already installed
	$query = 'SELECT `id`' .
	        ' FROM `#__plugins`' .
	        ' WHERE folder = '.$db->Quote($group) .
	        ' AND element = '.$db->Quote($pname);
	$db->setQuery($query);
	if (!$db->Query()) {
	    // Install failed, roll back changes
	    $installer->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
	   	$status->errmsg[]=JText::_('Unable to query Database');
		return $status;
	}
	$id = $db->loadResult();

	// Was there a plugin already installed with the same name?
	if ($id) {
	
	    if (!$installer->parent->getOverwrite())
	    {
	        // Install failed, roll back changes
	        $installer->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Plugin').' "'.$pname.'" '.JText::_('already exists!'));
	        $status->errmsg[]=JText::_('Plugin already is installed');
			return $status;
	    }
	
	} else {
	    $row =& JTable::getInstance('plugin');
	    $row->name = $title;
	    $row->ordering = 2;
	    $row->folder = $group;
	    $row->iscore = 1;
	    $row->access = 0;
	    $row->client_id = 0;
	    $row->element = $pname;
	    $row->published = 1;
	    $row->params = '';
	
	    if (!$row->store()) {
	        // Install failed, roll back changes
	        $installer->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
	        $status->errmsg[]=JText::_('Unable to write plugin information to database');
			return $status;

	    }
	}
	$status->status = $status->STATUS_SUCCESS;
	return $status;
}

function rok_plugin_uninstall(&$installer, $pluginname, $group='search', &$db) { 	
	$status = new Status();
	$status->status = $status->STATUS_FAIL;
	
	// Set the plugin root path
	$installer->parent->setPath('extension_root', JPATH_ROOT.DS.'plugins'.DS.$group);
	
	// Delete the module in the #__modules table
	$query = 'DELETE FROM #__plugins WHERE element = '.$db->Quote($pluginname).' AND folder = '.$db->Quote($group);
	$db->setQuery($query);
	if (!$db->query()) {
	    $status->errmsg[]=JText::_('Unable to delete from plugin table').': '.$db->stderr(true);
		return $status;
	}
	
	// Set the installation path
	$element =& $installer->manifest->getElementByPath($pluginname.'/files');
	if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
	    $installer->parent->removeFiles($element, -1);
	}
	// If the folder is empty, let's delete it
	$files = JFolder::files($installer->parent->getPath('extension_root'));
	if (!count($files)) {
	    JFolder::delete($installer->parent->getPath('extension_root'));
	}
	$status->status = $status->STATUS_SUCCESS;
	return $status;
}