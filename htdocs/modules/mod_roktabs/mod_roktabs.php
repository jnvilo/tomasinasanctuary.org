<?php
/**
* @version		$Id: mod_roktabs.php 9764 2008-08-12 07:48:11Z djamil $
* @package		RocketTheme
* @copyright	Copyright (C) 2005 - 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');


$conf =& JFactory::getConfig();
if ($conf->getValue('config.caching') && $params->get("module_cache", 0)) { 
	$cache =& JFactory::getCache('mod_roktabs');
	$list = $cache->call(array('modRokTabsHelper', 'getList'), $params);
}
else {
	$list = modRokTabsHelper::getList($params);
}

require(JModuleHelper::getLayoutPath('mod_roktabs'));