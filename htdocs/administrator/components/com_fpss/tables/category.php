<?php
/*
// "Frontpage Slideshow" Component for Joomla! 1.5.x - Version 2.0.0
// Copyright (c) 2006 - 2008 JoomlaWorks. All rights reserved.
// This code cannot be redistributed without permission from JoomlaWorks - http://www.joomlaworks.gr.
// More info at http://www.joomlaworks.gr and http://www.frontpageslideshow.net
// Designed and developed by the JoomlaWorks team
// ***Last update: September 1st, 2008***
*/

defined('_JEXEC') or die('Restricted access');

class TableCategory extends JTable
{
	var $id 				= null;
	var $name 				= null;
	var $width 				= null;
	var $quality 			= null;
	var $width_thumb 		= null;
	var $quality_thumb 		= null;
	var $published 			= null;
	
	function __construct(&$db)
	{
		parent::__construct( _FPSS_TABLE_CATEGORIES, 'id', $db );
	}
}
?>