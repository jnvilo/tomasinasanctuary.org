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

$relTag = "group";
$popupIncludes = '
<link rel="stylesheet" type="text/css" href="'.$popupPath.'/jquery.fancybox.css" media="screen" />
<!--[if IE]>
<style type="text/css" media="screen">
	div#fancy_title {filter:alpha(opacity=90);}
	div#fancy_title table { width:90%; margin:0 auto; }
</style>
<![endif]-->
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">google.load("jquery", "1.3.2");</script>
<script type="text/javascript" src="'.$popupPath.'/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="'.$popupPath.'/jquery.fancybox-1.2.1.pack.js"></script>
<script type="text/javascript">
	jQuery(function($) {
		$(".sig-link").fancybox({
			\'overlayShow\': true
		});
	});
</script>
';
