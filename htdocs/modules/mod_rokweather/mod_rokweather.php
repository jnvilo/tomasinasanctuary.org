<?php
/**
* @version		$Id: mod_rokweather.php 9764 2009-04-15 07:48:11Z rhuk $
* @package		RocketTheme
* @copyright	Copyright (C) 2005 - 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

JHTML::_('behavior.mootools');

$defaultDegree = $params->get('default_degree', 0);

$doc =& JFactory::getDocument();
$doc->addScript(JURI::Root(true).'/modules/mod_rokweather/tmpl/js/rokweather.js');
$doc->addStyleSheet(JURI::Root(true).'/modules/mod_rokweather/rokweather.css');
$doc->addScriptDeclaration("window.addEvent('domready', function() {new RokWeather({defaultDegree: {$defaultDegree}});});");

$passed_module_name = JRequest::getString('module_name');
if (isset($passed_module_name) && $module->title=="") $module->title = $passed_module_name;
$module_name = $module->title;

if (isset($_REQUEST['weather_location'])) {
    $weather_location = JRequest::getString("weather_location");
} elseif (isset($_COOKIE["rokweather_location"])) {
    $weather_location = JRequest::getString("rokweather_location", '', 'COOKIE', 'STRING');
} else {
    $weather_location = $params->get("default_location","New York,NY");
}


$url = JURI::base()."index.php?option=com_rokmodule&tmpl=component&type=raw&module=".$module_name;
$icon_url = JURI::base()."modules/mod_rokweather";
$output = "";

$weather = modRokWeatherHelper::getWeather($weather_location,$icon_url,$params);

require(JModuleHelper::getLayoutPath('mod_rokweather'));