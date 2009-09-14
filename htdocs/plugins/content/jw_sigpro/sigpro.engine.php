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

if(!function_exists('jwGallery')){
	function jwGallery(
		$site_httppath,
		$site_absolutepath,
		$srcimgfolder,
		$thbimgfolder,
		$thb_width,
		$thb_height,
		$keepratio,
		$jpg_quality,
		$singlethumbmode,
		$sortorder,
		$showcaptions,
		$dynamic_caption,
		$wordlimit,
		$cache_expire_time,
		$html_template,
		$modulehtml,
		$downloadfile
	){
	
		// Internal parameters
		$prefix							= "jwsigpro_cache_";
		$captionFile				= "labels.txt";
		$captionDummyTitle	= "Title";
		$captionDummyDesc		= "<b>Description</b><br />You can use HTML as you can see!";

		// Initiate $result
		$result = '';
	
		// Execute code if the folder can be opened, or fail silently
		if ($dir = @ opendir($site_absolutepath.$srcimgfolder)) {

			// initialize an array for matching files
			$found = array();
			
			// Create an array of file types
			$fileTypes = array('jpg','jpeg','gif','png');
			
			// Traverse the folder, and add filename to $found array if type matches
			while (false !== ($item = readdir($dir))) {
				$fileInfo = pathinfo($item);
				if (array_key_exists('extension', $fileInfo) && in_array(strtolower($fileInfo['extension']),$fileTypes)) {
					$found[] = $item;
				}
			}
			
			// Check if the $found array is not empty
			if ($found) {
			
				// Sort array
				switch ($sortorder) {
					case 0: sort($found); break;
					case 1: natsort($found); break; // Sort in natural, case-sensitive order
					case 2: natcasesort($found); break; // Sort in natural, case-insensitive order
					case 3: rsort($found); break;
					case 4: shuffle($found); break;
				}		

				// Caption handling
				if(file_exists($site_absolutepath.$srcimgfolder.$captionFile) && is_readable($site_absolutepath.$srcimgfolder.$captionFile)){
					// read the captions file into an array
					$captionsfile = file($site_absolutepath.$srcimgfolder.$captionFile);
					foreach($captionsfile as $caption){
						$temp = explode("|",$caption);
						if(isset($temp[0]) && file_exists($site_absolutepath.$srcimgfolder."/".$temp[0])) {
							$captions[$site_absolutepath.$srcimgfolder."/".strtolower($temp[0])]['title'] = @ $temp[1];
							// maintain backwards compatibility regarding captions
							if(isset($temp[2])){
								$captions[$site_absolutepath.$srcimgfolder."/".strtolower($temp[0])]['description'] = @ $temp[2];
							} else {
								$captions[$site_absolutepath.$srcimgfolder."/".strtolower($temp[0])]['description'] = @ $temp[1];
							}
						}						
					}
					
				} else {
					if($showcaptions==2){
						// Check if a captions file exists and if not write a new captions file and fill it with the image file list and content placeholders
						$captionsfile = fopen($site_absolutepath.$srcimgfolder.$captionFile,'w');
						foreach ($found as $filename) {
							fwrite($captionsfile,"$filename|$captionDummyTitle|$captionDummyDesc\n");
						}
						fclose($captionsfile);
						// Read the new file
						$newcaptionsfile = file($site_absolutepath.$srcimgfolder.$captionFile);
						foreach($newcaptionsfile as $caption){
						$temp = explode("|",$caption);
						if(isset($temp[0]) && file_exists($site_absolutepath.$srcimgfolder."/".$temp[0])) {
							$captions[$site_absolutepath.$srcimgfolder."/".strtolower($temp[0])]['title'] = @ $temp[1];
							$captions[$site_absolutepath.$srcimgfolder."/".strtolower($temp[0])]['description'] = @ $temp[2];
						}						
					}						
					}		
				}	
					
				// Loop through the image file list
				for ($i=0; $i<count($found); $i++){
				
					// Assign source image and path to a variable
					$filename = $found[$i];
					$original = $site_absolutepath.$srcimgfolder.$filename;
					
					// Single thumb mode conditions
					if ($singlethumbmode) {
						if($i==0) {
							$li_style = "";
							$leftcomment = "";
							$rightcomment = "";					
						} else {
							$li_style = " style=\"display:none;\"";
							$leftcomment = "<!--";
							$rightcomment = "-->";	
						}
					} else {
						$li_style = "";
						$leftcomment = ""; // <!--
						$rightcomment = ""; // -->
					}
					
					// Caption display
					if($showcaptions==2){					
						$captiontitle = $captions[$site_absolutepath.$srcimgfolder."/".strtolower($filename)]['title'];
						$captiondescription = $captions[$site_absolutepath.$srcimgfolder."/".strtolower($filename)]['description']."<br /><br />";
						$dynamic_caption = '';
					} elseif($showcaptions==1) {
						$captiontitle = JText::_('JWSP_FE_CPNTITLE');
						$captiondescription = JText::_('JWSP_FE_CPNDESCRIPTION')."<br />".$dynamic_caption."<br /><br />";
					} else {
						$captiontitle = '';
						$captiondescription = '';
					}
					
					if($wordlimit){
						$captiontitle = wordLimit($captiontitle,$wordlimit);
					}
					
					if($downloadfile){
						$download_html = replaceHtml('<a class="sig-download-link" href="'.$downloadfile.'?file='.$srcimgfolder.replaceWhiteSpace($filename).'">'.JText::_('JWSP_FE_DOWNLOAD').'</a><br /><br />');
					} else {
						$download_html = '';
					}

					// Template assignments
					$template_variables = array(
						"/{FILENAME}/",
						"/{SOURCE IMAGE FILEPATH}/",
						"/{THUMB IMAGE FILEPATH}/",
						"/{CAPTION TITLE}/",
						"/{CAPTION DESCRIPTION}/",
						"/{THUMB CLASS}/",
						"/{LEFT HTML COMMENT}/",
						"/{RIGHT HTML COMMENT}/",
						"/{DOWNLOAD LINK}/",
						"/{LOAD MODULE POSITION}/",
					);
					$real_variables = array(
						$filename,
						$site_httppath.$srcimgfolder.replaceWhiteSpace($filename),
						$site_httppath.$thbimgfolder.$prefix.substr(md5($srcimgfolder),1,10).strtolower(replaceWhiteSpace($filename)),
						replaceHtml($captiontitle),
						replaceHtml($captiondescription),
						$li_style,
						$leftcomment,
						$rightcomment,
						$download_html,
						replaceHtml($modulehtml),
					);
					
					// Check if thumb image exists already
					$thumbimage = $site_absolutepath.$thbimgfolder.$prefix.substr(md5($srcimgfolder),1,10).strtolower($filename);
					if(file_exists($thumbimage) && is_readable($thumbimage) && (filemtime($thumbimage)+$cache_expire_time) > time()){
						
						// OUTPUT
						$result .= preg_replace($template_variables, $real_variables, $html_template);

					} else {
						// Otherwise create the thumb image
						
						// begin by getting the details of the original
						list($width, $height, $type) = getimagesize($original);

						// strip the extension off the image filename (case insensitive)
						$imagetypes = array('/\.gif$/i', '/\.jpg$/i', '/\.jpeg$/i', '/\.png$/i');
						$name = preg_replace($imagetypes, '', basename($original));
						
						// create an image resource for the original
						switch($type) {
							case 1:
								$source = @ imagecreatefromgif($original);
								if (!$source) {
									$result = JText::_('JWSP_MSG_ERRGIF');
								}
								break;
							case 2:
								$source = imagecreatefromjpeg($original);
								break;
							case 3:
								$source = imagecreatefrompng($original);
								break;
							default:
								$source = NULL;
								$result = JText::_('JWSP_MSG_NOFILETYPE');
						}
						
						// make sure the image resource is OK
						if (!$source) {
							$result = JText::_('JWSP_MSG_COPYPROBLEM');
						} else {
						
							// calculate the dimensions of the thumbnail
							if($keepratio){
							
								if ($width > $height) {
									$thumb_width = $thb_width;
									$thumb_height = $thb_width*$height/$width;
								} elseif ($width < $height) {
									$thumb_width = $thb_height*$width/$height;
									$thumb_height = $thb_height;
								} else {
									$thumb_width = $thb_width;
									$thumb_height = $thb_height;
								}
							
							} else {

								// thumb ratio bigger that container ratio
								if($width/$height > $thb_width/$thb_height){
									// wide containers
									if($thb_width>=$thb_height){
										// wide thumbs
										if ($width > $height) { $thumb_width = $thb_height*$width/$height; $thumb_height = $thb_height; }
										// high thumbs
										else { $thumb_width = $thb_height*$width/$height; $thumb_height = $thb_height; }
									// high containers
									} else {
										// wide thumbs
										if ($width > $height) { $thumb_width = $thb_height*$width/$height; $thumb_height = $thb_height; }
										// high thumbs
										else { $thumb_width = $thb_height*$width/$height; $thumb_height = $thb_height; }
									}
								} else {
									// wide containers
									if($thb_width>=$thb_height){
										// wide thumbs
										if ($width > $height) { $thumb_width = $thb_width; $thumb_height = $thb_width*$height/$width; }
										// high thumbs
										else { $thumb_width = $thb_width; $thumb_height = $thb_width*$height/$width; }
									// high containers
									} else {
										// wide thumbs
										if ($width > $height) { $thumb_width = $thb_height*$width/$height; $thumb_height = $thb_height; }
										// high thumbs
										else { $thumb_width = $thb_width; $thumb_height = $thb_width*$height/$width; }
									}
								}

							}

							$thumb_width = round($thumb_width);
							$thumb_height = round($thumb_height);

							// create an image resource for the thumbnail
							$thumb = imagecreatetruecolor($thumb_width, $thumb_height);
							
							// create the resized copy
							imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
							
							// save the resized copy
							$thumbname = $thbimgfolder.$prefix.substr(md5($srcimgfolder),1,10).strtolower($name);
							switch($type) {
								case 1:
									if (function_exists('imagegif')) {
										$success = imagegif($thumb, $site_absolutepath.$thumbname.'.gif');
									} else {
										$success = imagejpeg($thumb, $site_absolutepath.$thumbname.'.jpg', $jpg_quality);
									}
									break;
								case 2:
									$success = imagejpeg($thumb, $site_absolutepath.$thumbname.'.jpg', $jpg_quality);
									break;
								case 3:
									$success = imagepng($thumb, $site_absolutepath.$thumbname.'.png');
							}
							if ($success) {
							
								// OUTPUT
								$result .= preg_replace($template_variables, $real_variables, $html_template);
								
							}
							
							// remove the image resources from memory
							imagedestroy($source);
							imagedestroy($thumb);
						}
					}
				}
			}

			closedir($dir);
			
			
		} else {
			$result = '<li class="message">'.JText::_('JWSP_MSG_ERRFOLDER').'</li>';
		}
		
		// Return output
		return $result;
	
	} // END FUNCTION
}



/* ------------------ Helper Functions ------------------ */

// Word limit
if(!function_exists('wordLimit')){
	function wordLimit($str, $limit = 100, $end_char = '...') {
		if (trim($str) == '') return $str;
		preg_match('/\s*(?:\S*\s*){'. (int) $limit .'}/', $str, $matches);
		if (strlen($matches[0]) == strlen($str)) $end_char = '';
		return rtrim($matches[0]).$end_char;
	}
}

// Entity replacements
if(!function_exists('replaceHtml')){
	function replaceHtml($text_to_parse){
		$source_html = array("&","\"","'","<",">","\r","\t","\n");
		$replacement_html = array("&amp;","&quot;","&#039;","&lt;","&gt;","","","");
		return str_replace($source_html,$replacement_html,$text_to_parse);
	}
}

if(!function_exists('replaceWhiteSpace')){
	function replaceWhiteSpace($text_to_parse){
		$source_html = array(" ");
		$replacement_html = array("%20");
		return str_replace($source_html,$replacement_html,$text_to_parse);
	}
}
