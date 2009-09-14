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
error_reporting(0);
// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'tables');

$mosConfig_absolute_path 	= JPATH_SITE;
$mosConfig_live_site 		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);
$lang						=& JFactory::getLanguage();
$langTag 					= $lang->getTag();

// INCLUDE LANGUAGE FILES
if (file_exists($mosConfig_absolute_path.'/administrator/components/'.$option.'/language/'.$langTag.'.php')) {
	include_once ($mosConfig_absolute_path.'/administrator/components/'.$option.'/language/'.$langTag.'.php');
} else {
	include_once ($mosConfig_absolute_path.'/administrator/components/'.$option.'/language/en-GB.php');
}

require_once( JApplicationHelper::getPath('class') );
require_once( JApplicationHelper::getPath('admin_html') );

$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
JArrayHelper::toInteger($cid, array(0));

$catid = JRequest::getVar('catid', 0);

// Output
HTML_FPSlideShow::showHeader($option, $task);
HTML_FPSlideShow::showNavBar($option, $task);

switch ($task) {

	// CONFIG
	case "save_config":
		saveConfig( $option, $mosConfig_absolute_path );
		break;

	case "config":
		editConfig( $fpss_config, $option );
		break;

		// CATEGORIES
	case "cancel_category":
		cancelCategory( $option );
		break;

	case "categories":
		showCategories( $option );
		break;

	case "new_category":
		editCategory( 0, $option );
		break;

	case "edit_category":
		editCategory( $cid, $option );
		break;

	case "save_category":
		saveCategory( $option );
		break;

	case "delete_category":
		deleteCategories( $option );
		break;

	case "publish_category":
		publishCategories( 1, $option );
		break;

	case "unpublish_category":
		publishCategories( 0, $option );
		break;

		// SLIDES
	case "cancel":
		cancelSlide( $option );
		break;

	case "slides":
		showSlides( $option );
		break;

	case "new":
		editSlide( 0, $option, $fpss_config );
		break;

	case "edit":
		editSlide( $cid[0], $option, $fpss_config );
		break;

	case "save":
		saveSlide( $option );
		break;

	case "orderup":
		orderSlide( $cid[0], -1, $option );
		break;

	case "orderdown":
		orderSlide( $cid[0], 1, $option );
		break;

	case "saveorder":
		saveOrder( $cid, $option );
		break;

	case "publish":
		publishImages( $cid, 1, $option );
		break;

	case "unpublish":
		publishImages( $cid, 0, $option );
		break;

	case "deleteslides":
		deleteSlides( $option );
		break;

	case 'accesspublic':
		accessMenu( intval( $cid[0] ), 0, $option, $task );
		break;

	case 'accessregistered':
		accessMenu( intval( $cid[0] ), 1, $option, $task );
		break;

	case 'accessspecial':
		accessMenu( intval( $cid[0] ), 2, $option, $task );
		break;

		// CREDITS
	case "credits":
		viewCredits( $option );
		break;

		// DEFAULT
	default:
		showSlides( $option );
		break;
}

HTML_FPSlideShow::showFooter();

function viewCredits( $option ) {

	HTML_FPSlideShow::viewCredits( $option );

}


// CONFIG
function editConfig( &$fpss_config, $option ) {
	global $mainframe;

	// Make sure the user is authorized to view this page
	$user = & JFactory::getUser();
	if (!$user->authorize( 'com_config', 'manage' )) {
		$mainframe->redirect('index.php', JText::_('ALERTNOTAUTH'));
	}


	$lists = array();

	$lists['editor'] 		= JHTML::_('select.booleanlist', 'editor', '', intval($fpss_config->editor));
	$lists['show_width'] 	= JHTML::_('select.booleanlist', 'show_width', '', intval($fpss_config->show_width));
	$lists['show_quality'] 	= JHTML::_('select.booleanlist', 'show_quality', '', intval($fpss_config->show_quality));
	$lists['articlelist'] 	= JHTML::_('select.booleanlist', 'articlelist', '', intval($fpss_config->articlelist));
	$lists['septhumb'] 		= JHTML::_('select.booleanlist', 'septhumb', '', intval($fpss_config->septhumb));

	HTML_FPSlideShow::editConfig( $fpss_config, $lists, $option );

}

