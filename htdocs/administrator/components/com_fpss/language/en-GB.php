<?php
/*
// "Frontpage Slideshow" Component for Joomla! 1.5.x - Version 2.0.0
// Copyright (c) 2006 - 2008 JoomlaWorks. All rights reserved.
// This code cannot be redistributed without permission from JoomlaWorks - http://www.joomlaworks.gr.
// More info at http://www.joomlaworks.gr and http://www.frontpageslideshow.net
// Designed and developed by the JoomlaWorks team
// ***Last update: September 1st, 2008***
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
	
define("_FPSS_CATEGORIES_CATEGORYNAME","Category name");	
define("_FPSS_CATEGORIES_EDIT_CATEGORY","Edit category");	
define("_FPSS_CATEGORIES_ID","ID");	
define("_FPSS_CATEGORIES_ITEMS_DELETED","Categories deleted!");	
define("_FPSS_CATEGORIES_LIST","Categories (Slideshows)");	
define("_FPSS_CATEGORIES_NAME","Name");	
define("_FPSS_CATEGORIES_NEW_CATEGORY","New Category");	
define("_FPSS_CATEGORIES_NUM_SLIDES","No. of slides");	
define("_FPSS_CATEGORIES_PUBLISHED_DONE","Publish Done!");	
define("_FPSS_CATEGORIES_PUBLISHED","Published");	
define("_FPSS_CATEGORIES_SHOWSLIDES_LINK","Display the slides of this category");	
define("_FPSS_CATEGORIES_SHOWSLIDES","Slides");	
define("_FPSS_CATEGORIES_SUCCESS_SAVE","Successfully save of the category");	
define("_FPSS_CATEGORIES_UNPUBLISHED_DONE","Unpublish Done!");	
define("_FPSS_CATEGORY_ALERT_MUST_NAME","Error. Category must have a name!");	
define("_FPSS_CATEGORY_DEFAULT_QUALITY_THUMBS","Default resize quality (%) for uploaded thumbnails");	
define("_FPSS_CATEGORY_DEFAULT_QUALITY","Default resize quality (%) for uploaded images");	
define("_FPSS_CATEGORY_DEFAULT_WIDTH_THUMBS","Default resize width (px) for uploaded thumbnails");	
define("_FPSS_CATEGORY_DEFAULT_WIDTH","Default resize width (px) for uploaded images");	
define("_FPSS_CATEGORY_GENERALPARAMS","General options");	
define("_FPSS_CATEGORY_SHOWCUSTOMTEXT","Show custom text/introtext");	
define("_FPSS_CATEGORY_SHOWNOLINK","No link");	
define("_FPSS_CATEGORY_SHOWPARAMS","Category Parameters for content display");	
define("_FPSS_CATEGORY_SHOWPLAINTEXT","Show tagline text");	
define("_FPSS_CATEGORY_SHOWREADMORE","Show \"read more...\" link");	
define("_FPSS_CATEGORY_SHOWSECCAT","Show section/category <span>(if applicable)</span>");	
define("_FPSS_CATEGORY_SHOWTITLE","Show title");	
define("_FPSS_CATEGORY_SLIDEPARAMS","Category parameters for uploading images / thumbnails");	
define("_FPSS_CATEGORY","Category");	
define("_FPSS_CONFIG_ADVPARAMS","Image upload parameters");	
define("_FPSS_CONFIG_ARTICLELIST_TEXT","Optimize article list loading (uses Ajax)");	
define("_FPSS_CONFIG_BASEPATH_TEXT","Define the base path for browsing images through the built-in Image Browser");	
define("_FPSS_CONFIG_EDITOR","Enable WYSIWYG editor in \"custom text\" area");	
define("_FPSS_CONFIG_ERROR_PATH","Please provide a valid directory as the base path for the Image Browser.");	
define("_FPSS_CONFIG_FORMPARAMS","Slide new/edit page parameters");	
define("_FPSS_CONFIG_SAVED_ERROR","Error saving Global Configuration options");	
define("_FPSS_CONFIG_SAVED","Global Configuration options saved!");	
define("_FPSS_CONFIG_SEPTHUMB_TEXT","Enable separate thumbnail uploading form");	
define("_FPSS_CONFIG_SHOW_QUALITY","Show upload \"quality\" option for main slide image");																																																																																																					
define("_FPSS_CONFIG_SHOW_WIDTH","Show upload \"width\" option for main slide image");
define("_FPSS_CONFIG_TITLE","Global Configuration");
define("_FPSS_FE_CREDITS","About");
define("_FPSS_FE_SLIMBOX","This image has been assigned to slide:");
define("_FPSS_IMAGES_CHANGE_ACCESS_IMAGES","The \"access group\" for this slide has changed!");
define("_FPSS_IMAGES_FULLREORDERING_DONE","New ordering saved");
define("_FPSS_IMAGES_PUBLISHED_DONE","item(s) successfully published");
define("_FPSS_IMAGES_UNPUBLISHED_DONE","item(s) successfully unpublished");
define("_FPSS_IMGBR_CLICK","Navigate through the server folders and then click on an image to insert it on your slide.");
define("_FPSS_IMGBR_DIMENSIONS","Dimensions (px)");
define("_FPSS_IMGBR_DIR","Change directory");
define("_FPSS_IMGBR_FILENAME","Filename");
define("_FPSS_IMGBR_LIST","Browse images on this server");
define("_FPSS_IMGBR_PREVIEW","Preview Image");
define("_FPSS_IMGBR_SIZE","Size");
define("_FPSS_IMGBR_TITLE","Frontpage Slideshow: Image Browser");
define("_FPSS_LIST_OF_SLIDES","Slide List");
define("_FPSS_MOD_ALERT","<div class=\"message\">Please, install the JoomlaWorks \"Frontpage Slideshow\" Component first!</div>");
define("_FPSS_MOD_CLICKNAV","Click to navigate!");
define("_FPSS_MOD_IMGALT","Click on the slide!");
define("_FPSS_MOD_LOADING","Loading...");
define("_FPSS_MOD_NEXT","Next");
define("_FPSS_MOD_PAUSE","Pause");
define("_FPSS_MOD_PLAY","Play");
define("_FPSS_MOD_PLAYPAUSE","Play/Pause Slide");
define("_FPSS_MOD_PREV","Previous");
define("_FPSS_SEL_ACCESS","--- Select access group ---");
define("_FPSS_SEL_CATEGORIES_LABEL","Filter categories: ");
define("_FPSS_SEL_CATEGORIES","Select");
define("_FPSS_SEL_CATEGORY","--- Select category ---");
define("_FPSS_SEL_CONTENT","--- Select content item ---");
define("_FPSS_SEL_SECTION","--- Select section ---");
define("_FPSS_SHOW_CATEGORIES","Categories");
define("_FPSS_SHOW_CONFIG","Configuration");
define("_FPSS_SHOW_CREDITS","About");
define("_FPSS_SHOW_SLIDES","Slide List");
define("_FPSS_SLIDE_ALERT_MUST_CATEGORY","Error: Slide must have a category!");
define("_FPSS_SLIDE_ALERT_MUST_IMAGE","You must upload an image or browse for one first!");
define("_FPSS_SLIDE_ALERT_MUST_MENU","You must select slide target link!");
define("_FPSS_SLIDE_ALERT_MUST_NAME","Slide must have a name!");
define("_FPSS_SLIDE_ALERT_MUST_REGIST","You must select slide access group!");
define("_FPSS_SLIDE_BROWSE_EXP","(this image will be resized and transferred to the Frontpage Slideshow image folder)");
define("_FPSS_SLIDE_BROWSE","Click to browse...");
define("_FPSS_SLIDE_CUSTOM_PLAINTEXT","Tagline text");
define("_FPSS_SLIDE_CUSTOM_TEXT","Custom text <span>(will override existing content item introtext)</span>");
define("_FPSS_SLIDE_DISPLAYOPTIONS","Content Display Options");
define("_FPSS_SLIDE_EDIT_IMAGE","Edit slide: ");
define("_FPSS_SLIDE_EDIT_INTROTEXT","Text Options");
define("_FPSS_SLIDE_EXIMG_PATH","Image path");
define("_FPSS_SLIDE_EXIMG_PREVIEW","Current slide image preview (scaled down to 300px wide for viewing purposes only)");
define("_FPSS_SLIDE_EXTHUMB_PREVIEW","Thumb preview");
define("_FPSS_SLIDE_FINISH_PUBLISHING","Finish Publishing");
define("_FPSS_SLIDE_IMAGEACTION","Slide Image: <span>Upload an image or browse the server for one</span>");
define("_FPSS_SLIDE_IMAGENAME","Slide Name");
define("_FPSS_SLIDE_IMAGEPATH","Current image assigned on this slide");
define("_FPSS_SLIDE_LINKOPTIONS","Slide Link Options");
define("_FPSS_SLIDE_LINKS_CI","(a content item)");
define("_FPSS_SLIDE_LINKS_MI","(a menu item)");
define("_FPSS_SLIDE_LINKS_NOLINK","NO URL (use if you don't want your slides to redirect anywhere)");
define("_FPSS_SLIDE_LINKS_NORMAL","(a regular URL - make sure it starts with: http://)");
define("_FPSS_SLIDE_LINKTO","This slide links to");
define("_FPSS_SLIDE_NEW_IMAGE","Create a new slide");
define("_FPSS_SLIDE_NOACTION","Keep current image");
define("_FPSS_SLIDE_PUBLISHED","Published");
define("_FPSS_SLIDE_REGISTERS","User group access for this slide");
define("_FPSS_SLIDE_SAVED","Slide saved!");
define("_FPSS_SLIDE_SERVERIMAGE","Browse for an image on the server");
define("_FPSS_SLIDE_SHOWCUSTOMTEXT","Show custom text/introtext");
define("_FPSS_SLIDE_SHOWNOLINK","No link");
define("_FPSS_SLIDE_SHOWPLAINTEXT","Show tagline text");
define("_FPSS_SLIDE_SHOWREADMORE","Show \"read more...\" link");
define("_FPSS_SLIDE_SHOWSECCAT","Show section/category <span>(if applicable)</span>");
define("_FPSS_SLIDE_SHOWTITLE","Show title");
define("_FPSS_SLIDE_START_PUBLISHING","Start Publishing");
define("_FPSS_SLIDE_TARGET","Should the slide link open in a new browser window?");
define("_FPSS_SLIDE_THUMB_UPLOADFROMPC","Upload thumbnail image");
define("_FPSS_SLIDE_THUMBACTION","Navigation thumbnail image: Upload a thumbnail");
define("_FPSS_SLIDE_UPLOADFROMPC","Upload image");
define("_FPSS_SLIDES_ALERT_DELETE","Please confirm your actions");
define("_FPSS_SLIDES_BROWSE_EXP","(this image will be resized and transferred to the Frontpage Slideshow image folder)");
define("_FPSS_SLIDES_CUSTOM_TEXT","Custom text <span>(will override existing content item introtext)</span>");
define("_FPSS_SLIDES_ORDER","Order");
define("_FPSS_SLIDES_PATH","Image Path");
define("_FPSS_SLIDES_PREVIEW","Preview");
define("_FPSS_SLIDES_PUBLISHED","Published");
define("_FPSS_SLIDES_REGISTERS_ACCESS","Access");
define("_FPSS_SLIDES_REORDER","Re-order");
define("_FPSS_SLIDES_SELECT_MENU","--- Select Content Item ---");
define("_FPSS_SLIDES_SELECT_MENU2","--- Select target menu item ---");
define("_FPSS_SLIDES_SLIDENAME","Slide Name");
define("_FPSS_SLIDES_SUCC_DELETED","Slide(s) successfuly deleted");
define("_FPSS_SLIDES_UPLOADCUSTOM","<b>OPTIONAL:</b> Override default upload options by entering ");
define("_FPSS_SLIDES_UPLOADFROMPC_DEF_EXP"," (<b>NOTE:</b> The uploaded image will be resized and resampled, based on the corresponding settings in the \"Configuration\" page.)");
define("_FPSS_SLIDES_UPLOADFROMPC_EXP"," (<b>NOTE:</b> Entering a new value will override default settings.)");
define("_FPSS_SLIDES_UPLOADQUALITY","new image quality (%): ");
define("_FPSS_SLIDES_UPLOADWIDTH","new width (px): ");
define("_FPSS_UPLOAD_FAIL_THUMB","Thumbnail image upload failed!");
define("_FPSS_UPLOAD_FAIL","Slide image upload failed!");		

// Copyrights
define('_FPSS_ABOUT','Find out more about "Frontpage SlideShow" at the official <a href="http://www.joomlaworks.gr" target="_blank" title="Visit JoomlaWorks!">JoomlaWorks</a> website or visit the exclusive product website at <a href="http://www.frontpageslideshow.net" target="_blank">www.frontpageslideshow.net</a>.<br /><br />Copyright &copy 2006 - '.date('Y').' <a href="http://www.joomlaworks.gr" target="_blank">JoomlaWorks</a>. All rights reserved. This code cannot be redistributed without permission from <a href="http://www.joomlaworks.gr" target="_blank">JoomlaWorks</a>.<br /><br />Designed and developed by the JoomlaWorks team.<br /><br /><i>(Last update: September 1st, 2008 - v2.0.0)</i>');
define('_FPSS_COPYRIGHTS','Frontpage Slideshow v2.0.0 | Copyright &copy; 2006-'.date('Y').' <a href="http://www.joomlaworks.gr/" target="_blank">JoomlaWorks</a>');																																																																																																																																																																																																																																																														

?>