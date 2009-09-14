<?php
/*
// "Frontpage Slideshow" Component for Joomla! 1.5.x - Version 2.0.0
// Copyright (c) 2006 - 2008 JoomlaWorks. All rights reserved.
// This code cannot be redistributed without permission from JoomlaWorks - http://www.joomlaworks.gr.
// More info at http://www.joomlaworks.gr and http://www.frontpageslideshow.net
// Designed and developed by the JoomlaWorks team
// ***Last update: September 1st, 2008***
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class HTML_FPSlideShow {

	function showHeaderIM($option, $task) {
		global $mainframe, $option, $Itemid;

		$jw_fpss_head = '
<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 starts here -->
<link href="administrator/components/'.$option.'/style/fpss.css" rel="stylesheet" type="text/css" />
<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 ends here -->
			';
		$mainframe->addCustomHeadTag($jw_fpss_head);

		echo '
<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 starts here -->
<div id="jwfpss" class="jwfpss-fp">
<h2 id="jwfpss-logo"></h2>
		';
	}

	function showFooter() {
		echo '
	<div id="jwfpss-footer">'._FPSS_COPYRIGHTS.'</div>
</div>
<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 ends here -->
	';
	}
	// END CLASS
}

?>