function saveConfig( $option, $mosConfig_absolute_path ) {
	global $mainframe;

	$user = & JFactory::getUser();
	if ($user->gid!=25) {
		$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
	}

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	// Set FTP credentials, if given
	jimport('joomla.client.helper');
	JClientHelper::setCredentialsFromRequest('ftp');
	$ftp = JClientHelper::getCredentials('ftp');

	$config = new JRegistry('config');
	$config_array = array();

	// CONFIG SETTINGS
	$config_array['editor']				= JRequest::getVar('editor', 1, 'post', 'int');
	$config_array['show_width']			= JRequest::getVar('show_width', 0, 'post', 'int');
	$config_array['show_quality']		= JRequest::getVar('show_quality', 0, 'post', 'int');
	$config_array['basepath']			= JRequest::getVar('basepath', 'images/stories', 'post', 'text');
	$config_array['articlelist']		= JRequest::getVar('articlelist', 1, 'post', 'int');
	$config_array['septhumb']			= JRequest::getVar('septhumb', 0, 'post', 'int');

	if (!is_dir($mosConfig_absolute_path.DS.$config_array['basepath'])) {
		$msg=_FPSS_CONFIG_ERROR_PATH;
		$mainframe->redirect('index.php?option='.$option.'&task=config', $msg);
		exit();
	}

	$config->loadArray($config_array);

	// Get the path of the configuration file
	$fname =  JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'configuration.php';

	JClientHelper::getCredentials('ftp', true);

	// Try to make configuration.php writeable
	jimport('joomla.filesystem.path');
	if (!JPath::setPermissions($fname, '0644')) {
		JError::raiseNotice('SOME_ERROR_CODE', 'Could not make configuration.php writable');
	}

	// Get the config registry in PHP class format and write it to configuation.php
	jimport('joomla.filesystem.file');
	if (JFile::write($fname, $config->toString('PHP', 'config', array('class' => 'FPSSConfig')))) {
		$msg = _FPSS_CONFIG_SAVED;
	} else {
		$msg = _FPSS_CONFIG_SAVED_ERROR;
	}

	// Try to make configuration.php unwriteable
	if (!JPath::setPermissions($fname, '0444')) {
		JError::raiseNotice('SOME_ERROR_CODE', 'Could not make configuration.php unwritable');
	}

	// Redirect appropriately
	$mainframe->redirect('index.php?option='.$option.'&task=config', $msg);
}


