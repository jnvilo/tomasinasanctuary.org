<?php
/**
 * plgSystemFacebook
 *
 * 
 * @version		$Id: facebook.php 36 2008-03-12 02:21:01Z chalet16 $
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

jimport( 'joomla.event.plugin' );

class plgSystemFacebook extends JPlugin
{    	
	function plgSystemFacebook( &$subject, $config ) 
	{
		parent::__construct($subject, $config);
	}
	function onAfterRoute()
    {
    	global $mainframe;
    	if(defined('_JOOMLAFACEBOOK')) {
			if(JRequest::getVar('auth_token')!=null) {
				if(JRequest::getVar('redirect')=='admin') {
				$mainframe->redirect( JURI::Base().'administrator/?auth_token='.JRequest::getVar('auth_token') );
				} else {
				$credentials 					= array();
				$credentials['username'] 		= 'FACEBOOK';
				$credentials['password'] 		= JRequest::getVar('auth_token', '', 'get');
				$mainframe->login( $credentials );
				$mainframe->redirect( JURI::Base() );
				}
			}
			if(JRequest::getVar('fb_sig_session_key','','get')!=null) {
					$credentials = array();
					$credentials['username'] 	= 'FACEBOOKSESSION';
					$credentials['password'] 	= JRequest::getVar( 'fb_sig_session_key', '', 'get' );
					$mainframe->login( $credentials );
			}
    	}
		return true;
    }
	function onAfterRender()
	{
		global $mainframe;
		if(defined('_JOOMLAFACEBOOK')) {
			JPluginHelper::importPlugin('facebook');
			$mainframe->triggerEvent( 'onSystemUpdate',array()); 
		}
		return true;
	}

}
