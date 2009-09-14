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

//////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                                  //
//              Classes and helper functions to the Frontpage SlideShow system                      //
//                                                                                                  //
//////////////////////////////////////////////////////////////////////////////////////////////////////

class mosFPSlideShow {

	/**
  * build the dropdown with sections, categories and content items
  */
	function builtDropDown( $menu ) {

		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();

		//
		$nullDate	= $database->getNullDate();
		jimport('joomla.utilities.date');
		$date = new JDate();
		$now = $date->toMySQL();

		$contentConfig 	= &JComponentHelper::getParams( 'com_content' );
		$access			= !$contentConfig->get('shownoauth');
		$aid			= $user->get('aid', 0);
		$qaccess = $access ? "\n AND a.access <= ".(int) $aid." AND cc.access <= ".(int) $aid." AND s.access <= ".(int) $aid." " : '';
		//

		$nbsp5 = "&nbsp;&nbsp;";
		$nbsp15 = $nbsp5.$nbsp5.$nbsp5;

		$list_temp[] = JHTML::_('select.option', '0', _FPSS_SLIDES_SELECT_MENU);

		$query_sec = "SELECT id, title"
		. "\n FROM #__sections"
		. "\n WHERE published=1"
		. "\n ORDER BY ordering"
		;
		$database->setQuery( $query_sec );
		$sections = $database->loadObjectList();

		foreach ($sections as $section) {

			$list_temp[] 	= JHTML::_('select.option', '-0', $section->title);

			$query_cat = "SELECT id, title"
			. "\n FROM #__categories"
			. "\n WHERE published=1"
			. "\n AND section=".$section->id
			. "\n ORDER BY ordering"
			;
			$database->setQuery( $query_cat );
			$categories = $database->loadObjectList();

			foreach ($categories as $category) {

				$list_temp[] 	= JHTML::_('select.option', '-0', $nbsp5."|--- ".$category->title);

				$query = "SELECT a.id, a.title "
				. "\n FROM #__content AS a"
				. "\n INNER JOIN #__categories AS cc ON cc.id = a.catid"
				. "\n INNER JOIN #__sections AS s ON s.id = a.sectionid"
				. "\n WHERE ( a.state = 1 AND a.sectionid > 0 )"
				. "\n AND ( a.publish_up = '$nullDate' OR a.publish_up <= '$now' )"
				. "\n AND ( a.publish_down = '$nullDate' OR a.publish_down >= '$now' )"
				. $qaccess
				. "\n AND s.published = 1"
				. "\n AND cc.published = 1"
				. "\n ORDER BY a.created DESC"
				;

				$database->setQuery( $query );
				$items = $database->loadObjectList();

				foreach ($items as $item) {

					$list_temp[] = JHTML::_('select.option', $item->id, $nbsp15."|--- ".htmlentities($item->title, ENT_QUOTES, 'utf-8' ));

				}

			}

		}

		return JHTML::_('select.genericlist',  $list_temp, 'contentid', 'class="inputbox" size="1" onclick="this.form.linkto[0].checked=true" onchange="this.form.linkto[0].checked=true"', 'value', 'text', $menu);

	}


