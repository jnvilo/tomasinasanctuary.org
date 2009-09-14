<?php
/**
* @version		$Id: view.html.php 10906 2008-09-05 07:27:34Z rhuk $
* @package		RokCandy
* @copyright	Copyright (C) 2008 - 2009 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'categories.php' );

class RokCandyViewRokCandy extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db		    =& JFactory::getDBO();
		$uri	    =& JFactory::getURI();
		$document   =& JFactory::getDocument();
		
		$document->addStyleSheet('components/'.$option.'/assets/rokcandy.css');

		$filter_state		= $mainframe->getUserStateFromRequest( $option.'filter_state',		'filter_state',		'',				'word' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'filter_catid',		'filter_catid',		0,				'int' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'filter_catid',		'filter_catid',		0,				'int' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// Get data from the model
		if ($filter_catid != -1) {
		    $items		= & $this->get( 'Data');
	    } else {
	        $items      = array();
	    }
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );
		
		if ($filter_catid == -1 or $filter_catid == 0) {
		    $overrides  = & $this->get( 'TemplateOverrides' );
		} else {
		    $overrides = array();
		}
		
		// build list of categories
		$javascript 	= 'onchange="document.adminForm.submit();"';
		$lists['catid'] = JElementRokCandyList::getCategories('filter_catid',  $option, intval( $filter_catid ), $javascript );

		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('overrides',   $overrides);

		parent::display($tpl);
	}
}