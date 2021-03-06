<?php
/**
 * plgFacebookIframe
 *
 * 
 * @version		$Id: iframe.php 51 2008-03-20 05:36:46Z chalet16 $
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

class plgFacebookIframe extends JPlugin
{
	function plgFacebookIframe( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		$this->_plugin = &JPluginHelper::getPlugin( 'facebook', 'iframe' );
		$this->_params = new JParameter( $this->_plugin->params );
		$lang =& JFactory::getLanguage(); 
		$lang->load( 'plg_facebook_iframe' , JPATH_ADMINISTRATOR );
	}
	function onFacebookPage( $page, &$class )
	{
		$class->addMenu('home',JText::_('HOME'),'home','left',-10);
		if($page=='home') {
			$class->addContent('<fb:iframe smartsize="true" scrolling="auto" frameborder="0" height="4000" src="'.JURI::base().'/index.php?infacebook=1&template='.$this->_params->get('theme','beez').'"/>',0);
			
		}
		return true;
	}
	function onSystemUpdate() {
		if(JRequest::getVar('infacebook')=='1') {
			$app 		=& JFactory::getApplication();
			if($app->getName() != 'site') {
				return true;
			}
			$buffer 	= JResponse::getBody();
		   	$url		= addcslashes( JURI::Base(), '\/' );
			$append		= 'infacebook=1';
			if(JRequest::getVar( 'template' ) != '') {
			$append		.= '&template='.JRequest::getVar( 'template' );
			}
			$pattern[0]	= '/href="\/([^"\?]*)"/';
			$pattern[1] = '/href="\/([^"\?]*)\?([^"\?]*)"/';
			$pattern[2] = '/href="'.$url.'([^"\?]*)"/';
			$pattern[3] = '/href="'.$url.'([^"\?]*)\?([^"\?]*)"/';
			$replacements[0]	= 'href="/\\1?"'.$append.'"';
			$replacements[1]	= 'href="/\\1?\\2&'.$append.'"';
			$replacements[2]	= 'href="'.JURI::Base().'\\1?'.$append.'"';
			$replacements[3]	= 'href="'.JURI::Base().'\\1?\\2&'.$append.'"';
		  	$buffer = preg_replace( $pattern, $replacements, $buffer );
			JResponse::setBody( $buffer );
		}
		return true;
	}
}

