<?php
/**
* @version $Id: mod_variationchooser.php 1492 2005-12-20 16:07:35Z Jinx $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); 
global $tstyle;

function getSelected($style) {
	global $tstyle;
	if ($style==$tstyle) return ' selected="selected"';
	else return "";
}

$document =& JFactory::getDocument();
$document->addScript( JURI::root(true).'/modules/mod_variationchooser/tmpl/variationchooser.js');

require(JModuleHelper::getLayoutPath('mod_variationchooser'));  
