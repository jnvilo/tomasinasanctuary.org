<?php
/**
 * Joomla Flash uploader 2.8 Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class TOOLBAR_joomla_flash_uploader {

function _EDIT() {
						JToolBarHelper::save('saveConfig');
						JToolBarHelper::cancel('config');
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);	
        }
        
function _USER() {
						JToolBarHelper::custom( 'upload', 'upload','upload',JText::_('T_UPLOAD'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
            JToolBarHelper::custom( 'config', 'config','config',JText::_('T_CONFIG'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);	
        }
        
function _HELP() {
						JToolBarHelper::custom( 'upload', 'upload','upload',JText::_('T_UPLOAD'),false);
						JToolBarHelper::spacer();
                        JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'config', 'config','config',JText::_('T_CONFIG'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'user', 'user','user',JText::_('T_USER'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);		
        }        
        
function _UPLOAD() {
						JToolBarHelper::custom( 'config', 'config','config',JText::_('T_CONFIG'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'user', 'user','user',JText::_('T_USER'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);
				}

function _LIST() {
						JToolBarHelper::custom( 'upload', 'upload','upload',JText::_('T_UPLOAD'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'user', 'user','user',JText::_('T_USER'),false);
						JToolBarHelper::spacer();
                        JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::addNew('newConfig');
						JToolBarHelper::editList('edit');
						JToolBarHelper::custom( 'copyConfig', 'copy', 'copy', JText::_('T_COPY'), false );
						JToolBarHelper::save('saveMainConfig');
						JToolBarHelper::deleteList('','deleteConfig');
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);	
        }
               
function _DEFAULT() {
						JToolBarHelper::help('tfu',true);	
						JToolBarHelper::cancel();
        }
}
?>