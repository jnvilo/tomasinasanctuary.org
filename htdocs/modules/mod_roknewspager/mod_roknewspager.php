<?php
/**
* @version		$Id: mod_roknewspager.php 9764 2009-04-15 07:48:11Z rhuk $
* @package		RocketTheme
* @copyright	Copyright (C) 2005 - 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

JHTML::_('behavior.mootools');

$doc =& JFactory::getDocument();
$doc->addScript(JURI::Root(true).'/modules/mod_roknewspager/tmpl/js/roknewspager.js');

// Cache this basd on access level
$conf =& JFactory::getConfig();
if ($conf->getValue('config.caching') && $params->get("module_cache", 0)) { 
	$user =& JFactory::getUser();
	$aid  = (int) $user->get('aid', 0);
	switch ($aid) {
	    case 0:
	        $level = "public";
	        break;
	    case 1:
	        $level = "registered";
	        break;
	    case 2:
	        $level = "special";
	        break;
	}
	// Cache this based on access level
	$cache =& JFactory::getCache('mod_roknewspager-' . $level);
	$list = $cache->call(array('modRokNewsPagerHelper', 'getList'), $params);
	$count = $cache->call(array('modRokNewsPagerHelper', 'getRowCount'), $params);
}
else {
    $list = modRokNewsPagerHelper::getList($params);
    $count = modRokNewsPagerHelper::getRowCount($params);
}

$perpage = $params->get('count', 5);
$offset = JRequest::getInt('offset',0);

$pages = floor($count/$perpage) + 1;
$curpage = intval(($offset/$perpage)+1);

if ($params->get('module_ident','name')=='name') {
    $passed_module_name = JRequest::getString('module');
    if (isset($passed_module_name) && $module->title=="") $module->title = $passed_module_name;
    $module_name = $module->title;
} else {
    $passed_module_id = JRequest::getString('moduleid');
    if (isset($passed_module_id) && $module->id=="") $module->id = $passed_module_id;
    $module_id = $module->id;
}


require(JModuleHelper::getLayoutPath('mod_roknewspager'));