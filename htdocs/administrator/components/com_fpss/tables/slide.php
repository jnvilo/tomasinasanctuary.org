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

class TableSlide extends JTable
{
	var $id 			= null;
	var $catid			= null;
	var $name 			= null;
	var $path 			= null;
	var $path_type 		= null;
	var $thumb 			= null;
	var $state 			= null;
	var $publish_up 	= null;
	var $publish_down 	= null;
	var $itemlink 		= null;
	var $menulink 		= null;
	var $target 		= null;
	var $customlink 	= null;
	var $nolink 		= null;
	var $ctext 			= null;
	var $plaintext 		= null;
	var $registers 		= null;
	var $showtitle 		= null;
	var $showseccat 	= null;
	var $showcustomtext = null;
	var $showplaintext 	= null;
	var $showreadmore 	= null;
	var $ordering 		= null;
	
	function __construct(&$db)
	{
		parent::__construct( _FPSS_TABLE_SLIDES, 'id', $db );
	}
}
?>