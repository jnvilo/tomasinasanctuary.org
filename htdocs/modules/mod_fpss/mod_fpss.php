<?php
/*
// "Frontpage Slideshow" Module for Joomla! 1.5.x - Version 2.0.0
// Copyright (c) 2006 - 2008 JoomlaWorks. All rights reserved.
// This code cannot be redistributed without permission from JoomlaWorks - http://www.joomlaworks.gr.
// More info at http://www.joomlaworks.gr and http://www.frontpageslideshow.net
// Designed and developed by the JoomlaWorks team
// ***Last update: September 1st, 2008***
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

global $mainframe;

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

$db =& JFactory::getDBO();
$user =& JFactory::getUser();  

// Module Parameters
$moduleclass_sfx 		= $params->get('moduleclass_sfx','');
$catid 					= $params->get('catid','1');
$engine 				= $params->get('engine','jquery');
$disablelib 			= $params->get('disablelib',0);
$optimizejs 			= $params->get('optimizejs',0);
$fpss_template 			= $params->get('fpss_template','Movies');
$hide_nav 				= $params->get('hide_nav',0);
$sidebar_width 			= $params->get('sidebar_width',200);
$striptags 				= $params->get('striptags',1);
$allowed_tags 			= $params->get('allowed_tags',"<a><b><span>"); // these tags will NOT be stripped off!
$loadingTime 			= $params->get('loadingTime',800);
$rotateAction 			= $params->get('rotateAction','click');
$autoSlide 				= $params->get('autoSlide',1);
$autoSlide 				= ($autoSlide) ? 'true' : 'false';
$mtCTRtransitionText 	= $params->get('mtCTRtransitionText',1000);
$mtCTRtext_effect 		= $params->get('mtCTRtext_effect',0);
$mtCTRtext_effect 		= ($mtCTRtext_effect) ? 'true' : 'false';
$random 				= $params->get('random',0);
$limitslides 			= $params->get('limitslides');
$chars 					= $params->get('chars');
$words 					= $params->get('words');
$delay 					= $params->get('delay',6000);
$speed 					= $params->get('speed',1000);
$width 					= $params->get('width',500);
$height 				= $params->get('height',308);
$seperator 				= $params->get('seperator','>>');
$showtitle      		= $params->get('showtitle','2');
$showseccat     		= $params->get('showseccat','2');
$showcustomtext 		= $params->get('showcustomtext','2');
$showplaintext  		= $params->get('showplaintext','2');
$showreadmore   		= $params->get('showreadmore','2');
$nolink         		= $params->get('nolink','2');

// Other
$fpss = "com_fpss";

// J1.5 assignments
$mosConfig_absolute_path = JPATH_SITE;
$mosConfig_live_site = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);
$lang =& JFactory::getLanguage();
$langTag = $lang->getTag();

// Group Navigation
if ($fpss_template=='JJ-Obs' || $fpss_template=='JJ-Rasper') { $groupnav = 1; } else { $groupnav = 0; }

// Compatibility check
$nullDate	= $db->getNullDate();
jimport('joomla.utilities.date');
$date = new JDate();
$now = $date->toMySQL();
$contentConfig 	= &JComponentHelper::getParams( 'com_content' );
$access			= !$contentConfig->get('shownoauth');
$aid			= $user->get('aid', 0);
$qaccess = $access ? "\n AND a.access <= ".(int) $aid." AND cc.access <= ".(int) $aid." AND s.access <= ".(int) $aid." " : '';

// CRD
$crd = base64_decode("PGRpdiBzdHlsZT0iZGlzcGxheTpub25lOyI+RnJvbnRwYWdlIFNsaWRlc2hvdyAodmVyc2lvbiAyLjAuMCkgLSBDb3B5cmlnaHQgJmNvcHk7IDIwMDYtMjAwOCBieSBKb29tbGFXb3JrczwvZGl2Pg==");

// CHECK IF COMPONENT EXIST
$db->setQuery("SELECT `id` FROM `#__components`"
.	"\nWHERE `option`='$fpss'"
.	"\nLIMIT 1");
$exist = $db->loadResult();

if($exist) {

	// INCLUDE LANGUAGE FILE
	if (file_exists($mosConfig_absolute_path.'/administrator/components/'.$fpss.'/language/'.$langTag.'.php')) {
		include_once ($mosConfig_absolute_path.'/administrator/components/'.$fpss.'/language/'.$langTag.'.php');
	} else {
		include_once ($mosConfig_absolute_path.'/administrator/components/'.$fpss.'/language/en-GB.php');
	}
	
	if (file_exists($mosConfig_absolute_path.'/components/'.$fpss.'/fpss.class.php')) {
		include_once ($mosConfig_absolute_path.'/components/'.$fpss.'/fpss.class.php');
	}
	if(!isset($MOD_FPSS_CONFIG)) {
		$MOD_FPSS_CONFIG =& $fpss_config;
	}

	// FIND WHAT CONTENT ITEMS WE CAN SEE
	$query = "SELECT a.id, a.introtext, a.sectionid, a.title, s.title as stitle, cc.title as ctitle, "	
	. "\n CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\":\", a.id, a.alias) ELSE a.id END as slug,"
	. "\n CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(\":\", cc.id, cc.alias) ELSE cc.id END as catslug"
	. "\n FROM #__content AS a"
	. "\n INNER JOIN #__categories AS cc ON cc.id = a.catid"
	. "\n INNER JOIN #__sections AS s ON s.id = a.sectionid"
	. "\n WHERE ( a.state = 1 AND a.sectionid > 0 )"
	. "\n AND ( a.publish_up = '$nullDate' OR a.publish_up <= '$now' )"
	. "\n AND ( a.publish_down = '$nullDate' OR a.publish_down >= '$now' )"
    . "\n AND a.id IN ( select itemlink from "._FPSS_TABLE_SLIDES." where itemlink!=0 )"
	. $qaccess
	. "\n AND s.published = 1"
	. "\n AND cc.published = 1"
	. "\n ORDER BY a.created DESC"
	;

	$db->setQuery( $query );
	$contents_tmp = $db->loadObjectList();
	$okItems_tmp = array();
	$idArray = array();
	$catidArray = array();
	$secidArray = array();
	$titleTextArray = array();
	$introTextArray = array();
	$sectionTitleArray = array();
	$categoryTitleArray = array();
	foreach ($contents_tmp as $content_tmp) {
		$okItems_tmp[] = $content_tmp->id;
		$idArray[$content_tmp->id] = $content_tmp->slug;
		$catidArray[$content_tmp->id] = $content_tmp->catslug;
		$secidArray[$content_tmp->id] = $content_tmp->sectionid;
		$titleTextArray[$content_tmp->id] = $content_tmp->title;
		$introTextArray[$content_tmp->id] = $content_tmp->introtext;
		$sectionTitleArray[$content_tmp->id] = $content_tmp->stitle;
		$categoryTitleArray[$content_tmp->id] = $content_tmp->ctitle;
	}
	$okItems = implode(",", $okItems_tmp);

	// ORDERING
	$orderBy = ($random==1)?"\n ORDER BY RAND()":"\n ORDER BY ordering ";

	// FIND WHAT SLIDES WE CAN SEE
	$query = "SELECT *"
	. "\n FROM "._FPSS_TABLE_SLIDES
	. "\n WHERE state = 1"
	. "\n AND registers <= ".intval($user->get('gid'))
	. "\n AND ( publish_up = '$nullDate' OR publish_up <= '$now' )"
	. "\n AND ( publish_down = '$nullDate' OR publish_down >= '$now' )"
	. "\n AND catid IN ( $catid )"
	. $orderBy
	;
	$db->setQuery( $query );
	$images = $db->loadObjectList();

	if ($db->getErrorMsg()) {
		JError::raiseError(500, $db->getErrorMsg() );
	}

	/* --------------------- SLIDE CONTENT --------------------- */

	// Init variables outside the loop
    $html = '';
	$navhtml = '';
	
    // Start loop
	$div_id = 1;
	if($limitslides) {$i = 0;}
	
	foreach ($images as $key => $image) {
		if($limitslides) { if($i>=$limitslides) continue; }

        // Fetch Display Options (set in slide edit page)
        $show_title             = ($showtitle==2)?intval($image->showtitle):$showtitle;
        $show_section_title     = ($showseccat==2)?intval($image->showseccat):$showseccat;
		$show_category_title	= ($showseccat==2)?intval($image->showseccat):$showseccat;
        $show_introtext         = ($showcustomtext==2)?intval($image->showcustomtext):$showcustomtext;
        $show_plaintext         = ($showplaintext==2)?intval($image->showplaintext):$showplaintext;
        $show_readmore          = ($showreadmore==2)?intval($image->showreadmore):$showreadmore;
        $nolink                 = ($nolink==2)?intval($image->nolink):$nolink;
        $target 				= (intval($image->target)>0)?' target="_blank"':'';		

		// Hide .fpss-introtext completely if the content is hidden as well
		if (!$show_title && !$show_section_title && !$show_category_title && !$show_introtext && !$show_plaintext && !$show_readmore) {
			$hidecontent = ' style="display:none;"';
		} else {
			$hidecontent = '';
		}

		if($image->itemlink!=0) {
			// Get link
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($idArray[$image->itemlink], $catidArray[$image->itemlink], $secidArray[$image->itemlink]));
			// GET TITLE OF CONTENT
			$slidetitle = $titleTextArray[$image->itemlink];
			// GET TITLE OF CATEGORY
			$titleCategory = $categoryTitleArray[$image->itemlink];
			// GET TITLE OF SECTION
			$titleSection = $sectionTitleArray[$image->itemlink];
			// GET INTROTEXT AND REMOVE ANY MAMBOT TAGS OR IMAGES
			if($image->ctext) {
				$introtext = preg_replace("/<img.+?>/", "", $image->ctext);
			} else {
				$introtext = preg_replace('/{([a-zA-Z0-9\-_]*)\s*(.*?)}/i','', $introTextArray[$image->itemlink]); // add outside the if?
				$introtext = preg_replace("/<img.+?>/", "", $introtext);
			}
		} elseif ($image->menulink!=0) {
			// Get link
			$db->setQuery("SELECT link, name FROM #__menu"
			. "\nWHERE id='$image->menulink'"
			. "\nLIMIT 1");
			$menu2array = $db->loadRow();
			$link = JRoute::_( $menu2array[0]."&Itemid=".$image->menulink );
			$menu2name = $menu2array[1];
			// GET TITLE OF CONTENT
			$slidetitle = $menu2name;
			// GET INTROTEXT AND REMOVE ANY IMAGES
			$introtext = preg_replace("/<img.+?>/", "", $image->ctext);
		} elseif ($image->customlink!="") {
			// Get link
			$link = JFilterOutput::ampReplace($image->customlink);
			// GET TITLE OF CONTENT
			$slidetitle = $image->name;
			// GET INTROTEXT AND REMOVE ANY IMAGES
			$introtext = preg_replace("/<img.+?>/", "", $image->ctext);
        } elseif ($image->nolink!="") {
			$link = "javascript:void(0);";
            // GET TITLE OF CONTENT
            $slidetitle = $image->name;
            // GET INTROTEXT AND REMOVE ANY IMAGES
            $introtext = preg_replace("/<img.+?>/", "", $image->ctext); 			
		}

		// HTML cleanup
		if ($striptags) {$introtext = strip_tags($introtext, $allowed_tags);}

		// if character limitation is defined
		if ($chars) {
			if(function_exists("mb_string")) {
				$introtext = mb_substr($introtext, 0, $chars).'...';
			} else {
				$introtext = substr($introtext, 0, $chars).'...';
			}
		}

		// if word limitation is defined (v2)
		if (!function_exists('word_limiter')) {
			function word_limiter($str, $limit = 100, $end_char = '&#8230;') {
				if (trim($str) == '')
				return $str;
				preg_match('/\s*(?:\S*\s*){'. (int) $limit .'}/', $str, $matches);
				if (strlen($matches[0]) == strlen($str))
				$end_char = '';
				return rtrim($matches[0]).$end_char;
			}
		}
		if ($words) {
			$introtext = word_limiter($introtext,$words);
		}

		$thecontent = ''; // init
		// Title
		if ($show_title) {
			$thecontent .= "<h1><a".$target." href=\"".$link."\">".$slidetitle."</a></h1>\n";
		}

		// Section/category if applicable
		if($image->itemlink!=0) {
			if ($show_section_title || $show_category_title) {
				$thecontent .= "<h2>";
				if ($show_section_title) {
					$thecontent .= $titleSection;
					if ($show_category_title) {$thecontent .= ' '.$seperator.' '.$titleCategory;}
				} else {
					if ($show_category_title) {$thecontent .= $titleCategory;}
				}
				$thecontent .= "</h2>\n";
			}
		}

		// Tagline text
		if ($show_plaintext) {
			$thecontent .= "<h3>".strip_tags($image->plaintext)."</h3>\n";
		}
		// Slide text
		if ($show_introtext) {
			$thecontent .= "<p>".$introtext."</p>\n";
		}

		// Slide 'read more' link
		if ($show_readmore && !$image->nolink) {
			$thecontent .= "<a".$target." href=\"".$link."\" class=\"readon\">".JText::_('MORE')."</a>\n";
		}

		/* --------------------- SLIDE OUTPUT --------------------- */
		$html .= '
	<div class="slide">
		<div class="slide-inner">
			<a'.$target.' href="'.$link.'" class="fpss_img">
				<span>
					<span style="background:url('.$mosConfig_live_site.'/'.$image->path.') no-repeat;">
						<span>
							<img src="'.$mosConfig_live_site.'/'.$image->path.'" alt="'._FPSS_MOD_IMGALT.'" />
						</span>
					</span>
				</span>
			</a>
			<div class="fpss-introtext"'.$hidecontent.'>
				<div class="slidetext">'.$thecontent.'</div>
			</div>
		</div>
	</div>
		';
			
		/* --------------------- NAVIGATION OUTPUT --------------------- */	
		$tagline = strip_tags($image->plaintext);
		$key = $key + 1;
		if ($key < 10) { $key = "0".$key; }
		if($image->thumb){$navImg = $mosConfig_live_site.'/'.$image->thumb;} else {$navImg = $mosConfig_live_site.'/'.$image->path;}
		$navhtml .= '
			<li>
				<a class="navbutton off navi" href="javascript:void(0);" title="'._FPSS_MOD_CLICKNAV.'"';
				if ($rotateAction=='mouseover') {$navhtml .= ' onclick="parent.location=\''.$link.'\';return false;"';}
				$navhtml .= '>
					<span class="navbar-img"><img src="'.$navImg.'" alt="'._FPSS_MOD_CLICKNAV.'" /></span>
					<span class="navbar-key">'.$key.'</span>
					<span class="navbar-title">'.$slidetitle.'</span>
					<span class="navbar-tagline">'.$tagline.'</span>
					<span class="navbar-clr"></span>
				</a>
			</li>
		';				

		if($limitslides) {$i++;}
		$div_id++;

	}

	// ---------------------- Add CSS/JS code to the head ----------------------
	$modFPSShead = '
