<?php
/**
 * plgFacebookProfile
 *
 * 
 * @version		$Id: profile.php 53 2008-03-20 05:50:08Z chalet16 $
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

jimport( 'joomla.event.plugin' );

class plgFacebookProfile extends JPlugin
{
	function plgFacebookProfile( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		$this->_plugin = &JPluginHelper::getPlugin( 'facebook', 'profile' );
		$this->_params = new JParameter( $this->_plugin->params );
		$lang =& JFactory::getLanguage(); 
		$lang->load( 'plg_facebook_profile' , JPATH_ADMINISTRATOR );
	}
	function onFacebookPage( $page, &$class )
	{
		global $mainframe;
		$class->addMenu('profile',JText::_('PROFILE'),'profile','right',5);
		JPluginHelper::importPlugin('facebook');
		$content='';
		if($page=='profile') {
			$facebook=&JoomlaFacebook::getFacebookPlatform();
			$facebook->require_install();
			if(JRequest::getVar('action')=='save'||JRequest::getVar('action')=='update') {
				$error='';
				if(JRequest::getVar('action')=='save') {
					$fbml='<p style="text-align:right"><a href="http://apps.facebook.com/'.JoomlaFacebook::getAppname().'/profile&action=update">Update Profile Box</a></p>';
					$fbml.=$this->_params->get('header');

					$mainframe->triggerEvent( 'onFacebookSaveForm', array( &$fbml,&$error ) );
					$fbml.=$this->_params->get('footer');
					$facebook->api_client->profile_setFBML($fbml,$facebook->require_login());
				} 
				$mainframe->triggerEvent( 'onFacebookUpdate', array( &$error ) );
				if($error!='') {
					$content.='<fb:error><fb:message>'.JText::_('UPDATEERROR').'</fb:message> '.$error.'</fb:error>';
				} else {
					$content.='<fb:success message="'.JText::_('UPDATESUCCESS').'" /> ';

				}
			}
			$content.='<h1>'.JText::_('PROFILESETTING').'</h1><fb:editor action="profile" labelwidth="100">  ';
			$mainframe->triggerEvent( 'onFacebookConfigForm', array( &$content ) ); 
			$content.='<br/><fb:editor-custom><input type="hidden" name="action" value="save"/></fb:editor-custom><fb:editor-buttonset><fb:editor-button value="'.JText::_('APPLY').'"/><fb:editor-cancel href="" /></fb:editor-buttonset></fb:editor>';
			$class->addContent($content,0);
		}
		return true;
	}

}

