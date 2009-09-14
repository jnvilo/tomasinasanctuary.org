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
#fpss-outer-container {padding:4px;margin:8px auto;overflow:hidden;border-top:1px solid #ccc;border-left:1px solid #ccc;border-right:2px solid #ccc;border-bottom:2px solid #ccc;width:<?php echo $width; ?>px;} /* This element controls the slideshow spacing and border */
#fpss-container {position:relative;margin:0;padding:0;clear:both;width:<?php echo $width; ?>px;}
#fpss-slider {background:none;overflow:hidden;clear:both;text-align:left;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-loading {background:#fff url(loading.gif) no-repeat center;text-align:center;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper {display:none;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer {height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide {position:absolute;overflow:hidden;right:0;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide .slide-inner {position:relative;margin:0;color:#fff;overflow:hidden;background:#3a3a3a;text-align:left;z-index:8;height:<?php echo $height; ?>px;}
#slide-wrapper #slide-outer .slide .slide-inner a.fpss_img span span span {background:none;}

/* --- Content --- */
.fpss-introtext {width:100%;margin:0;padding:0;position:absolute;left:0;right:0;bottom:0;background:url(transparent_bg.png);}
.fpss-introtext .slidetext {padding:4px 8px 2px 8px;}

/* --- Navigation Buttons --- */
#navi-outer {clear:both;margin:0;padding:0;border-top:2px solid #505050;background:url(black_stripes.gif) repeat top;overflow:hidden;}
#navi-outer ul {margin:0;padding:2px 16px;list-style:none;background:none;text-align:left;float:left;}
#navi-outer li {padding:0;margin:0;border:none;list-style:none;display:inline;}
#navi-outer li a,
#navi-outer li a:hover,
#navi-outer li a.navi-active {display:block;float:left;overflow:hidden;width:50px;height:35px;padding:0px;margin:0px 2px;text-decoration:none;line-height:35px;background:#404040;}
#navi-outer li a {border:1px solid #bbb;}
#navi-outer li a:hover,
#navi-outer li a.navi-active {border:1px solid #f90;}
#navi-outer li a img,
#navi-outer li a:hover img,
#navi-outer li a.navi-active img {display:block;width:200px;height:auto;margin:-20px 0 0 -80px;padding:0;}
#navi-outer li a span.navbar-img {}
#navi-outer li a span.navbar-img img {opacity:0.6;-moz-opacity:0.6;filter:alpha(opacity=60);}
#navi-outer li a:hover span.navbar-img img,
#navi-outer li a.navi-active span.navbar-img img {opacity:1.0;-moz-opacity:1.0;filter:alpha(opacity=100);}
#navi-outer li a span.navbar-key {display:none;}
#navi-outer li a span.navbar-title {display:none;}
#navi-outer li a span.navbar-tagline {display:none;}
#navi-outer li a span.navbar-clr {display:none;}
#navi-outer li.noimages a {font-family:Tahoma, Arial, sans-serif;font-size:10px;border:none;text-align:center;width:auto;padding:0px 4px;margin:0px;background:none;color:#9c0;}
#navi-outer li.noimages a:hover {font-family:Tahoma, Arial, sans-serif;font-size:10px;border:none;text-align:center;width:auto;padding:0px 4px;margin:0px;background:none;color:#f90;}

/* --- Notice: Add custom text styling here to overwrite your template's CSS styles! --- */
.fpss-introtext .slidetext h1 {font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:26px;line-height:26px;margin:0;padding:0;color:#fff;}
.fpss-introtext .slidetext h1 a {font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:26px;margin:0;padding:0;color:#9c0;}
.fpss-introtext .slidetext h1 a:hover {font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:26px;margin:0;padding:0;color:#f90;}
.fpss-introtext .slidetext h2 {font-size:10px;margin:0;padding:0;color:#999;font-weight:normal;}
.fpss-introtext .slidetext h3 {display:none;}
.fpss-introtext .slidetext p {margin:4px 0;padding:0;color:#fff;}
.fpss-introtext .slidetext a.readon {margin:0;padding:1px 0;background:none;border:none;color:#fff;line-height:14px;}
.fpss-introtext .slidetext a.readon:hover {margin:0;padding:1px 0;background:none;border:none;color:#f90;line-height:14px;}

/* --- Generic Styling (highly recommended) --- */
a:active,a:focus {outline:0;}
#fpss-container img {border:none;}
.fpss-introtext .slidetext img,
.fpss-introtext .slidetext p img {display:none;} /* this will hide images inside the introtext */
.fpss-clr {clear:both;height:0;line-height:0;}

/* --- End of stylesheet --- */