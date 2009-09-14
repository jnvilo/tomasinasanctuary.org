<?php
/**
 * Joomla Flash uploader 2.8.x Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class HTML_joomla_flash_uploader {
	function showFlash($row, $realfolder, $use_js_include, $plugin=false) {		
	  	$language = &JFactory::getLanguage();
        $Cfg_lang = $language->getTag();
		$sess_id = session_id();
		$base_dir = 'administrator/components/com_joomla_flash_uploader/tfu';
		$relative_dir = parse_url(JURI::base());
		$relative_dir = $relative_dir['path'];
                $relative_dir = rtrim($relative_dir,"\\/.") . '/'; // we replace to get a consistent output with different php versions!

		$width=$row->display_width;
		$height=floor($width*(340/650));
		
		$extra_settings = '';
		$extra_settings_js = '';
        if ($row->description_mode == "true") {
          $extra_settings .= '&amp;tfu_description_mode=true';
          $extra_settings_js .= 'flashvars.tfu_description_mode="true";';
        }
        if ($row->hide_remote_view == "true") {
          $extra_settings .= '&amp;hide_remote_view=true';
          $extra_settings_js .= 'flashvars.hide_remote_view="true";';
        }
        $swf_text_settings_js = '';
        if ($row->swf_text) { // we bring the parameters into the right js format.
          $elements = split("&",$row->swf_text);
          foreach ($elements as $element) {
            $extra_settings_js .= "flashvars." . str_replace("=", "=\"", $element) . "\";";
          }
        }
        
		$output = ''; // <form action="index2.php" method="post" name="adminForm"><input type="hidden" name="option" value="com_joomla_flash_uploader"/><input type="hidden" name="task" value="config"/></form>';
		
		if ($row->enable_setting=="false") { // no flash only text!
		  if ($plugin) {
		    return JFULanguage::getLanguage($row->text_top_lang,$row->text_top, "TEXT_TOP" , $row->id);
		  } else {
		    echo   JFULanguage::getLanguage($row->text_top_lang,$row->text_top, "TEXT_TOP" , $row->id);
            return;
          }
		}
		
		if (!file_exists($realfolder) && $realfolder != "") {
		  $output .= "Configuration Error! The destination folder does not exist.";
		  $output .= $realfolder; 
		}
		
		if (!$plugin) {
		$output .= "<h3>";
		$output .= JFULanguage::getLanguage($row->text_title_lang,$row->text_title, "TEXT_TITLE" ,$row->id);
		$output .= "</h3>";
		$output .= JFULanguage::getLanguage($row->text_top_lang,$row->text_top, "TEXT_TOP" , $row->id);
		$output .= "<p>";
		}
        $lang = JFULanguage::mapLangJoomlatoTFU($Cfg_lang);
		
		// object tag is used for noscript and if JS include is disabled in the config.
		$objecttag = '
          <object name="mymovie" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="'.$width.'" height="'.$height.'"  align="middle">
            <param name="allowScriptAccess" value="sameDomain" />
            <param name="movie" value="'.$relative_dir.'administrator/components/com_joomla_flash_uploader/tfu/tfu_291.swf?joomla=frontend&amp;session_id='.$sess_id.'&amp;lang='.$lang.'&amp;base='.$base_dir.'&amp;relative_dir='.$relative_dir.$extra_settings.'&amp;'.$row->swf_text.'" />
            <param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />';
             if ($width == '650') {
                $objecttag .= '<param name="scale" value="noScale" />';
             }
            $objecttag .= '<param name="allowFullScreen" value="true" />
            <embed src="'.$relative_dir.'administrator/components/com_joomla_flash_uploader/tfu/tfu_291.swf?joomla=frontend&amp;session_id='.$sess_id.'&amp;lang='.$lang.'&amp;base='.$base_dir.'&amp;relative_dir='.$relative_dir.$extra_settings.'&amp;'.$row->swf_text.'" name="mymovie" quality="high" bgcolor="#ffffff" width="'.$width.'" height="'.$height.'" align="middle" '. (($width == '650') ? 'scale="noScale" ' : '') . ' allowfullscreen="true" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
            </object> ';
		
		
		if ($use_js_include) { // include with Javascript
        $output .= '
			<!-- include with javascript - best solution because otherwise you get the "klick to activate border in IE" -->
			<script type="text/javascript" src="'.$relative_dir.'administrator/components/com_joomla_flash_uploader/tfu/swfobject.js"></script>
			<script type="text/javascript">
			   document.write(\'<div id="flashcontent"><div class="noflash">TWG Flash Uploader requires at least Flash 8.<br>Please update your browser.<\/div><\/div>\');
                var flashvars = {};
                var params = {};
                var attributes = {};	
                flashvars.joomla="frontend";
                flashvars.session_id="'. $sess_id .'";	
                flashvars.lang="'.$lang.'";
                flashvars.base="'.$base_dir.'";
                flashvars.relative_dir="'.$relative_dir.'";
                params.allowfullscreen = "true";		   
                ';
                $output .=  $extra_settings_js;
                $output .=  $swf_text_settings_js;
                if ($row->fix_overlay == "true") {
                  $output .= 'params.wmode ="transparent";
                  '; 
                }   
                if ($width == '650') {
                  $output .= 'params.scale = "noScale";
                  ';
                }
                $output .= '  swfobject.embedSWF("'.$relative_dir.'administrator/components/com_joomla_flash_uploader/tfu/tfu_291.swf", "flashcontent", "'.$width.'", "'.$height.'", "8.0.0", "", flashvars, params, attributes);
                ';		   
                $output .= '
			</script>
			<!-- end include with Javascript -->
			<!-- static html include -->
			<noscript>
			Please enable Javascript.
			</noscript>
        ';
        } else { // include with object tag
          $output .= $objecttag;     
        }
        
        if (!$plugin) {
		$output .= "</p>";
		$output .= "<br>" . JFULanguage::getLanguage($row->text_bottom_lang, $row->text_bottom, "TEXT_BOTTOM" ,$row->id);
		}
			
   if ($plugin) {
          return $output;
        } else {
          echo $output;
     }     
   }   
   
   function wrongId($id, $plugin=false) {
     $output = "<center><div class='errordiv'>";
     if ($id == '') {
        $output .= JText::_('ERR_ID_NO');
     } else if ($id == '1') {
        $output .= JText::_('ERR_ADMIN_ID');
     } else {
        $output .= JText::_('ERR_ID_WRONG');
     }
     $output .= "</div></center>"; 
     if ($plugin) {
       return $output;
     } else {
       echo $output;
     }
   }	    
   function noUser($id, $plugin=false) {
     $output = "<center><div class='errordiv'>";
     $output .= JText::_('ERR_NO_USER');
     $output .= "</div></center>"; 
     if ($plugin) {
       return $output;
     } else {
       echo $output;
     }
   
   }
}
?>
