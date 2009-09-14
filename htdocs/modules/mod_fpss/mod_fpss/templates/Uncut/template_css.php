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
#fpss-outer-container {padding:0;margin:8px auto;border:1px solid #333;width:<?php echo $width+$sidebar_width; ?>px;}
#fpss-container {/*clear:both;*/padding:0;margin:0;position:relative;text-align:left;width:<?php echo $width+$sidebar_width; ?>px;}
#fpss-slider {overflow:hidden;background:none;/*clear:both;*/width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}

#slide-loading {background:#000 url(loading_black.gif) no-repeat center;margin:0;padding:0;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper {display:none;font-size:11px;text-align:left;}
#slide-wrapper #slide-outer {height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide {position:absolute;overflow:hidden;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide .slide-inner {position:relative;margin:0;color:#fff;overflow:hidden;background:#141414;text-align:left;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide .slide-inner a.fpss_img span span span {background:none;}

/* --- Content --- */
.fpss-introtext {width:auto;width:100%;margin:0;padding:0;position:absolute;bottom:0;left:0;right:0;background:url(transparent_bg.png);}
.fpss-introtext .slidetext {margin:0;padding:4px 12px;}

/* --- Navigation Buttons --- */
#navi-outer {position:absolute;top:0;right:0;/*clear:both;*/margin:0;padding:0;color:#fff;background:#363636;overflow:hidden;width:<?php echo $sidebar_width; ?>px;height:<?php echo $height; ?>px;}
#navi-outer ul {padding:0;margin:0;background:none;text-align:left;}
#navi-outer li {display:inline;padding:0;margin:0;border:none;height:56px;list-style:none;background:none;}
#navi-outer a {display:block;padding:4px;margin:0;text-decoration:none;background:#141414 url(nav.gif) repeat-x bottom;height:52px;border-bottom:1px solid #333;overflow:hidden;font-weight:normal;color:#aaa;line-height:10px;}
#navi-outer a:hover,
#navi-outer a.navi-active {display:block;padding:4px;margin:0;text-decoration:none;background:#c32851 url(nav-over.gif) repeat-x bottom;height:52px;border-bottom:1px solid #333;overflow:hidden;font-weight:normal;color:#aaa;line-height:10px;}

#navi-outer li a span.navbar-img {display:none;}
#navi-outer li a span.navbar-key {display:none;}
#navi-outer li a span.navbar-title {display:block;font-size:13px;font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-weight:bold;color:#fff;line-height:13px;}
#navi-outer li a span.navbar-tagline {font-weight:normal;font-size:10px;color:#fff;margin:0;padding:0;}
#navi-outer li a:hover span.navbar-tagline,
#navi-outer li a.navi-active span.navbar-tagline {font-weight:normal;font-size:10px;color:#fff;margin:0;padding:0;line-height:12px;}
span.navbar-clr {display:block;/*clear:both;*/}
#navi-outer li a span.navbar-clr {display:none;}
#navi-outer li.noimages {display:none;}

/* --- Notice: Add custom text styling here to overwrite your template's CSS styles! --- */
.fpss-introtext .slidetext h1 {color:#fff;font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:20px;margin:0;padding:0;line-height:20px;}
.fpss-introtext .slidetext h1 a {color:#fff;font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:20px;margin:0;padding:0;}
.fpss-introtext .slidetext h1 a:hover {color:#fff;font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:20px;margin:0;padding:0;}
.fpss-introtext .slidetext h2 {display:none;}
.fpss-introtext .slidetext h3 {display:none;}
.fpss-introtext .slidetext p {margin:4px 0;padding:0;color:#fff;}
.fpss-introtext .slidetext a.readon {display:none;}
.fpss-introtext .slidetext a.readon:hover {display:none;}

/* --- Generic Styling (highly recommended) --- */
a:active,a:focus {outline:0;}
#fpss-container img {border:none;}
.fpss-introtext .slidetext img,
.fpss-introtext .slidetext p img {display:none;} /* this will hide images inside the introtext */
.fpss-clr {/*clear:both;*/height:0;line-height:0;}

/* --- End of stylesheet --- */