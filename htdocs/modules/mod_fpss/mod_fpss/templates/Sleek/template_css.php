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
#fpss-outer-container {width:<?php echo $width; ?>px;padding:0;margin:4px auto;border:2px solid #ccc;}
#fpss-container {/*clear:both;*/border:none;padding:0;margin:0;position:relative;width:<?php echo $width; ?>px;}
#fpss-slider {overflow:hidden;background:none;/*clear:both;*/width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-loading {background:#000 url(loading_black.gif) no-repeat center;text-align:center;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper {display:none;font-size:11px;text-align:left;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer {height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide {position:absolute;right:0;overflow:hidden;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide .slide-inner {position:relative;margin:0px;color:#fff;overflow:hidden;background:#000;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide .slide-inner a.fpss_img span span span {background:none;}

/* --- Content --- */
.fpss-introtext {margin:0;padding:0;position:absolute;bottom:25px;left:0;background:url(transparent_bg.png);width:<?php echo $width-$sidebar_width-30; ?>px;height:34px;overflow:hidden;}
.fpss-introtext .slidetext {margin:0;padding:0 8px;font-size:11px;}

/* --- Navigation Buttons --- */
#pseudobox {position:absolute;top:7px;left:0;right:0;height:34px;margin:0;padding:0;background:#444;opacity:0.8;-moz-opacity:0.8;filter:alpha(opacity=80);width:<?php echo $sidebar_width; ?>px;}
#navi-outer {position:absolute;right:0;z-index:9;bottom:16px;width:<?php echo $sidebar_width; ?>px;}
#navi-outer ul {margin:0;padding:0;text-align:right;display:block;}
#navi-outer li {display:inline;background:none;padding:0;margin:0;}
#navi-outer li a,
#navi-outer li a:hover,
#navi-outer li a.navi-active {display:block;float:left;overflow:hidden;width:50px;height:50px;padding:0;margin:0 2px;text-decoration:none;position:relative;}
#navi-outer li a {background:none;}
#navi-outer li a:hover,
#navi-outer li a.navi-active {background:url(nav-current.gif) no-repeat 49% 0;}
#navi-outer li a img,
#navi-outer li a:hover img,
#navi-outer li a.navi-active img {width:45px;height:30px;margin:8px 0 0 0;padding:0;}
#navi-outer li a img {opacity:0.7;-moz-opacity:0.7;filter:alpha(opacity=70);border:1px solid #aaa;}
#navi-outer li a:hover img,
#navi-outer li a.navi-active img {opacity:1.0;-moz-opacity:1.0;filter:alpha(opacity=100);border:1px solid #fff;width:48px;height:32px;margin:6px 0 0 0;padding:0;}
#navi-outer li a span.navbar-key {display:none;}
#navi-outer li a span.navbar-title {display:none;}
#navi-outer li a span.navbar-tagline {display:none;}
#navi-outer li a span.navbar-clr {display:none;}
#navi-outer li.noimages a {font-family:Tahoma, Arial, sans-serif;font-size:10px;border:none;text-align:center;width:auto;padding:0 4px;margin:0;background:none;}
#navi-outer li.noimages a:hover {font-family:Tahoma, Arial, sans-serif;font-size:10px;border:none;text-align:center;width:auto;padding:0 4px;margin:0;background:none;color:#FF9900;}
#navi-outer li.noimages a#fpss-container_prev {background:url(nav-prev.gif) no-repeat center 50%;font-size:0;margin:0 2px;text-indent:-9999px;overflow:hidden;}
#navi-outer li.noimages a#fpss-container_playButton {display:none;}
#navi-outer li.noimages a#fpss-container_next {background:url(nav-next.gif) no-repeat center 50%;font-size:0;margin:0 2px;text-indent:-9999px;overflow:hidden;}

/* Notice: Add custom text styling here to overwrite your template's CSS styles! */
.fpss-introtext .slidetext h1 {font-size:14px;margin:0;padding:0;color:#dcdcdc;font-weight:bold;}
.fpss-introtext .slidetext h1 a {color:#9c0;font-weight:bold;font-size:13px;}
.fpss-introtext .slidetext h1 a:hover {color:#f90;font-weight:bold;font-size:13px;}
.fpss-introtext .slidetext h2 {display:none;}
.fpss-introtext .slidetext h3 {margin:0;padding:0;color:#fff;font-size:11px;}
.fpss-introtext .slidetext p {display:none;}
.fpss-introtext .slidetext a.readon {display:none;}
.fpss-introtext .slidetext a.readon:hover {display:none;}

/* --- Generic Styling (highly recommended) --- */
a:active,a:focus {outline:0;}
#fpss-container img {border:none;}
.fpss-introtext .slidetext img,
.fpss-introtext .slidetext p img {display:none;} /* this will hide images inside the introtext */
.fpss-clr {/*clear:both;*/height:0;line-height:0;}

/* --- End of stylesheet --- */