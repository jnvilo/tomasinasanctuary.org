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

$relTag = "colorbox";
$style = "example1";
$popupIncludes = '
<link type="text/css" media="screen" rel="stylesheet" href="'.$popupPath.'/colorbox/colorbox.css" />
<link type="text/css" media="screen" rel="stylesheet" href="'.$popupPath.'/'.$style.'/colorbox-custom.css" />
<!--[if lte IE 6]>
<style type="text/css" media="screen">
	#borderTopLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$popupPath.'/'.$style.'/images/borderTopLeft.png, sizingMethod=\'scale\');}
	#borderTopCenter{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$popupPath.'/'.$style.'/images/borderTopCenter.png, sizingMethod=\'scale\');}
	#borderTopRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$popupPath.'/'.$style.'/images/borderTopRight.png, sizingMethod=\'scale\');}
	#borderBottomLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$popupPath.'/'.$style.'/images/borderBottomLeft.png, sizingMethod=\'scale\');}
	#borderBottomCenter{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$popupPath.'/'.$style.'/images/borderBottomCenter.png, sizingMethod=\'scale\');}
	#borderBottomRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$popupPath.'/'.$style.'/images/borderBottomRight.png, sizingMethod=\'scale\');}
	#borderMiddleLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$popupPath.'/'.$style.'/images/borderMiddleLeft.png, sizingMethod=\'scale\');}
	#borderMiddleRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$popupPath.'/'.$style.'/images/borderMiddleRight.png, sizingMethod=\'scale\');}
	
	#contentTitle {left:1px;}
</style>
<![endif]-->
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">google.load("jquery", "1.3.2");</script>
<script type="text/javascript" src="'.$popupPath.'/colorbox/jquery.colorbox.js"></script>
<script type="text/javascript">
	jQuery(function($) {
		$.fn.colorbox.settings.transition = "fade";
		$.fn.colorbox.settings.bgOpacity = "0.9";
		$.fn.colorbox.settings.contentCurrent = "image {current} of {total}";
		$(".sig-link").colorbox();
	});
</script>
';