// SLIDES
function showSlides( $option ) {
	global $mainframe;

	$db = &JFactory::getDBO();

	// limit
	$limit 			= JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart 	= JRequest::getVar('limitstart', 0);
	$filter_catid 	= JRequest::getVar('filter_catid', '');

	// where
	$where = array();

	if ($filter_catid) {
		$where[] = 's.catid = ' . (int) $filter_catid;
	}

	$where		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';
	$orderby = ' ORDER BY s.catid, s.ordering' ;

	// query count
	$query = 'SELECT COUNT(s.id)'
	. ' FROM '._FPSS_TABLE_SLIDES.' AS s'
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	// pagination
	jimport('joomla.html.pagination');
	$pagination = new JPagination( $total, $limitstart, $limit );

	// main query
	$query = 'SELECT s.*, g.name as groupname '
	. ' FROM '._FPSS_TABLE_SLIDES.' AS s'
	. ' LEFT JOIN #__groups AS g ON g.id = s.registers'
	. $where
	. $orderby
	;
	//echo $query;

	$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
	$slides = $db->loadObjectList();

	// build list of categories
	$query = 'SELECT c.id, c.name' .
	' FROM '._FPSS_TABLE_CATEGORIES.' AS c' .
	' ORDER BY c.name';
	$categories[] = JHTML::_('select.option', '0', '- '._FPSS_SEL_CATEGORIES.' -', 'id', 'name');
	$db->setQuery($query);
	$categories = array_merge($categories, $db->loadObjectList());
	$lists['categories'] = JHTML::_('select.genericlist',  $categories, 'filter_catid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'name', $filter_catid);

	HTML_FPSlideShow::showSlides( $slides, $lists, $pagination, $option );
}

function publishImages( $cid=null, $state=0, $option ) {
	global $mainframe;

	$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart = JRequest::getVar('limitstart', 0);

	$db = &JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	JArrayHelper::toInteger($cid);

	if (count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		JError::raiseError(500, JText::_( 'Select a slide to '.$action, true ) );
	}

	$total = count($cid);
	$cids = implode( ',', $cid );

	$query = "UPDATE "._FPSS_TABLE_SLIDES
	. "\n SET state = $state "
	. "\n WHERE id IN ( $cids )"
	;
	$db->setQuery( $query );
	if (!$db->query()) {
		JError::raiseError(500, $db->getErrorMsg() );
	}

	switch ( $state ) {
		case 1:
			$msg = $total .' '._FPSS_IMAGES_PUBLISHED_DONE;
			break;

		case 0:
		default:
			$msg = $total .' '._FPSS_IMAGES_UNPUBLISHED_DONE;
			break;
	}

	$filter_catid 	= JRequest::getVar('filter_catid', '');

	$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$filter_catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
}

function accessMenu( $uid, $access, $option, $task ) {
	global $mainframe;

	$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart = JRequest::getVar('limitstart', 0);

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db  =& JFactory::getDBO();
	$cid = JRequest::getVar( 'cid', array(), '', 'array' );
	$n   = count( $cid );

	switch ($task) {

		case 'accessregistered' :
			$access = 1;
			break;

		case 'accessspecial' :
			$access = 2;
			break;

		case 'accesspublic' :
		default:
			$access = 0;
			break;

	}

	JArrayHelper::toInteger( $cid );
	$cids = implode( ',', $cid );

	$query = 'UPDATE '._FPSS_TABLE_SLIDES
	. ' SET registers = ' . (int) $access
	. ' WHERE id IN ( '. $cids.'  )'
	; echo $query;
	$db->setQuery( $query );
	if (!$db->query()) {
		return JError::raiseWarning( 500, $row->getError() );
	}

	$msg =_FPSS_IMAGES_CHANGE_ACCESS_IMAGES;

	$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart = JRequest::getVar('limitstart', 0);
	$filter_catid 	= JRequest::getVar('filter_catid', '');

	$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$filter_catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
}

function orderSlide( $uid, $inc, $option ) {
	global $mainframe, $option;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	// Initialize variables
	$db		= & JFactory::getDBO();

	$row = & JTable::getInstance('slide', 'Table');
	$row->load( (int) $uid );
	$row->move( $inc, 'catid = ' . (int) $row->catid );

	$cache = & JFactory::getCache($option);
	$cache->clean();

	$filter_catid 	= JRequest::getVar('filter_catid', '');

	$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$filter_catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );

}

function saveOrder( &$cid, $option ) {
	global $mainframe, $option;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	// Initialize variables
	$db			= & JFactory::getDBO();

	$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
	$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
	$total		= count($cid);
	$conditions	= array ();

	JArrayHelper::toInteger($cid, array(0));
	JArrayHelper::toInteger($order, array(0));

	// Instantiate an slide table object
	$row = & JTable::getInstance('slide', 'Table');

	// Update the ordering for items in the cid array
	for ($i = 0; $i < $total; $i ++)
	{
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				JError::raiseError( 500, $db->getErrorMsg() );
				return false;
			}
			// remember to updateOrder this group
			$condition = 'catid = '.(int) $row->catid;
			$found = false;
			foreach ($conditions as $cond)
			if ($cond[1] == $condition) {
				$found = true;
				break;
			}
			if (!$found)
			$conditions[] = array ($row->id, $condition);
		}
	}

	// execute updateOrder for each group
	foreach ($conditions as $cond)
	{
		$row->load($cond[0]);
		$row->reorder($cond[1]);
	}

	$cache = & JFactory::getCache($option);
	$cache->clean();

	$msg = _FPSS_IMAGES_FULLREORDERING_DONE;
	$filter_catid 	= JRequest::getVar('filter_catid', '');

	$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$filter_catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
}