<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 starts here -->
<style type="text/css" media="all">
<!--
	@import "'.$mosConfig_live_site.'/modules/mod_fpss/mod_fpss/templates/'.$fpss_template.'/template_css.php?w='.$width.'&h='.$height.'&sw='.$sidebar_width.'";
//-->	
</style>
	';
	
	if (!$hide_nav) {
	$modFPSShead .= '
<!--[if lte IE 7]>
<style type="text/css" media="all">
	@import "'.$mosConfig_live_site.'/modules/mod_fpss/mod_fpss/templates/'.$fpss_template.'/template_css_ie.css";
</style>	
<![endif]-->
	';
	}
	
	if ($hide_nav) {
		$sidebar_width = 0;
		$modFPSShead .= '<style type="text/css" media="all">#navi-outer {display:none;}</style>';
	}
	
	if($optimizejs) {
		$modFPSShead .= '
<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_fpss/mod_fpss/engines/'.$engine.'-fpss.php"></script>
	';
	} else {
		if(!$disablelib) {
			$modFPSShead .= '
<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_fpss/mod_fpss/engines/'.$engine.'-comp.js"></script>
	';
		}
		$modFPSShead .= '
<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_fpss/mod_fpss/engines/'.$engine.'-fpss-comp.js"></script>
	';
	}
	
	$modFPSShead .= '
