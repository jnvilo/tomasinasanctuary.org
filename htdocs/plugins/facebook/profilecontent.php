<?php
/**
 * plgFacebookProfileContent
 *
 * 
 * @version		$Id: profilecontent.php 37 2008-03-12 09:29:21Z chalet16 $
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

class plgFacebookProfileContent extends JPlugin
{
	function plgFacebookProfileContent( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		$this->_plugin = &JPluginHelper::getPlugin( 'facebook', 'profilecontent' );
		$this->_params = new JParameter( $this->_plugin->params );
		$lang =& JFactory::getLanguage(); 
		$lang->load( 'plg_facebook_profilecontent' , JPATH_ADMINISTRATOR );
	}
	function onFacebookConfigForm( &$form )
	{
		$form.= '
		<fb:editor-custom><h2>'.JText::_('LASTARTICLE').'</h2></fb:editor-custom>
		<fb:editor-custom label="'.JText::_('SHOWPROFILE').'">
		<select name="content_profile">
		<option value="1" selected>'.JText::_('Yes').'</option>  
		<option value="0">'.JText::_('No').'</option>
		</select>
		</fb:editor-custom>
		<fb:editor-custom label="'.JText::_('LASTPOSTNO').'">
		<select name="content_no">
		';
		for($n=1; $n<=20; $n++)
		{
			$form.='<option value="'.$n.'" '.(($n==5)?'selected':'').'>'.$n.'</option>';
		}
		$form.= '</select></fb:editor-custom>';
		return true;
	}
	function onFacebookSaveForm( &$fbml, &$error )
	{	
		if(JRequest::getVar('content_profile')==1) {
			$var=intval(JRequest::getVar('content_no'));
			$fbml.='<h2>'.JText::_('LASTCONTENT').'</h2><ul>';
			for($n=0;$n<$var;$n++) {
				$fbml.='<li><fb:ref handle="content-'.$n.'" /></li>';
			}
			$fbml.='</ul>';
		}
		return true;
	}
	function onFacebookUpdate($error) {
		$facebook = &JoomlaFacebook::getFacebookPlatform();
		$db = &JFactory::getDBO();
		$query = 'SELECT id,title,created FROM #__content ORDER BY created DESC LIMIT 0,'.$this->_params->get('num',10);
		$db->setQuery($query);
		$result=$db->loadObjectList();
		$config = &JFactory::getConfig();
		$n=0;
		foreach($result as $record) {
			$facebook->api_client->fbml_setRefHandle('content-'.$n,'<a href="'.JURI::base().'/index.php?option=com_content&id='.$record->id.'">'.$record->title.'</a> (Created on'.$record->created.')');
			$n++;
		}
		return true;
	}
}

