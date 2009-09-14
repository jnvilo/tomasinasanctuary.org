<?php
/**
 * mod_facebook
 *
 * 
 * @version		$Id: mod_facebook.php 36 2008-03-12 02:21:01Z chalet16 $
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

@include_once(JPATH_SITE.DS.'components'.DS.'com_facebook'.DS.'api.php');

if(defined('_JOOMLAFACEBOOK')) {
// Include the syndicate functions only once
$apikey			= JoomlaFacebook::getAPIKey();
$module = &JModuleHelper::getModule( 'facebook' );
$params = new JParameter( $module->params );
$showadmin		= $params->get('showadmin',1) == 1;
$showimg		= $params->get('showimg',1) == 1;
$imgpath		= 'http://static.ak.facebook.com/images/devsite/facebook_login.gif';
require( JModuleHelper::getLayoutPath( 'mod_facebook' ) );
}
else {
	echo JText::_('NOTINSTALL');
}