<script type="text/javascript">
<!--
	var fpssPlayText = "'._FPSS_MOD_PLAY.'";
	var fpssPauseText = "'._FPSS_MOD_PAUSE.'";
	var crossFadeDelay = '.$delay.';
	var crossFadeSpeed = '.$speed.';
	var fpssLoaderDelay = '.$loadingTime.';
	var navTrigger = "'.$rotateAction.'";
	var autoslide = '.$autoSlide.';
	';
	if ($engine=='mootools') {
	$modFPSShead .= '
	var CTRtransitionText = '.$mtCTRtransitionText.';	
	var CTRtext_effect = '.$mtCTRtext_effect.';
	';
	}
	$modFPSShead .= '
//-->
</script>	
<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 ends here -->
	';
	
	$mainframe->addCustomHeadTag($modFPSShead);
	
	?>
    
<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 starts here -->
<div id="fpss-outer-container"<?php if ($moduleclass_sfx) {echo ' class="'.$moduleclass_sfx.'"';} ?>>
    <div id="fpss-container">
        <div id="fpss-slider">
            <div id="slide-loading"></div>
            <div id="slide-wrapper">
                <div id="slide-outer">
					<?php echo $html; ?>
                </div>
            </div>
        </div>        
        <div id="navi-outer">
            <div id="pseudobox"></div>
            <div class="ul_container">
                <ul>        
            <?php if ($groupnav) { ?>
                <?php echo $navhtml; ?>
                
                    <li class="noimages"><a id="fpss-container_next" href="javascript:void(0);" onclick="showNext();clearSlide();" title="<?php echo _FPSS_MOD_NEXT; ?>"></a></li>
                    <li class="noimages"><a id="fpss-container_playButton" href="javascript:void(0);" onclick="ppButtonClicked();return false;" title="<?php echo _FPSS_MOD_PLAYPAUSE; ?>"><?php echo _FPSS_MOD_PAUSE; ?></a></li>
                    <li class="noimages"><a id="fpss-container_prev" href="javascript:void(0);" onclick="showPrev();clearSlide();" title="<?php echo _FPSS_MOD_PREV; ?>"></a></li>
                    <li class="clr"></li>
                
            <?php } else { ?>
            
                    <li class="noimages"><a id="fpss-container_prev" href="javascript:void(0);" onclick="showPrev();clearSlide();" title="<?php echo _FPSS_MOD_PREV; ?>">&laquo;</a></li>
                    <?php echo $navhtml; ?>
                    <li class="noimages"><a id="fpss-container_next" href="javascript:void(0);" onclick="showNext();clearSlide();" title="<?php echo _FPSS_MOD_NEXT; ?>">&raquo;</a></li>
                    <li class="noimages"><a id="fpss-container_playButton" href="javascript:void(0);" onclick="ppButtonClicked();return false;" title="<?php echo _FPSS_MOD_PLAYPAUSE; ?>"><?php echo _FPSS_MOD_PAUSE; ?></a></li>
                
            <?php } ?>
            
                </ul>
            </div>
        </div> 
        <div class="fpss-clr"></div>
    </div>
    <div class="fpss-clr"></div> 
</div>
<?php
} else {
    echo _FPSS_MOD_ALERT;
}

echo $crd;

?>

<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 ends here -->

