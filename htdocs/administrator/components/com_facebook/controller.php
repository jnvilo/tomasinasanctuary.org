<?php
/**
 * com_facebook (Admin) Controller
 *
 * 
 * @version		$Id: controller.php 37 2008-03-12 09:29:21Z chalet16 $
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
include_once(JPATH_SITE.DS.'components'.DS.'com_facebook'.DS.'api.php');

jimport('joomla.application.component.controller');

class FacebookController extends JController
{
	function display()
	{
		$document 		= &JFactory::getDocument();
		$viewType		= $document->getType();
		$viewName		= JRequest::getCmd( 'view', $this->getName() );
		$view 			= &$this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath) );
		$model 			= &$this->getModel( 'install' );
		$view->setModel( $model, true );
		$view->setLayout( 'default'  );
		$view->display();
	}
	function redirectinstall() {
		$this->setRedirect( 'index.php?option=com_facebook&view=install' );	
	}
	function install()
	{
		global $mainframe;
		$model			= &$this->getModel('install');
		$list			= &$model->getToInstallList();
		foreach ($list as $path) {
			$model->uninstall( $path );
			$manifest = $model->getManifestObject( $path );
			$manifestdata 	= $model->extractManifestData(  $manifest );
			if(JRequest::getVar('install-'.$manifestdata['name'],0)==1||$manifestdata['require']==1) {
				if(!$manifestdata['installed']) {			
					$model->install( $path );			
					$model->enable( $path );
				}
			} else {
				if($manifestdata['installed']) {
					$model->uninstall( $path );
				}
			}
		} 
		$this->setRedirect( 'index.php?option=com_facebook' );	
	}

}