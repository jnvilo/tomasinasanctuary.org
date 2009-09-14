<?php
/**
* @version		$Id: helper.php 9764 2009-04-24 14:31:11Z djamil $
* @package		RocketTheme
* @copyright	Copyright (C) 2005 - 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'modules'.DS.'mod_rokstock'.DS.'googlestock.class.php');

class modRokStockHelper
{
	
	function loadScripts(&$params, $url)
	{
		JHTML::_('behavior.mootools');
		
		$js_file = JURI::base() . 'modules/mod_rokstock/tmpl/js/rokstock.js';
		
		if (!defined('ROKSTOCK_JS')) {
			$save_cookie = ($params->get("store_cookie", "1") == "1") ? 1 : 0;
			$duration_cookie = $params->get("store_time", 30);
			$externals = ($params->get('externals', "1") == "1") ? 1 : 0;
			$show_main_chart = ($params->get("show_main_chart", "1") == "1") ? 1 : 0;
			$show_tooltips = ($params->get("show_tooltips", "1") == "1") ? 1 : 0;
			
			$document =& JFactory::getDocument();
			$document->addScript($js_file);
			$document->addScriptDeclaration("window.addEvent('domready', function() {
	new RokStock({
		detailURL: '{$url}',
		cookie: {$save_cookie},
		cookieDuration: {$duration_cookie},
		externalLinks: {$externals},
		mainChart: {$show_main_chart},
		toolTips: {$show_tooltips}
	});
});");
			define('ROKSTOCK_JS',1);
		}
	}
	
	function getStock($stocks,&$params) {
	 	$gstock = new googleStock(JPATH_CACHE);
		$output = $gstock->makeRequest($stocks);
//		var_dump($output);
				
		return $output;
	}
}