function editSlide( $uid, $option, $config ) {
	global $mainframe;

	$cid = JRequest::getVar( 'cid', array(0), '', 'array' );

	JArrayHelper::toInteger($cid, array(0));
	$db =& JFactory::getDBO();

	// SLIDE
	$row =& JTable::getInstance('slide', 'Table');
	$id = intval($uid);
	$row->load($id);
	$slide =& $row;

	$editor =& JFactory::getEditor();

	$nullDate = $db->getNullDate();
	if($slide->publish_up==$nullDate) $slide->publish_up="";
	if($slide->publish_down==$nullDate) $slide->publish_down="Never";

	$lists['published'] = JHTML::_('select.booleanlist', 'state', '', $slide->state);
	$lists['publish_up'] = JHTML::_('calendar', $slide->publish_up, 'publish_up', 'publish_up', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25', 'maxlength'=>'19'));
	$lists['publish_down'] = JHTML::_('calendar', $slide->publish_down, 'publish_down', 'publish_down', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
	$lists['editor'] = $editor->display( 'ctext',  $slide->ctext , '100%', '350', '30', '10' ) ;

	if($slide->itemlink) {
		$content =& JTable::getInstance('content');
		$content->load( (int)$slide->itemlink );
		$sectionid = $content->sectionid;
		$categoryid = $content->catid;
	} else { $sectionid = ""; $categoryid = ""; }

	$javascript = "onchange='getCategoryList(this)'";
	$lists['jsections']	= mosFPSlideShow::getJSections( 'sectionid', $sectionid, $javascript );

	if($slide->itemlink) {
		$javascript = "onchange='getContentList(this)'";
		$lists["jcategories"] = mosFPSlideShow::getJCategories( 'categoryid', $categoryid, $sectionid, $javascript );
		$lists["jcontents"] = mosFPSlideShow::getJContents( 'contentid', $slide->itemlink, $categoryid, NULL );
	} else {
		$lists["jcategories"] = "<select id=\"categoryid\" name=\"categoryid\" class=\"inputbox\" size=\"1\" onchange=\"getContentList(this)\"><option value=\"0\">"._FPSS_SEL_CATEGORY."</option></select>";
		$lists["jcontents"] = "<select id=\"contentid\" name=\"contentid\" class=\"inputbox\" size=\"1\"><option value=\"0\">"._FPSS_SEL_CONTENT."</option></select>";
	}

	$lists["menu"] = mosFPSlideShow::builtDropDownMenu( $slide->menulink );

	$lists['target'] = JHTML::_('select.booleanlist', 'target', '', $slide->target);
    if($slide->id) $lists['groups'] = mosFPSlideShow::getAccessgroups($slide->registers);
    else $lists['groups'] = mosFPSlideShow::getAccessgroups(0);

	// CATEGORIES
	$query = 'SELECT c.id, c.name' .
	' FROM '._FPSS_TABLE_CATEGORIES.' AS c' .
	' ORDER BY c.name';
	$categories[] = JHTML::_('select.option', '0', '- '._FPSS_SEL_CATEGORIES.' -', 'id', 'name');
	$db->setQuery($query);
	$categories = array_merge($categories, $db->loadObjectList());
	if($slide->id) $curcat = $slide->catid; else { $filter_catid = JRequest::getVar('filter_catid', ''); $curcat = $filter_catid; }
	$lists['categories'] = JHTML::_('select.genericlist',  $categories, 'catid', 'class="inputbox" size="1" ', 'id', 'name', $curcat);

	// LOAD CATEGORY PARAMS
	$catparams =& JTable::getInstance('category', 'Table');
	if($row->catid) $catparams->load($row->catid);
	else $catparams->load(intval($_POST['filter_catid']));

	HTML_FPSlideShow::editSlide( $slide, $option, $lists, $config, $catparams );


}

function cancelSlide( $option ) {
	global $mainframe;

	$limit          = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart     = JRequest::getVar('limitstart', 0);
	$filter_catid 	= JRequest::getVar('catid', '');

	$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$filter_catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );

}

function saveSlide( $option ) {
	global $mainframe;

	$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart = JRequest::getVar('limitstart', 0);

	$db =& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	// load config file
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'configuration.php');
	$config = new FPSSConfig();

	// load upload file
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'class.upload.php');

	$mosConfig_live_site = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
	if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);
	$mosConfig_absolute_path = JPATH_ROOT;

	$nullDate 	= $db->getNullDate();

	$option = JRequest::getCmd( 'option');

	$row =& JTable::getInstance('slide', 'Table');

	//JRequest::getVar( 'listdir', _FPSSBASEPATH, 'get', 'text' );
	$imageaction 	= JRequest::getVar( 'imageaction', 1, 'post', 'int' );
	$oldpathtype 	= JRequest::getVar( 'oldpathtype', 0, 'post', 'int' );
	$serverimage 	= JRequest::getVar( 'serverimage', '', 'post', 'text' );
	$resize_x 		= JRequest::getVar( 'resize_x', '', 'post', 'int' );
	$quality 		= JRequest::getVar( 'quality', '', 'post', 'int' );

	if (!$row->bind(JRequest::get('post')))
	{
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if ((!$_FILES['image']['size'] && $serverimage=="" ) && !$row->id)
	{
		$msg = _FPSS_SLIDE_ALERT_MUST_IMAGE;
		$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$_POST['filter_catid'].'&limit='.$limit.'&limitstart='.$limitstart, $msg );
	}

	// LOAD CATEGORY PARAMS
	$catparams =& JTable::getInstance('category', 'Table');
	if($row->catid) $catparams->load($row->catid);
	else $catparams->load(intval($_POST['filter_catid']));

	$width 		= ($resize_x)?$resize_x:$catparams->width;
	$quality 	= ($quality)?$quality:$catparams->quality;

	// CHECK AND UPLOADS IMAGE - THUMB
	if($imageaction==2) { // if action is to upload image

		if($_FILES['image']['size']>0) { // if is selected an image

			if(!$image = mosFPSlideShow::uploadMedia($_FILES, 'image', $option, "", $width, $quality)) { // if the image is uploaded
				$msg = _FPSS_UPLOAD_FAIL;
				$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
				// $image the new path of the image
			}

			// delete the old image
			if($row->id) { // if we edit a slide

				$sl =& JTable::getInstance('slide', 'Table');
				$sl->load( (int)$row->id );

				if(file_exists($mosConfig_absolute_path."/".$sl->path) && $sl->path_type==1) { // if image exist and the image was from PC
					@unlink($mosConfig_absolute_path."/".$sl->path);
				}

			}

			if($config->septhumb==1) { // if config says to upload separate thumbnail

				if($_FILES['thumb']['size']>0) { // if is selected an thumbnail

					if(!$thumb = mosFPSlideShow::uploadMedia($_FILES, 'thumb', $option, "thumbs/", $catparams->width_thumb, $catparams->quality_thumb)) { // if the thumb is uploaded
						$msg = _FPSS_UPLOAD_FAIL_THUMB;
						$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
						// $thumb the new path of the image
					}

					// delete the old thumb
					if($row->id) { // if we edit a slide

						if(!is_object($sl)) {
							$sl =& JTable::getInstance('slide', 'Table');
							$sl->load( (int)$row->id );
						}

						if(file_exists($mosConfig_absolute_path."/".$sl->thumb) && $sl->path_type==1) { // if thumb exist and the thumb was from PC
							@unlink($mosConfig_absolute_path."/".$sl->thumb);
						}

					}

				} else {

					if(!is_object($sl)) {
						$sl =& JTable::getInstance('slide', 'Table');
						$sl->load( (int)$row->id );
					}

					if(!is_file($mosConfig_absolute_path."/".$sl->thumb)) { // if thumb doesn't exist break the operation

						$msg = _FPSS_UPLOAD_THUMB_NOTEXIST;
						$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );

					}

				}

			} else { // if config says to use the main image to create the thumb

				if(!$thumb = mosFPSlideShow::uploadMedia($_FILES, 'image', $option, "thumbs/", $catparams->width_thumb, $catparams->quality_thumb)) {
					$msg = _FPSS_UPLOAD_FAIL_THUMB;
					$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
					// $thumb the new path of the image
				}

				// delete the old thumb
				if($row->id) { // if we edit a slide

					if(!is_object($sl)) {
						$sl =& JTable::getInstance('slide', 'Table');
						$sl->load( (int)$row->id );
					}

					if(file_exists($mosConfig_absolute_path."/".$sl->thumb) && $sl->path_type==1) { // if thumb exist and the thumb was from PC
						@unlink($mosConfig_absolute_path."/".$sl->thumb);
					}

				}

			}

		}

	} elseif($imageaction==3) { // if the action is an image on the server

		// get the complete abs path
		$abspath = str_replace($mosConfig_live_site,$mosConfig_absolute_path,$serverimage);

		if(is_file($abspath)) { // if is selected an image

			if(!$image = mosFPSlideShow::uploadMediaServer($abspath, 'image', $option, "", $width, $quality)) { // if the image is uploaded
				$msg = _FPSS_UPLOAD_FAIL;
				$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
				// $image the new path of the image
			}

			// delete the old image
			if($row->id) { // if we edit a slide

				$sl =& JTable::getInstance('slide', 'Table');
				$sl->load( (int)$row->id );

				if(file_exists($mosConfig_absolute_path."/".$sl->path) && $sl->path_type==1) { // if image exist and the image was from PC
					@unlink($mosConfig_absolute_path."/".$sl->path);
				}

			}

			if($config->septhumb==1) { // if config says to upload separate thumbnail

				if($_FILES['thumb']['size']>0) { // if is selected an thumbnail

					if(!$thumb = mosFPSlideShow::uploadMedia($_FILES, 'thumb', $option, "thumbs/", $catparams->width_thumb, $catparams->quality_thumb)) { // if the thumb is uploaded
						$msg = _FPSS_UPLOAD_FAIL_THUMB;
						$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
						// $thumb the new path of the image
					}

					// delete the old thumb
					if($row->id) { // if we edit a slide

						if(!is_object($sl)) {
							$sl =& JTable::getInstance('slide', 'Table');
							$sl->load( (int)$row->id );
						}

						if(file_exists($mosConfig_absolute_path."/".$sl->thumb) && $sl->path_type==1) { // if thumb exist and the thumb was from PC
							@unlink($mosConfig_absolute_path."/".$sl->thumb);
						}

					}

				} else {

					if(!is_object($sl)) {
						$sl =& JTable::getInstance('slide', 'Table');
						$sl->load( (int)$row->id );
					}

					if(!is_file($mosConfig_absolute_path."/".$sl->thumb)) { // if thumb doesn't exist break the operation

						$msg = _FPSS_UPLOAD_THUMB_NOTEXIST;
						$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );

					}

				}

			} else { // if config says to use the main image to create the thumb

				if(!$thumb = mosFPSlideShow::uploadMediaServer($abspath, 'image', $option, "thumbs/", $catparams->width_thumb, $catparams->quality_thumb)) {
					$msg = _FPSS_UPLOAD_FAIL_THUMB;
					$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
					// $thumb the new path of the image
				}

				// delete the old thumb
				if($row->id) { // if we edit a slide

					if(!is_object($sl)) {
						$sl =& JTable::getInstance('slide', 'Table');
						$sl->load( (int)$row->id );
					}

					if(file_exists($mosConfig_absolute_path."/".$sl->thumb) && $sl->path_type==1) { // if thumb exist and the thumb was from PC
						@unlink($mosConfig_absolute_path."/".$sl->thumb);
					}

				}

			}

		}

	} elseif($imageaction==1) {

		if($config->septhumb==1) { // if config says to upload separate thumbnail

			if($_FILES['thumb']['size']>0) { // if is selected an thumbnail

				if(!$thumb = mosFPSlideShow::uploadMedia($_FILES, 'thumb', $option, "thumbs/", $catparams->width_thumb, $catparams->quality_thumb)) { // if the thumb is uploaded
					$msg = _FPSS_UPLOAD_FAIL_THUMB;
					$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );
					// $thumb the new path of the image
				}

				// delete the old thumb
				if($row->id) { // if we edit a slide

					if(!is_object($sl)) {
						$sl =& JTable::getInstance('slide', 'Table');
						$sl->load( (int)$row->id );
					}

					if(file_exists($mosConfig_absolute_path."/".$sl->thumb) && $sl->path_type==1) { // if thumb exist and the thumb was from PC
						@unlink($mosConfig_absolute_path."/".$sl->thumb);
					}

				}

			}

		}

	}

	if($row->id) { // if we edit an slide, load the existened reccord

		if(!is_object($sl)) {
			$sl =& JTable::getInstance('slide', 'Table');
			$sl->load( (int)$row->id );
		}

		$row->path 	= (is_file($mosConfig_absolute_path."/components/".$option."/images/".$image))?"components/".$option."/images/".$image:$sl->path;
		$row->thumb = (is_file($mosConfig_absolute_path."/components/".$option."/images/thumbs/".$thumb))?"components/".$option."/images/thumbs/".$thumb:$sl->thumb;
		$row->path_type = 1;

	} else { // if the slide is new

		$row->path 	= "components/".$option."/images/".$image;
		$row->thumb = "components/".$option."/images/thumbs/".$thumb;
		$row->path_type = 1;

	}

	// sanitise id field
	$row->id = (int) $row->id;
	$row->catid = (int) $row->catid;

	if ($row->publish_up == '') {
		$row->publish_up = $nullDate;
	}

	if (trim( $row->publish_down ) == 'Never' || trim( $row->publish_down ) == '') {
		$row->publish_down = $nullDate;
	}

	if($row->publish_up>$row->publish_down) {
		$row->publish_up = $nullDate;
		$row->publish_down = $nullDate;
	}

	$row->state = JRequest::getVar( 'published', 0, 'post', 'int' );
	$row->target = JRequest::getVar( 'target', 0, 'post', 'int' );
	$row->itemlink = JRequest::getVar( 'contentid', 0, 'post', 'int' );
	$row->registers = JRequest::getVar( 'registers', 0, 'post', 'int' );

	$row->name = $name = JRequest::getVar( 'name', '', 'post', 'text' );
	$row->ctext = JRequest::getVar( 'ctext', '', 'post', 'string', JREQUEST_ALLOWRAW );
	$row->plaintext = JRequest::getVar( 'plaintext', '', 'post', 'text' );

	$row->showtitle = JRequest::getVar( 'showtitle', 0, 'post', 'int' );
	$row->showseccat = JRequest::getVar( 'showseccat', 0, 'post', 'int' );
	$row->showcustomtext = JRequest::getVar( 'showcustomtext', 0, 'post', 'int' );
	$row->showplaintext = JRequest::getVar( 'showplaintext', 0, 'post', 'int' );
	$row->showreadmore = JRequest::getVar( 'showreadmore', 0, 'post', 'int' );

	$linkto = JRequest::getVar( 'linkto', 0, 'post', 'int' );   echo $linkto;
	switch ($linkto) {
		case 1:
			$row->itemlink=0;
			$row->menulink=0;
			$row->nolink=0;
			break;

		case 2:
			$row->customlink="";
			$row->menulink=0;
			$row->nolink=0;
			break;

		case 3:
			$row->customlink="";
			$row->itemlink=0;
			$row->nolink=0;
			break;

		case 4:
		default:
			$row->customlink="";
			$row->itemlink=0;
			$row->menulink=0;
			$row->nolink=1;
			break;
	}

	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();
	$row->reorder('catid = '.(int) $row->catid);

	$msg = _FPSS_SLIDE_SAVED;

	$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$row->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );

}

