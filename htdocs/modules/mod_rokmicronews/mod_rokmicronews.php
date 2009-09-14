<?php
/**
* @version		$Id: rokmicronews.php 9764 2007-12-30 07:48:11Z rhuk $
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
$doc->addScript(JURI::Root(true)."/modules/mod_rokmicronews/tmpl/rokmicronews.js");

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
	$cache =& JFactory::getCache('mod_rokmicronews-' . $level);
	$list = $cache->call(array('modRokMicroNewsHelper', 'getList'), $params);
}
else {
    $list = modRokMicroNewsHelper::getList($params);
}

$num_lead = 1;  // this is now configured via js
if (sizeof($list) < $num_lead) $num_lead = sizeof($list);

require(JModuleHelper::getLayoutPath('mod_rokmicronews'));