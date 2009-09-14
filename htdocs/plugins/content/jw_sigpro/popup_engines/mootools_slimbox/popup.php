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

$relTag = "lightbox";
$popupIncludes = '
'.JHTML::_('behavior.mootools').'
<script type="text/javascript" src="'.$popupPath.'/js/slimbox.js"></script>
<link href="'.$popupPath.'/css/slimbox.css" rel="stylesheet" type="text/css" />
';
