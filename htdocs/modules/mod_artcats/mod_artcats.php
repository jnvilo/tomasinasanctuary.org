<?php
/**
* @version        2.0.3    2009-04-25
* @package        Joomla
* @copyright    Copyright (C) 2008-09 Omar Ramos, Imperial Valley College. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
*/

/// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the helper functions only once
require_once (dirname(__FILE__).DS.'helper.php');

/**
* Contributed by Keywan Ghadami
* Router Fix which sets links on the Frontpage,
* that are not associated with any menu items,
* with the Itemid of the page they are currently on. 
*/
global $Itemid;

$app    =& JFactory::getApplication();
$router =& $app->getRouter();
$itemid = $router->getVar("Itemid");

if (!isset($itemid)){
    $router->setVar("Itemid", (int) $Itemid);
}

$artcats = new modArtCatsHelper($params);

$list = $artcats->getList();

if (!count($list)) {
    return;
}

require(JModuleHelper::getLayoutPath('mod_artcats', $params->get('tmpl') ));
