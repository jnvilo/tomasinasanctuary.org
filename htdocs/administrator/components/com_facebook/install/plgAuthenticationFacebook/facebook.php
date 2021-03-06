<?php
/**
 * plgAuthenticationFacebook
 *
 * 
 * @version		$Id: facebook.php 52 2008-03-20 05:47:39Z chalet16 $
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

class plgAuthenticationFacebook extends JPlugin
{
	function plgAuthenticationFacebook( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
	function onAuthenticate( $credentials, $options, &$response )
	{
		if(defined('_JOOMLAFACEBOOK')) {
			if( ($credentials['username']=='FACEBOOK') || ($credentials['username']=='FACEBOOKSESSION') ) {			
				$facebook 		= JoomlaFacebook::getRestClient();
				$fbsession		= array();
				if($credentials['username']=='FACEBOOK') {
					$fbsession=$facebook->auth_getSession( $credentials['password'] );
				}
				if(($credentials['username']=='FACEBOOKSESSION') && ($credentials['password']!='')) {
					$facebook->session_key 	= $credentials['password'];
					$fbsession['uid']		= $facebook->users_getLoggedInUser();
				}
				if($fbsession['uid']!='') {
	
					$query					= 'SELECT first_name,last_name,sex,current_location.zip,current_location.country,timezone FROM user WHERE uid='.$fbsession['uid'];
					$fbuserprofile 			= $facebook->fql_query( $query );
					$response->status		= JAUTHENTICATE_STATUS_SUCCESS;
					@$response->username		= 'fb_'.$fbsession['uid'];
					@$response->email		= $fbsession['uid'].'@facebook.com';
					@$response->fullname		= $fbuserprofile[0]['first_name'].' '
											.$fbuserprofile[0]['last_name'];
					@$response->gender		= strtoupper( substr($fbuserprofile[0]['sex'], 0, 1 ));
					@$response->postcode		= $fbuserprofile[0]['current_location']['zip'];
					@$response->country		= $fbuserprofile[0]['current_location']['country'];
					@$response->timezone		= $fbuserprofile[0]['timezone'];
					return;
				}
			}
		}
		$response->status 		= JAUTHENTICATE_STATUS_FAILURE;
		
	}
}