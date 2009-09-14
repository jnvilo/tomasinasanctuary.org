<?php
/*
// JoomlaWorks "Simple Image Gallery PRO" Plugin for Joomla! 1.5.x - Version 2.0.4
// Copyright (c) 2006 - 2009 JoomlaWorks Ltd.
// This code cannot be redistributed without permission from JoomlaWorks
// More info at http://www.joomlaworks.gr
// Designed and developed by JoomlaWorks
// ***Last update: May 4th, 2009***
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$relTag = "prettyPhoto";
$popupIncludes = '
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">google.load("jquery", "1.3.2");</script>
<script src="'.$popupPath.'/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
<!--[if IE 6]>
<script type="text/javascript" src="'.$popupPath.'/js/DD_belatedPNG_0.0.7a-min.js"></script>
<script type="text/javascript">
	DD_belatedPNG.fix(\'.pp_left,.pp_right,a.pp_close,a.pp_arrow_next,a.pp_arrow_previous,.pp_content,.pp_middle\');     
</script>
<![endif]-->
<script type="text/javascript" charset="utf-8">
	jQuery(function($) {
		$("a[rel^=\'prettyPhoto\']").prettyPhoto({
			animationSpeed: \'normal\', /* fast/slow/normal */
			padding: 40, /* padding for each side of the picture */
			opacity: 0.35, /* Value betwee 0 and 1 */
			showTitle: true, /* true/false */
			allowresize: true, /* true/false */
			counter_separator_label: \'/\', /* The separator for the gallery counter 1 "of" 2 */
			theme: \'light_rounded\' /* light_rounded / dark_rounded / light_square / dark_square */
		});
	});
</script>
<link rel="stylesheet" href="'.$popupPath.'/css/prettyPhoto.css" type="text/css" media="screen" />
';