	function builtDropDownMenu( &$lookup, $unassigned=1 )
	{
		$db =& JFactory::getDBO();

		// get a list of the menu items
		$query = "SELECT m.*"
		. "\n FROM #__menu AS m"
		. "\n WHERE m.published = 1"
		//. "\n AND m.type != 'separator'"
		//. "\n AND NOT ("
		//	. "\n ( m.type = 'url' )"
		//	. "\n AND ( m.link LIKE '%index.php%' )"
		//	. "\n AND ( m.link LIKE '%Itemid=%' )"
		//. "\n )"
		. "\n ORDER BY m.menutype, m.parent, m.ordering"
		;
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		$mitems_temp = $mitems;

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ( $mitems as $v ) {
			$id = $v->id;
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$list = mosFPSlideShow::mosTreeRecurse( intval( $mitems[0]->parent ), '', array(), $children, 20, 0, 0 );

		// Code that adds menu name to Display of Page(s)
		$text_count 	= 0;
		$mitems_spacer 	= $mitems_temp[0]->menutype;
        
        // joomla original code 
		foreach ($list as $list_a) {
			foreach ($mitems_temp as $mitems_a) {
				if ($mitems_a->id == $list_a->id) {
					// Code that inserts the blank line that seperates different menus
					if ($mitems_a->menutype != $mitems_spacer) {
						$list_temp[]    = JHTML::_('select.option', '-999', '-----');
						$mitems_spacer 	= $mitems_a->menutype;
					}

					// do not display `url` menu item types that contain `index.php` and `Itemid`
					if (!($mitems_a->type == 'url' && strpos($mitems_a->link, 'index.php') !== false && strpos($mitems_a->link, 'Itemid=') !== false)) {
						$text 			= $mitems_a->menutype .' | '. $list_a->treename;
						$list_temp[]    = JHTML::_('select.option', $list_a->id, $text);
						if ( strlen($text) > $text_count) {
							$text_count = strlen($text);
						}
					}
				}
			}
		} 
        
        // Abhinesh modify of above "foreach"
        /*
        foreach ($list as $list_a) {   
            
            // Code that inserts the blank line that seperates different menus
            if ($list_a->menutype != $mitems_spacer) {
                $list_temp[]       = JHTML::_('select.option', '-999', '-----');
                $mitems_spacer     = $list_a->menutype;
            }

            // do not display `url` menu item types that contain `index.php` and `Itemid`
            if (!($list_a->type == 'url' && strpos($list_a->link, 'index.php') !== false && strpos($list_a->link, 'Itemid=') !== false)) {
                $text            = $list_a->menutype .' | '. $list_a->treename;
                $list_temp[]     = JHTML::_('select.option', $list_a->id, $text); 

                if ( strlen($text) > $text_count) {
                    $text_count = strlen($text);
                }
            } 
                    
        }
        */
        
		$list = $list_temp;

		$mitems = array();
		$mitems[] = JHTML::_('select.option', '0', _FPSS_SLIDES_SELECT_MENU2);
		// append the rest of the menu items to the array
		foreach ($list as $item) {
			$mitems[] = JHTML::_('select.option', $item->value, $item->text);
		}

		$pages = JHTML::_('select.genericlist',  $mitems, 'menulink', 'class="inputbox" size="1" onclick="this.form.linkto[1].checked=true" onchange="this.form.linkto[1].checked=true"', 'value', 'text', $lookup);
		return $pages;
	}

	function mosTreeRecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1,$parent='parent') {
		if (@$children[$id] AND $level <= $maxlevel) {
			$newindent = $indent.($type ? '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;');
			$pre = $type ? '<sup>L</sup>&nbsp;' : '- ';
			foreach ($children[$id] as $v) {
				$id = $v->id;
				$list[$id] = $v;
				$list[$id]->treename = $indent.($v->$parent == 0 ? '' : $pre).$v->name;
				$list[$id]->children = count( @$children[$id] );
				$list[$id]->level = $level;
				$list = mosFPSlideShow::mosTreeRecurse( $id, $newindent, $list, $children, $maxlevel, $level+1, $type );
			}
		}
		return $list;
	}

	function getAccessgroups($registers)
	{

		$db =& JFactory::getDBO();

		// build list of categories
		$query = 'SELECT g.id, g.name' .
		' FROM #__groups AS g' .
		' ORDER BY g.id';
		$groups[] = JHTML::_('select.option', '-1', '- '._FPSS_SEL_ACCESS.' -', 'id', 'name');
		$db->setQuery($query);
		$groups = array_merge($groups, $db->loadObjectList());
		$groups = JHTML::_('select.genericlist',  $groups, 'registers', 'class="inputbox" size="1" ', 'id', 'name', $registers);

		return $groups;

	}

	function getJSections( $name, $active=NULL, $javascript=NULL )
	{

		$db =& JFactory::getDBO();

		$query = "SELECT id AS id, title AS name"
		. "\n FROM #__sections"
		. "\n WHERE published = '1'"
		. "\n ORDER BY ordering"
		;
		$sections[] = JHTML::_('select.option', '0', _FPSS_SEL_SECTION, 'id', 'name');
		$db->setQuery($query);
		$sections = array_merge( $sections, $db->loadObjectList() );
		$sections = JHTML::_('select.genericlist',  $sections, $name, 'class="inputbox" size="1" '. $javascript, 'id', 'name', $active);

		return $sections;

	}

	function getJCategories( $name, $active=NULL, $section=NULL, $javascript=NULL )
	{

		$db =& JFactory::getDBO();

		$query = "SELECT cc.id AS id, cc.title AS name, section"
		. "\n FROM #__categories AS cc"
		. "\n WHERE published = '1'"
		. "\n AND section = '$section'"
		. "\n ORDER BY cc.ordering"
		;
		$categories[] = JHTML::_('select.option', '0', _FPSS_SEL_CATEGORY, 'id', 'name');
		$db->setQuery( $query );
		$categories = array_merge( $categories, $db->loadObjectList() );
		$categories = JHTML::_('select.genericlist',  $categories, $name, 'class="inputbox" size="1" '. $javascript, 'id', 'name', $active);

		return $categories;

	}