function deleteslides( $option )
{
	global $mainframe, $option;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db  =& JFactory::getDBO();
	$cid = JRequest::getVar( 'cid', array(), '', 'array' );
	$n   = count( $cid );

	JArrayHelper::toInteger( $cid );

	foreach ($cid as $id) {

		$slide =& JTable::getInstance('slide', 'Table');
		$slide->load($id);
		if($slide->delete( $id )) {
			@unlink(JPATH_ROOT.DS.$slide->path);
			@unlink(JPATH_ROOT.DS.$slide->thumb);
			$slide->reorder('catid = '.(int) $slide->catid);
		}

	}

	$msg = _FPSS_SLIDES_SUCC_DELETED;

	$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart = JRequest::getVar('limitstart', 0);

	$mainframe->redirect( 'index.php?option='.$option.'&task=slides&filter_catid='.$slide->catid.'&limit='.$limit.'&limitstart='.$limitstart, $msg );

}

// CATEGORIES
function showCategories( $option ) {
	global $mainframe;

	$db = &JFactory::getDBO();

	// limit
	$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart = JRequest::getVar('limitstart', 0);

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM "._FPSS_TABLE_CATEGORIES
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	// pagination
	jimport('joomla.html.pagination');
	$pagination = new JPagination( $total, $limitstart, $limit );

	$query = "SELECT  * "
	. "\n FROM "._FPSS_TABLE_CATEGORIES
	. "\n LIMIT $pagination->limitstart, $pagination->limit"
	;
	$db->setQuery( $query );
	$rows = $db->loadObjectList();

	HTML_FPSlideShow::showCategories( $rows, $pagination, $option );

}

