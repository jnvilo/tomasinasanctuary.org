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

// SUPPORT NOTE: Comment out the following line if your site is not loading at all when SIG Pro is used (script execution timeout case)
//ini_set("memory_limit","64M");

// Trigger the plugin
$mainframe->registerEvent( 'onPrepareContent', 'jwSIGPRO' );

function jwSIGPRO( &$row, &$params, $page=0 ) {
    global $mainframe;
		$document = & JFactory::getDocument();
    
    // JoomlaWorks reference parameters
    $plg_name               = "jw_sigpro";
    $plg_tag               	= "gallery";
    $plg_copyrights_start   = "\n\n<!-- JoomlaWorks \"Simple Image Gallery PRO\" Plugin (v2.0.4) starts here -->\n";
    $plg_copyrights_end     = "\n<!-- JoomlaWorks \"Simple Image Gallery PRO\" Plugin (v2.0.4) ends here -->\n\n";
	
    // Paths without the slash in the end
    $mosConfig_absolute_path = JPATH_SITE;
    $mosConfig_live_site     = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
    if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);
	
    // simple performance check to determine whether plugin should process further
    if ( JString::strpos( $row->text, $plg_tag) === false ) {
        return true;
    }
	
		// expression to search for
		$regex = "#{".$plg_tag."}(.*?){/".$plg_tag."}#s";    

    // find all instances of the plugin and put them in $matches
    preg_match_all( $regex, $row->text, $matches );

    // Number of plugins
    $count = count( $matches[0] );
    
    // plugin only processes if there are any instances of the plugin in the text
    if ( $count ) {
		
		// load the plugin language file the proper way
		if($mainframe->isAdmin()){
			JPlugin::loadLanguage( 'plg_content_'.$plg_name );
		} else {
			JPlugin::loadLanguage( 'plg_content_'.$plg_name, 'administrator' );
		}
		
		// Get plugin info
		$plugin =& JPluginHelper::getPlugin('content', $plg_name);
		
		// Outside Parameters
		if(!$params) $params = new JParameter(null);
		
		// Plugin Parameters
		$pluginParams = new JParameter( $plugin->params );

		$galleries_rootfolder 	= ($params->get('galleries_rootfolder')) ? $params->get('galleries_rootfolder') : $pluginParams->get('galleries_rootfolder','images/stories');
		$popup_engine 					= $pluginParams->get('popup_engine', 'jquery_slimbox');
		$thb_template 					= $pluginParams->get('thb_template', 'Default');
		$thb_width 							= (!is_null($params->get('thb_width',null))) ? $params->get('thb_width') : $pluginParams->get('thb_width', 200);
		$thb_height 						= (!is_null($params->get('thb_height',null))) ? $params->get('thb_height') : $pluginParams->get('thb_height', 160);
		$keepratio 							= $pluginParams->get('keepratio', 0);
		$jpg_quality 						= $pluginParams->get('jpg_quality', 80);
		$singlethumbmode 				= (!is_null($params->get('singlethumbmode',null))) ? $params->get('singlethumbmode') : $pluginParams->get('singlethumbmode', 0);
		$sortorder	 						= $pluginParams->get('sortorder','0');
		$showcaptions						= (!is_null($params->get('showcaptions',null))) ? $params->get('showcaptions') : $pluginParams->get('showcaptions', 1);
		$wordlimit 							= $pluginParams->get('wordlimit',0);
		$enabledownload					= (!is_null($params->get('enabledownload',null))) ? $params->get('enabledownload') : $pluginParams->get('enabledownload', 1);
		$loadmoduleposition			= $pluginParams->get('loadmoduleposition','');
		$cache_expire_time	 		= $pluginParams->get('cache_expire_time',120) * 60; // Cache expiration time in minutes
		$site_httppath					= $mosConfig_live_site.'/';
		$site_absolutepath			= $mosConfig_absolute_path.'/';
		$thbimgfolder						= 'cache/';
		$debugMode							= $pluginParams->get('debugMode',0);
		if($debugMode==0){
			// Turn off all error reporting
			error_reporting(0);
		}
		
		$downloadfile						= $enabledownload ? "{$mosConfig_live_site}/plugins/content/{$plg_name}/sigpro.download.php" : NULL;
		$modulehtml							= $loadmoduleposition ? '<div class="sig-mod-position">'.plgSPContentLoadPosition($loadmoduleposition,-1).'</div>' : NULL;
		$dynamic_caption				= '<span class="sig-popup-caption">'.$row->title.'</span>';
		$popupPath 							= "{$mosConfig_live_site}/plugins/content/{$plg_name}/popup_engines/{$popup_engine}";
		$extraClass							= "";
		
		// include files
		include_once($mosConfig_absolute_path."/plugins/content/{$plg_name}/sigpro.engine.php");
		include($mosConfig_absolute_path."/plugins/content/{$plg_name}/popup_engines/{$popup_engine}/popup.php");
		
		// define the template folder path (for overrides)
		if(file_exists("{$mosConfig_absolute_path}/templates/".$mainframe->getTemplate()."/html/{$plg_name}/{$thb_template}") && is_dir("{$mosConfig_absolute_path}/templates/".$mainframe->getTemplate()."/html/{$plg_name}/{$thb_template}")){
			$templatePath = "{$mosConfig_live_site}/templates/".$mainframe->getTemplate()."/html/{$plg_name}/{$thb_template}";
		} else {
			$templatePath = "{$mosConfig_live_site}/plugins/content/{$plg_name}/templates/{$thb_template}";
		}
		
		// add to the document head
		$headIncludes = "
{$plg_copyrights_start}
{$popupIncludes}
<style type=\"text/css\" media=\"all\">
	@import \"{$templatePath}/template.css\";
</style>
<!--[if IE 7]>
<style type=\"text/css\" media=\"all\">
	@import \"{$templatePath}/template_ie7.css\";
</style>
<![endif]-->
<!--[if lte IE 6]>
<style type=\"text/css\" media=\"all\">
	@import \"{$templatePath}/template_ie6.css\";
</style>
<![endif]-->
{$plg_copyrights_end}
		";
		
		global $loadSigProIncludes;
		if(!$loadSigProIncludes){
			$loadSigProIncludes=1;
			$document->addCustomTag($headIncludes);
			
			// Requirements
			if($debugMode){
				if(!extension_loaded('gd') && !function_exists('gd_info')) {
				    echo '<div class="message"><span>Simple Image Gallery PRO Notice:</span> It looks like PHP\'s "GD Image Library" is not installed/enabled on your system. Please ask your hosting company to enable/install it.</div>';
				}
				if(!is_writable("{$mosConfig_absolute_path}/cache")){
					echo '<div class="message"><span>Simple Image Gallery PRO Notice:</span> Joomla!\'s <b>"/cache"</b> folder is not writable. Please correct this folder\'s permissions.</div>';
				}
			}
		}
		
		// process tags
		if (preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
			// start the replace loop
			foreach ($matches[0] as $key => $match) {
			
				$tagcontent 		= preg_replace("/{.+?}/", "", $match);
				$tagparams 			= explode(':',$tagcontent);
				$gal_folder 		= $tagparams[0];
				$gal_width 			= (array_key_exists(1,$tagparams) && $tagparams[1]!='') ? $tagparams[1] : $thb_width;
				$gal_height 		= (array_key_exists(2,$tagparams) && $tagparams[2]!='') ? $tagparams[2] : $thb_height;
				$gal_thumbmode 	= (array_key_exists(3,$tagparams) && $tagparams[3]!='') ? $tagparams[3] : $singlethumbmode;
				$gal_captions 	= (array_key_exists(4,$tagparams) && $tagparams[4]!='') ? $tagparams[4] : $showcaptions;
				
				if($gal_thumbmode){
					$classSingleThumb = "-single";
				} else {
					$classSingleThumb = "";
				}

				// Gallery specific
				$srcimgfolder = $galleries_rootfolder.'/'.$gal_folder.'/';
				$galleryID = substr(md5($key.$srcimgfolder),1,10); // create a unique 8-digit identifier for each gallery
				
				$html_template = '
				<li class="sig-block"{THUMB CLASS}>
					<span class="sig-link-wrapper">
						<span class="sig-link-innerwrapper">
							<a href="{SOURCE IMAGE FILEPATH}" class="sig-link'.$extraClass.'" style="width:'.$gal_width.'px;height:'.$gal_height.'px;" rel="'.$relTag.'[gallery'.$galleryID.']" title="{CAPTION DESCRIPTION}{DOWNLOAD LINK}{LOAD MODULE POSITION}" target="_blank">
								{LEFT HTML COMMENT}<img class="sig-image" src="'.$mosConfig_live_site.'/plugins/content/'.$plg_name.'/sigpro.transparent.gif" alt="'.JText::_('JWSP_FE_IMGTITLE').'" title="'.JText::_('JWSP_FE_IMGTITLE').'" style="width:'.$gal_width.'px;height:'.$gal_height.'px;background-image:url({THUMB IMAGE FILEPATH});" />{RIGHT HTML COMMENT}
				';
				if($gal_captions!=0){
				$html_template .= '
								<span class="sig-pseudo-caption"><b>{CAPTION TITLE}</b></span>
								<span class="sig-caption" title="{CAPTION TITLE}">{CAPTION TITLE}</span>
				';
				}
				
				$html_template .= '
							</a>
						</span>
					</span>
				</li>
				';

				$showGallery = jwGallery(
					$site_httppath,
					$site_absolutepath,
					$srcimgfolder,
					$thbimgfolder,
					$gal_width,
					$gal_height,
					$keepratio,
					$jpg_quality,
					$gal_thumbmode,
					$sortorder,
					$gal_captions,
					$dynamic_caption,
					$wordlimit,
					$cache_expire_time,
					$html_template,
					$modulehtml,
					$downloadfile
				);
				
				$plg_html = $plg_copyrights_start.'<ul id="sig'.$galleryID.'" class="sig-container'.$classSingleThumb.'">'.$showGallery.'<li class="sig-clr">&nbsp;</li></ul>'.$plg_copyrights_end;
				
				// Do the replace
				$row->text = preg_replace( "#{".$plg_tag."}".$tagcontent."{/".$plg_tag."}#s", $plg_html , $row->text );
				
			} // end foreach
	
		} // end if

	} // END COUNT
    
} // END FUNCTION



/* ------------ Additional functions ------------ */

// Load Module Position
if(!function_exists('plgSPContentLoadPosition')){
	function plgSPContentLoadPosition( $position, $style=-2 ){
		$document	= &JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$params		= array('style'=>$style);
	
		$contents = '';
		foreach (JModuleHelper::getModules($position) as $mod)  {
			$contents .= $renderer->render($mod, $params);
		}
		return $contents;
	}
}