	function getJContents( $name, $active=NULL, $categoryid, $javascript=NULL )
	{

		$db =& JFactory::getDBO();

		$query = "SELECT c.id AS id, c.title AS name"
		. "\n FROM #__content AS c"
		. "\n WHERE state = '1'"
		. "\n AND catid = '$categoryid'"
		. "\n ORDER BY c.ordering"
		;
		$contents[] = JHTML::_('select.option', '0', _FPSS_SEL_CONTENT, 'id', 'name');
		$db->setQuery( $query );
		$contents = array_merge( $contents, $db->loadObjectList() );
		$contents = JHTML::_('select.genericlist',  $contents, $name, 'class="inputbox" size="1" '. $javascript, 'id', 'name', $active);

		return $contents;
	}

	function AccessProcessing( &$row, $i ) {
		if ( !$row->registers ) {
			$color_access = 'style="color: green;"';
			$task_access = 'accessregistered';
		} else if ( $row->registers == 1 ) {
			$color_access = 'style="color: red;"';
			$task_access = 'accessspecial';
		} else {
			$color_access = 'style="color: black;"';
			$task_access = 'accesspublic';
		}

		$href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task_access .'\')" '. $color_access .'>
		'. $row->groupname .'
		</a>'
		;

		return $href;
	}

	// CATEGORIES
	function PublishedProcessing( &$row, $i, $publish, $unpublish ) {
		global $mainframe;
		$mosConfig_live_site = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

		$img 	= $row->published ? 'publish_g.png' : 'publish_x.png';
		$task 	= $row->published ? $unpublish : $publish;
		$alt 	= $row->published ? 'Published' : 'Unpublished';
		$action	= $row->published ? 'Unpublish Item' : 'Publish item';

		$href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">
		<img src="'.$mosConfig_live_site.'/administrator/images/'. $img .'" border="0" alt="'. $alt .'" />
		</a>'
		;

		return $href;
	}
	
	function showNumSlides( $id ) {

		$db =& JFactory::getDBO();

		// get a list of the menu items
		$query = "SELECT count(id) "
		. "\n FROM "._FPSS_TABLE_SLIDES
		. "\n WHERE catid = ".$id
		;
		$db->setQuery( $query );
		$num = $db->loadResult();
		return $num;
	}
	
	// MEDIA
	function uploadMedia(&$_FILES, $name, $option, $subfolder="", $width=200, $quality=80) {
		 
		$mosConfig_absolute_path = JPATH_SITE;

		require_once($mosConfig_absolute_path."/administrator/components/".$option."/class.upload.php");

		$handle = new Upload($_FILES[$name]);
		if ($handle->uploaded) {

			$dir = $mosConfig_absolute_path."/components/".$option."/images/".$subfolder;

			if(!is_dir($dir)) mkdir($dir,644);

			if(is_dir($dir)) {
				
				$handle->image_resize = true;
				$handle->image_x = $width;
				$handle->image_ratio_y = true;
				
				// quality checker -- must convert the image to JPG
				$handle->image_convert = 'jpg';
				$handle->jpeg_quality  = $quality;

				$handle->Process($dir);
				if ($handle->processed) {
					$photo = $handle->file_dst_name;
					return  $photo;
				} else {

					return  false;

				}

			} else return false;

		} else {

			return false;

		}

	}
	
	function uploadMediaServer($serverImage, $name, $option, $subfolder="", $width=200, $quality=80) {
		 
		$mosConfig_absolute_path = JPATH_SITE;

		require_once($mosConfig_absolute_path."/administrator/components/".$option."/class.upload.php");

		$handle = new Upload($serverImage);
		if ($handle->uploaded) {

			$dir = $mosConfig_absolute_path."/components/".$option."/images/".$subfolder;

			if(!is_dir($dir)) mkdir($dir,644);

			if(is_dir($dir)) {
				
				$handle->image_resize = true;
				$handle->image_x = $width;
				$handle->image_ratio_y = true;
				
				// quality checker -- must convert the image to JPG
				$handle->image_convert = 'jpg';
				$handle->jpeg_quality  = $quality;

				$handle->Process($dir);
				if ($handle->processed) {
					$photo = $handle->file_dst_name;
					return  $photo;
				} else {

					return  false;

				}

			} else return false;

		} else {

			return false;

		}

	}

}

//////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                                  //
//                               Classes of structure                                               //
//                                                                                                  //
//////////////////////////////////////////////////////////////////////////////////////////////////////


