<?php
/**
 * JoomlaFacebook API
 *
 * To Use This File
 * require_once(JPATH_SITE.DS.'components'.DS.'com_facebook'.DS.'api.php');
 *
 * @version		$Id: api.php 58 2008-03-23 05:23:39Z chalet16 $
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
defined('_JEXEC') or die();

$GLOBALS['facebook_config']['debug']=false;

if(substr(phpversion(),0,1)=='5') {
	include_once( JPATH_SITE.DS.'components'.DS.'com_facebook'.DS.'facebook-platform'.DS.'php5'.DS.'facebook.php' );
}
else {
	include_once( JPATH_SITE.DS.'components'.DS.'com_facebook'.DS.'facebook-platform'.DS.'php4'.DS.'facebook.php' );
}

/**
 * Joomla-Facebook API class
 *
 */
class JoomlaFacebook extends JObject
{
	/**
	 * Get a Facebook platform object
	 *
	 * Returns a reference to the global Facebook platform object, only creating it
	 * if it doesn't already exist.
	 *error_reporting(E_ALL);
	 * 
	 * @access public
	 * @return object Facebook
	 */
	function &getFacebookPlatform()
	{
		static $instance;
		if (!is_object( $instance )) {
			$instance = new Facebook( JoomlaFacebook::getAPIKey(), JoomlaFacebook::getSecretKey() );
		}
		return $instance;
	}
	/**
	 * Get a FacebookRestClient object
	 *
	 * Returns a reference to the global FacebookRestClient object, only creating it
	 * if it doesn't already exist.
	 *
	 * @access public
	 * @return object FacebookRestClient
	 */
	function &getRestClient()
	{
		static $instance;
		if (!is_object( $instance )) {
			$instance = new FacebookRestClient( JoomlaFacebook::getAPIKey(), JoomlaFacebook::getSecretKey() );
		}
		return $instance;
	}

	function getSecretkey() {
		return JoomlaFacebook::getCfg('secretkey');	
	}
	function getApiKey() {
		return JoomlaFacebook::getCfg('apikey');	
	}
	function getAppname() {
		return JoomlaFacebook::getCfg('appname');	
	}
	function getCfg($name,$default=NULL) { 
		$component=&JComponentHelper::getComponent( 'com_facebook' );
		$params=new JParameter($component->params);
		return $params->get($name,$default);
	}
}
if(JoomlaFacebook::getCfg('enable',0)==1) {
	define('_JOOMLAFACEBOOK',true);
}

