<?php
/**
 * Admin entry point 
 *
 * 
 * @version		$Id: admin.facebook.php 36 2008-03-12 02:21:01Z chalet16 $
 * @package 	Joomla-Facebook
 * @link 		http://joomlacode.org/gf/project/joomla-facebook/
 * @license		GNU/GPL, see LICENSE.php
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die();

// Require Joomla-Facebook Class
require_once(JPATH_SITE.DS.'components'.DS.'com_facebook'.DS.'api.php');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Require specific controller if requested
if($controller = JRequest::getVar( 'controller' )) {
	require_once ( JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php' );
}

// Create the controller
$classname	= 'FacebookController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();