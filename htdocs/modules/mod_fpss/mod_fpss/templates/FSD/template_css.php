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
#fpss-outer-container {/*clear:both;*/width:<?php echo $width+$sidebar_width; ?>px;margin:4px auto;padding:0 16px;border:1px solid #b0b0b0;background:#fff;}
#fpss-container {/*clear:both;*/margin:0;padding:0;position:relative;width:<?php echo $width+$sidebar_width; ?>px;}
#fpss-slider {overflow:hidden;background:none;/*clear:both;*/width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}

/* --- Slideshow Block --- */
.slide {position:absolute;right:0;width:<?php echo $width+$sidebar_width; ?>px;}
#slide-wrapper {display:none;font-size:11px;text-align:left;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-loading {background:#fff url(loading.gif) no-repeat center;text-align:center;margin-left:<?php echo $sidebar_width; ?>px;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;}
#slide-outer {height:<?php echo $height; ?>px;}
#slide-outer .slide-inner {position:relative;margin:0;color:#fff;overflow:hidden;height:<?php echo $height; ?>px;}
#slide-outer .slide-inner a.fpss_img {display:block;margin:0 0 0 <?php echo $sidebar_width; ?>px;padding:16px 0;border-left:1px solid #7b7b7b;overflow:hidden;}
#slide-outer .slide-inner a.fpss_img span {display:block;margin:0 0;overflow:hidden;height:<?php echo $height-32; ?>px;position:relative;}
#slide-outer .slide-inner a.fpss_img span span {margin:0;}
#slide-outer .slide-inner a.fpss_img span span span {background:url(readmore.png) no-repeat right bottom;}
#slide-outer .slide-inner a.fpss_img span span span img {display:none;}

/* --- Content --- */
.fpss-introtext {margin:0;padding:0;position:absolute;top:0;left:0;overflow:hidden;background:#fff;width:<?php echo $sidebar_width; ?>px;height:<?php echo $height-80; ?>px;}
.fpss-introtext .slidetext {padding:16px 8px 4px 2px;}

/* --- Navigation Buttons --- */
#navi-outer {position:absolute;bottom:0;left:0;/*clear:both;*/margin:8px 0 16px 0;padding:0;width:<?php echo $sidebar_width; ?>px;overflow:hidden;display:block;}
#navi-outer ul {margin:0;padding:0 16px;text-align:right;}
#navi-outer li {display:inline;background:none;padding:0;margin:0;}
#navi-outer li a,#navi-outer li a:hover,#navi-outer li a.navi-active {display:block;float:left;overflow:hidden;width:30px;height:30px;padding:0;margin:2px;text-decoration:none;line-height:40px;background:#404040;}
#navi-outer li a {border:1px solid #bbb;}
#navi-outer li a:hover {border:1px solid #ff9900;}
#navi-outer li a.navi-active {border:1px solid #ff9900;}
#navi-outer li a img,#navi-outer li a:hover img,#navi-outer li a.navi-active img {height:80px;width:auto;display:block;margin:-5px 0 0 -55px;}
#navi-outer li a img {opacity:0.6;-moz-opacity:0.6;filter:alpha(opacity=60);}
#navi-outer li a:hover img {opacity:1.0;-moz-opacity:1.0;filter:alpha(opacity=100);}
#navi-outer li a.navi-active img {opacity:1.0;-moz-opacity:1.0;filter:alpha(opacity=100);}
#navi-outer li a span.navbar-img {}
#navi-outer li a span.navbar-key {padding:0 2px;}
#navi-outer li a span.navbar-title {display:none;}
#navi-outer li a span.navbar-tagline {display:none;}
#navi-outer li a span.navbar-clr {display:none;}
#navi-outer li.noimages {display:none;}

/* --- Notice: Add custom text styling here to overwrite your template's CSS styles! --- */
.fpss-introtext .slidetext h1 {font-family:Georgia, "Times New Roman", Times, serif;font-size:18px;margin:0;padding:0;color:#0088bf;line-height:22px;}
.fpss-introtext .slidetext h1 a {font-family:Georgia, "Times New Roman", Times, serif;font-size:18px;color:#0088bf;}
.fpss-introtext .slidetext h1 a:hover {font-family:Georgia, "Times New Roman", Times, serif;font-size:18px;color:#cc3300;}
.fpss-introtext .slidetext h2 {font-size:10px;margin:0;padding:0;color:#999;}
.fpss-introtext .slidetext h3 {font-size:11px;margin:0;padding:0;display:none;}
.fpss-introtext .slidetext p {margin:0;padding:0;color:#333;}
.fpss-introtext .slidetext a.readon {display:none;}
.fpss-introtext .slidetext a.readon:hover {display:none;}

/* --- Generic Styling (highly recommended) --- */
a:active,a:focus {outline:0;}
#fpss-container img {border:none;}
.fpss-introtext .slidetext img,
.fpss-introtext .slidetext p img {display:none;} /* this will hide images inside the introtext */
.fpss-clr {/*clear:both;*/height:0;line-height:0;}

/* --- End of stylesheet --- */