function editCategory( $cid, $option ) {
	global $mainframe;

	$db =& JFactory::getDBO();

	// SLIDE
	$row =& JTable::getInstance('category', 'Table');
	$id = intval($cid[0]);
	$row->load($id);

	$lists['published'] 		= JHTML::_('select.booleanlist', 'published', '', ($row->id)?$row->published:1);


	HTML_FPSlideShow::editCategory( $row, $lists, $option );

}

function cancelCategory( $option ) {
	global $mainframe;

	$mainframe->redirect('index.php?option='.$option.'&task=categories', $msg);
}

function saveCategory( $option ) {
	global $mainframe;

	$db =& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$row =& JTable::getInstance('category', 'Table');

	if (!$row->bind(JRequest::get('post')))
	{
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// sanitise id field
	$row->id = (int) $row->id;
	$row->name = JRequest::getVar( 'name', '', 'post', 'text' );
	$row->published = JRequest::getVar( 'published', 0, 'post', 'int' );

	$row->width 			= (int) $row->width;
	$row->quality 			= (int) $row->quality;
	$row->width_thumb 		= (int) $row->width_thumb;
	$row->quality_thumb 	= (int) $row->quality_thumb;

    //init
    if(intval($row->width)==0)          $row->width = 500;
    if(intval($row->quality)==0)        $row->quality = 80;
    if(intval($row->width_thumb)==0)    $row->width_thumb = 100;
    if(intval($row->quality_thumb)==0)  $row->quality_thumb = 80;

	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$msg = _FPSS_CATEGORIES_SUCCESS_SAVE;
	$mainframe->redirect( 'index2.php?option='.$option.'&task=categories', $msg );

}

function publishCategories( $publish=0, $option ) {
	global $mainframe;

	$db =& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$cid = JRequest::getVar( 'cid', array(), '', 'array' );
	$n   = count( $cid );

	JArrayHelper::toInteger( $cid );
	$cids = implode( ',', $cid );

	$query = 'UPDATE '._FPSS_TABLE_CATEGORIES
	. ' SET published = ' . (int) $publish
	. ' WHERE id IN ( '. $cids.'  )'
	;
	$db->setQuery( $query );
	if (!$db->query()) {
		return JError::raiseWarning( 500, $row->getError() );
	}

	$msg =$publish ? _FPSS_CATEGORIES_PUBLISHED_DONE:_FPSS_CATEGORIES_UNPUBLISHED_DONE;

	$cache = & JFactory::getCache($option);
	$cache->clean();

	$mainframe->redirect('index.php?option='.$option.'&task=categories', $msg);


}

function deleteCategories( $option ) {
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db  =& JFactory::getDBO();
	$cid = JRequest::getVar( 'cid', array(), '', 'array' );
	$n   = count( $cid );

	JArrayHelper::toInteger( $cid );
	$cids = implode( ',', $cid );



	foreach ($cid as $value) {

		// SaD slides
		$query = "SELECT id "
		. "\n FROM "._FPSS_TABLE_SLIDES
		. "\n WHERE catid=".$value
		;

		$db->setQuery( $query );
		$sids = $db->loadObjectList();

		foreach ($sids as $sid) {

			$slide =& JTable::getInstance('slide', 'Table');
			$slide->load( $sid->id );
			if($slide->delete( $sid->id )) {
				@unlink(JPATH_ROOT.DS.$slide->path);
				@unlink(JPATH_ROOT.DS.$slide->thumb);
				$slide->reorder('catid = '.(int) $slide->catid);
			}

		}

		// SaD categories
		$category =& JTable::getInstance('category', 'Table');
		$category->load( $value );
		$category->delete( $value );

	}

	$msg = _FPSS_CATEGORIES_ITEMS_DELETED;

    $mainframe->redirect('index.php?option='.$option.'&task=categories', $msg);

}

?>