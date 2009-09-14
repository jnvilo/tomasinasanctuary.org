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

$relTag = "highslide";
$extraClass = " highslide";
$popupIncludes = '
<link rel="stylesheet" type="text/css" href="'.$popupPath.'/highslide.css" />
<script type="text/javascript" src="'.$popupPath.'/highslide-full.packed.js"></script>
<script type="text/javascript">
  // override Highslide settings here
  // instead of editing the highslide.js file
	hs.graphicsDir = \''.$popupPath.'/graphics/\';
	hs.align = \'center\';
	hs.transitions = [\'expand\', \'crossfade\'];
	hs.outlineType = \'rounded-white\';
	hs.fadeInOut = true;
	//hs.dimmingOpacity = 0.75;

	// Add the controlbar
	hs.addSlideshow({
		interval: 5000,
		repeat: false,
		useControls: true,
		fixedControls: \'fit\',
		overlayOptions: {
			opacity: .75,
			position: \'bottom center\',
			hideOnMouseOut: true
		}
	});
	
	// Load HighSlide
	function loadHighSlide() {
		if(!document.getElementsByTagName) return false;
		if(!document.getElementById) return false;
		var a = document.getElementsByTagName("a");
		for(var i=0; i<a.length; i++){
			if(/highslide/.test(a[i].getAttribute("class"))){
				a[i].onclick = function(){
					return hs.expand(this);
					return false;
				}
			}
		}
	}
	
	// Loader
	function addLoadEvent(func) {
	  var oldonload = window.onload;
	  if (typeof window.onload != \'function\') {
	    window.onload = func;
	  } else {
	    window.onload = function() {
	      if (oldonload) {
	        oldonload();
	      }
	      func();
	    }
	  }
	}
	
	// Load HighSlide
	addLoadEvent(loadHighSlide);
</script>
';
