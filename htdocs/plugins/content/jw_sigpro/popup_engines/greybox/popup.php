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

$relTag = "gb_imageset";
$extraClass = "";
$popupIncludes = '
<script type="text/javascript">
    var GB_ROOT_DIR = "'.$popupPath.'/";
</script>
<script type="text/javascript" src="'.$popupPath.'/AJS.js"></script>
<script type="text/javascript" src="'.$popupPath.'/AJS_fx.js"></script>
<script type="text/javascript" src="'.$popupPath.'/gb_scripts.js"></script>
<link href="'.$popupPath.'/gb_styles.css" rel="stylesheet" type="text/css" />
';
