<?php
header("Content-type: text/css; charset: UTF-8");
$width = $_GET['w'];
$height = $_GET['h'];
$sidebar_width = $_GET['sw'];
?>
/*
// "Frontpage Slideshow" Module for Joomla! 1.5.x - Version 2.0.0
// Copyright (c) 2006 - 2008 JoomlaWorks. All rights reserved.
// This code cannot be redistributed without permission from JoomlaWorks - http://www.joomlaworks.gr.
// More info at http://www.joomlaworks.gr and http://www.frontpageslideshow.net
// Designed and developed by the JoomlaWorks team
// ***Last update: September 1st, 2008***
*/

/* --- Slideshow Containers --- */
#fpss-outer-container {padding:2px;margin:8px auto;overflow:hidden;border:1px solid #ccc;width:<?php echo $width; ?>px;} /* This element controls the slideshow spacing and border */
#fpss-container {position:relative;margin:0;padding:0;clear:both;}
#fpss-slider {background:none;overflow:hidden;clear:both;text-align:left;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;} /* Add bg color if using bg image on #fpss-outer-container */
#slide-loading {background:#fff url(loading.gif) no-repeat center;text-align:center;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper {display:none;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer {height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide {position:absolute;overflow:hidden;right:0;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide .slide-inner {height:<?php echo $height; ?>px;position:relative;margin:0;overflow:hidden;text-align:left;z-index:8;}
#slide-wrapper #slide-outer .slide .slide-inner a.fpss_img span span span {background:none;}			

/* --- Content --- */
.fpss-introtext {width:100%;margin:0;padding:0;position:absolute;left:0;right:0;bottom:0;background:url(transparent_bg.png);}
.fpss-introtext .slidetext {padding:4px 8px;}

/* --- Navigation Buttons --- */
#navi-outer {clear:both;margin:0;padding:0;color:#fff;height:27px;background:url(navbar.png) repeat top;overflow:hidden;}
#navi-outer ul {margin:2px 0;padding:0 4px;text-align:right;}
#navi-outer li {display:inline;background:none;padding:0;margin:0;}
#navi-outer li a,#navi-outer li a:hover,#navi-outer li a.navi-active {display:block;float:left;overflow:hidden;width:40px;height:22px;padding:0;margin:0 2px;text-decoration:none;line-height:22px;background:#404040;}
#navi-outer li a {border:1px solid #fff;}
#navi-outer li a:hover,
#navi-outer li a.navi-active {border:1px solid #ff9900;}
#navi-outer li a img,#navi-outer li a:hover img,#navi-outer li a.navi-active img {height:80px;width:auto;display:block;margin:-15% 0 0 -70%;}
#navi-outer li a img {opacity:0.6;-moz-opacity:0.6;filter:alpha(opacity=60);}
#navi-outer li a:hover img {opacity:1.0;-moz-opacity:1.0;filter:alpha(opacity=100);}
#navi-outer li a.navi-active img {opacity:1.0;-moz-opacity:1.0;filter:alpha(opacity=100);}
#navi-outer li a span.navbar-img {}
#navi-outer li a span.navbar-key {display:none;}
#navi-outer li a span.navbar-title {display:none;}
#navi-outer li a span.navbar-tagline {display:none;}
#navi-outer li a span.navbar-clr {display:none;}
#navi-outer li.noimages {margin:0;padding:0;}
#navi-outer li.noimages a {display:block;float:right;width:12px;height:18px;line-height:18px;margin:4px 0 0 0;padding:0;border:none;text-align:center;background:none;color:#999;}
#navi-outer li.noimages a:hover {display:block;float:right;width:12px;height:18px;line-height:18px;margin:4px 0 0 0;padding:0;border:none;text-align:center;background:none;color:#555;}
#navi-outer li.noimages a#fpss-container_prev {background:url(prev.gif) no-repeat 50% 50%;}
#navi-outer li.noimages a#fpss-container_playButton {width:40px;background:none;}
#navi-outer li.noimages a#fpss-container_next {background:url(next.gif) no-repeat 50% 50%;}
#navi-outer li.clr {clear:both;}

/* --- Notice: Add custom text styling here to overwrite your template's CSS styles! --- */
.fpss-introtext .slidetext h1 {font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:26px;line-height:26px;margin:0;padding:0;color:#fff;}
.fpss-introtext .slidetext h1 a {font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:26px;margin:0;padding:0;color:#fafafa;}
.fpss-introtext .slidetext h1 a:hover {font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:26px;margin:0;padding:0;color:#f00;}
.fpss-introtext .slidetext h2 {font-size:11px;margin:0;padding:0;color:#999;font-weight:normal;}
.fpss-introtext .slidetext h3 {font-size:11px;margin:0;padding:0;display:none;}
.fpss-introtext .slidetext p {margin:4px 0;padding:0;color:#fff;}
.fpss-introtext .slidetext a.readon {display:none;}
.fpss-introtext .slidetext a.readon:hover {display:none;}

/* --- Generic Styling (highly recommended) --- */
a:active,a:focus {outline:0;}
#fpss-container img {border:none;}
.fpss-introtext .slidetext img,
.fpss-introtext .slidetext p img {display:none;} /* this will hide images inside the introtext */
.fpss-clr {clear:both;height:0;line-height:0;}

/* --- End of stylesheet --- */