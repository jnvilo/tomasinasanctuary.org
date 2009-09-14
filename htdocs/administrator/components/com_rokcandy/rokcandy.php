<?php
/**
* @version		$Id: rokcandy.php 10906 2008-09-05 07:27:34Z rhuk $
* @package		RokCandy
* @copyright	Copyright (C) 2008 - 2009 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

$controller	= new RokCandyController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();