class mosFPSlideShow_toolbars {

	function startTable() {
		?>
		<table cellpadding="0" cellspacing="8" border="0">
		<tr>
		<?php
	}

	function publishList( $task='publish', $alt=null ) {
		if (is_null($alt)) $alt = T_('Published');

		$mainframe =& mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'publish.png', '/administrator/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'publish_f2.png', '/administrator/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center"><a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo T_('Please make a selection from the list to publish')?> '); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);"> <?php echo $image; ?></a></td>
		<?php
	}

	function unpublishList( $task='unpublish', $alt=null ) {
		if (is_null($alt)) $alt = T_('Unpublished');

		$mainframe =& mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'unpublish.png', '/administrator/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'unpublish_f2.png', '/administrator/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center"><a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php T_('Please make a selection from the list to unpublish') ?>'); } else {submitbutton('<?php echo $task;?>', '');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);" > <?php echo $image; ?></a></td>
		<?php
	}

	function addNew( $task='new', $alt=null ) {
		if (is_null($alt)) $alt = T_('New');

		$mainframe =& mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'new.png', '/administrator/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'new_f2.png', '/administrator/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center"><a href="javascript:submitbutton('<?php echo $task;?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);"><?php echo $image; ?></a></td>
		<?php
	}

	function deleteList( $msg='', $task='remove', $alt=null ) {
		if (is_null($alt)) $alt = T_('Delete');

		$mainframe =& mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'delete.png', '/administrator/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'delete_f2.png', '/administrator/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center"><a href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('<?php echo 'Please make a selection from the list to delete'; ?>'); } else if (confirm('<?php echo 'Are you sure you want to delete selected items?'; ?> <?php echo $msg;?>')){ submitbutton('<?php echo $task;?>');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2; ?>',1);"><?php echo $image; ?></a></td>
		<?php
	}

	function save( $task='save', $alt=null ) {
		if (is_null($alt)) $alt = T_('Save');

		$mainframe =& mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'save.png', '/administrator/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'save_f2.png', '/administrator/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('<?php echo $task;?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	function savenew() {
		$mainframe =& mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'save.png', '/administrator/images/', NULL, NULL, T_('save'), 'save' );
		$image2 = $mainframe->ImageCheck( 'save_f2.png', '/administrator/images/', NULL, NULL, T_('save'), 'save', 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('savenew');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('save','','<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	function saveedit() {
		$mainframe =& mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'save.png', '/administrator/images/', NULL, NULL, T_('save'), 'save' );
		$image2 = $mainframe->ImageCheck( 'save_f2.png', '/administrator/images/', NULL, NULL, T_('save'), 'save', 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('saveedit');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('save','','<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	function cancel( $task='cancel', $alt=null ) {
		if (is_null($alt)) $alt = T_('Cancel');

		$mainframe =& mosMainFrame::getInstance();
		$image = $mainframe->ImageCheck( 'cancel.png', '/administrator/images/', NULL, NULL, $alt, $task );
		$image2 = $mainframe->ImageCheck( 'cancel_f2.png', '/administrator/images/', NULL, NULL, $alt, $task, 0 );
		?>
		<td width="25" align="center">
		<a href="javascript:submitbutton('<?php echo $task;?>');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('<?php echo $task;?>','','<?php echo $image2;?>',1);">
		<?php echo $image;?>
		</a>
		</td>
		<?php
	}

	function spacer( $width='' ) {
		if ($width != '') {
		?>
		<td width="<?php echo $width;?>">&nbsp;</td>
		<?php
		} else {
		?>
		<td>&nbsp;</td>
		<?php
		}
	}

	function divider() {
		$image = $mainframe->ImageCheck( 'menu_divider.png', '/administrator/images/' );
		?>
		<td width="25" align="center">
		<?php echo $image; ?>
		</td>
		<?php
	}

	function endTable() {
		?>
		</tr>
		</table>
		<?php
	}

}

//////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                                  //
//                               Get Config Class                                                   //
//                                                                                                  //
//////////////////////////////////////////////////////////////////////////////////////////////////////

// INCLUDE CONFIGURATION
include_once(JPATH_SITE."/administrator/components/com_fpss/configuration.php");
$fpss_config = new FPSSConfig();

// Table names (DO NOT edit if you don't know what you are doing!)
if(!defined("_FPSS_TABLE_CATEGORIES")) DEFINE("_FPSS_TABLE_CATEGORIES","#__fpss_categories");
if(!defined("_FPSS_TABLE_SLIDES")) DEFINE("_FPSS_TABLE_SLIDES","#__fpss_slides");

?>