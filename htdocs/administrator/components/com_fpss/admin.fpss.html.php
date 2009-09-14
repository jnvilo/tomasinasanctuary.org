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

class HTML_FPSlideShow {

	// Header
	function showHeader($option, $task) {
		global $mainframe;

		$jw_fpss_head = '
<!-- JoomlaWorks "Frontpage Slideshow" (v2.0.0) starts here -->
<style type="text/css" media="all">
	@import "components/'.$option.'/style/fpss.css";
</style>
		';
		if($task=="" || $task=="slides") {
			$jw_fpss_head .= '<script type="text/javascript" src="components/'.$option.'/style/slimbox.js"></script>';
		}
		if($task=="edit" || $task=="new") {
			$jw_fpss_head .= '<script type="text/javascript" src="components/'.$option.'/style/smoothbox.js"></script>';
			$jw_fpss_head .= '<script type="text/javascript" src="components/'.$option.'/style/ajax.js"></script>';
		}
		
		$jw_fpss_head .= '
<!-- JoomlaWorks "Frontpage Slideshow" (v2.0.0) ends here -->
		';
		
		$mainframe->addCustomHeadTag($jw_fpss_head);

	}



	// Navigation
	function showNavBar($option, $task) {
		
		$showConfigTab = true;

		// Make sure the user is authorized to view this page
		$user = & JFactory::getUser();
		if (!$user->authorize( 'com_config', 'manage' )) {
			$showConfigTab = false;
		}

	?>

<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 starts here -->
<div id="jwfpss">
<?php

// init variables
$pg_sid = '';
$pg_cfgid = '';
$pg_crdid = '';

if($task=="categories") {$pg_catid = ' id="current"';}
if($task=='' || $task=="slides") {$pg_sid = ' id="current"';}
if($task=="config") {$pg_cfgid = ' id="current"';}
if($task=="credits") {$pg_crdid = ' id="current"';}

if(!$task || $task=="categories" || $task=="slides" || $task=="config" || $task=="credits") {
	$navbar = '';
	$navbar .= '<ul id="jwfpss-menu">';
	$navbar .= '<li><a href="index2.php?option='.$option.'&amp;task=slides"'.$pg_sid.'>'._FPSS_SHOW_SLIDES.'</a></li>';
	$navbar .= '<li><a href="index2.php?option='.$option.'&amp;task=categories"'.$pg_catid.'>'._FPSS_SHOW_CATEGORIES.'</a></li>';
	if($showConfigTab)
		$navbar .= '<li><a href="index2.php?option='.$option.'&amp;task=config"'.$pg_cfgid.'>'._FPSS_SHOW_CONFIG.'</a></li>';
	$navbar .= '<li><a href="index2.php?option='.$option.'&amp;task=credits"'.$pg_crdid.'>'._FPSS_SHOW_CREDITS.'</a></li>';
	$navbar .= '</ul>';
	echo $navbar;
} else {
	$navbar = '';
	$navbar .= '<ul id="jwfpss-menu" class="inactive">';
	$navbar .= '<li>'._FPSS_SHOW_SLIDES.'</li>';
	$navbar .= '<li>'._FPSS_SHOW_CATEGORIES.'</li>';
	if($showConfigTab)
		$navbar .= '<li>'._FPSS_SHOW_CONFIG.'</li>';
	$navbar .= '<li>'._FPSS_SHOW_CREDITS.'</li>';
	$navbar .= '</ul>';
	echo $navbar;
}

	}



	// Footer
	function showFooter() {
		echo '
	<div id="jwfpss-footer">'._FPSS_COPYRIGHTS.'</div>
</div>
<!-- JoomlaWorks "Frontpage Slideshow" v2.0.0 ends here -->
	';
	}



	/* ------------------------------ PAGES ------------------------------ */

	// Slide List Page
	function showSlides( &$rows, &$lists, $pageNav, $option ) {
		global $mainframe;

		jimport('joomla.utilities.date');
		jimport('joomla.filter.filteroutput');

		$db =& JFactory::getDBO();
		$config	=& JFactory::getConfig();
		JHTML::_('behavior.tooltip');

		// action buttons
		JToolBarHelper::title( '<h2 id="jwfpss-logo"></h2>','' );
		JToolBarHelper::publishList('publish');
		JToolBarHelper::unpublishList('unpublish');
		JToolBarHelper::addNew('new');
		JToolBarHelper::deleteList(_FPSS_SLIDES_ALERT_DELETE,'deleteslides');

?>
<h2><?php echo _FPSS_LIST_OF_SLIDES; ?></h2>
<form action="index2.php" method="post" name="adminForm">
  <div class="categorySelect"><?php echo '<b>'._FPSS_SEL_CATEGORIES_LABEL.'</b>'.$lists['categories']; ?></div>
  <table class="adminlist">
    <thead>
      <tr>
        <th>#</th>
        <th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
        <th><?php echo _FPSS_SLIDES_SLIDENAME; ?></th>
        <th><?php echo _FPSS_SLIDES_PUBLISHED; ?></th>
        <th colspan="2"><?php echo _FPSS_SLIDES_REORDER; ?></th>
        <th><?php echo _FPSS_SLIDES_ORDER; ?> <a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )"><img style="vertical-align:text-top;" src="images/filesave.png" alt="Save Order" /></a></th>
        <th><?php echo _FPSS_SLIDES_REGISTERS_ACCESS; ?></th>
        <th><?php echo _FPSS_SLIDES_PATH; ?></th>
        <th><?php echo _FPSS_SLIDES_PREVIEW; ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="10"><?php echo $pageNav->getListFooter(); ?> </td>
      </tr>
    </tfoot>
    <tbody>
      <?php
      $k = 0;
      $nullDate = $db->getNullDate();
      $now = new JDate();
      for ($i=0, $n=count( $rows ); $i < $n; $i++) {
      	$row = &$rows[$i];
      	//$link 	= 'index2.php?option='.$option.'&amp;task=edit&amp;hidemainmenu=1&amp;id='. $row->id;
      	$link 	= JFilterOutput::ampReplace( 'index2.php?option='.$option.'&amp;task=edit&amp;hidemainmenu=1&amp;cid[]='. $row->id );

      	$publish_up = new JDate($row->publish_up);
      	$publish_down = new JDate($row->publish_down);
      	$publish_up->setOffset($config->getValue('config.offset'));
      	$publish_down->setOffset($config->getValue('config.offset'));
      	if ( $now->toUnix() <= $publish_up->toUnix() && $row->state == 1 ) {
      		$img = 'publish_y.png';
      		$alt = JText::_( 'Published' );
      	} else if ( ( $now->toUnix() <= $publish_down->toUnix() || $row->publish_down == $nullDate ) && $row->state == 1 ) {
      		$img = 'publish_g.png';
      		$alt = JText::_( 'Published' );
      	} else if ( $now->toUnix() > $publish_down->toUnix() && $row->state == 1 ) {
      		$img = 'publish_r.png';
      		$alt = JText::_( 'Expired' );
      	} else if ( $row->state == 0 ) {
      		$img = 'publish_x.png';
      		$alt = JText::_( 'Unpublished' );
      	}

      	$times = '';
      	if (isset($row->publish_up)) {
      		if ($row->publish_up == $nullDate) {
      			$times .= JText::_( 'Start: Always' );
      		} else {
      			$times .= JText::_( 'Start' ) .": ". $publish_up->toFormat();
      		}
      	}
      	if (isset($row->publish_down)) {
      		if ($row->publish_down == $nullDate) {
      			$times .= "<br />". JText::_( 'Finish: No Expiry' );
      		} else {
      			$times .= "<br />". JText::_( 'Finish' ) .": ". $publish_down->toFormat();
      		}
      	}

      	$checked 	= JHTML::_('grid.checkedout',   $row, $i );
      	$registers  = mosFPSlideShow::AccessProcessing( $row, $i );
	?>
      <tr class="<?php echo "row$k"; ?>">
        <td><?php echo $pageNav->getRowOffset( $i ); ?></td>
        <td><?php echo $checked; ?></td>
        <td><a href="<?php echo $link; ?>" title="Edit Slide"> <?php echo htmlspecialchars($row->name, ENT_QUOTES); ?> </a></td>
        <?php if ($times) { ?>
        <td><span class="editlinktip hasTip" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo $times; ?>"><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->state ? 'unpublish' : 'publish' ?>')"><img alt="Change publishing status" src="images/<?php echo $img;?>" /></a></span> </td>
        <?php } ?>
        <td><?php echo $pageNav->orderUpIcon( $i, ($row->catid == @$rows[$i-1]->catid) ); ?></td>
        <td><?php echo $pageNav->orderDownIcon( $i, $n, ($row->catid == @$rows[$i+1]->catid) ); ?></td>
        <td><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" /></td>
        <td><?php echo $registers; ?></td>
        <td><?php echo $row->path; ?></td>
        <td><a href="<?php echo $mainframe->getSiteURL().$row->path; ?>" rel="lightbox[fpss]" title="<?php echo _FPSS_FE_SLIMBOX.' <b>'.$row->name.'</b>'; ?>"><img alt="Preview image..." src="components/<?php echo $option; ?>/images/picture.png" /></a></td>
      </tr>
      <?php
      $k = 1 - $k; }
?>
    </tbody>
  </table>
  <input type="hidden" name="option" value="<?php echo $option; ?>" />
  <input type="hidden" name="task" value="slides" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="hidemainmenu" value="0" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
	}



	// Slides edit page
	function editSlide( &$slide, $option, $lists, &$config, &$catparams ) {
		global $mainframe;

		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		$text = intval($slide->id) ? JText::_( 'Edit' ) : JText::_( 'New' );

		// action buttons
		JToolBarHelper::title( '<h2 id="jwfpss-logo"></h2>','' );
		JToolBarHelper::save('save');
		JToolBarHelper::cancel('cancel');

		// clean item data
		JFilterOutput::objectHTMLSafe( $slide );

		if($slide->id) {

			$showtitle 		= ($slide->showtitle)?true:false;
			$showseccat 	= ($slide->showseccat)?true:false;
			$showcustomtext = ($slide->showcustomtext)?true:false;
			$showplaintext 	= ($slide->showplaintext)?true:false;
			$showreadmore 	= ($slide->showreadmore)?true:false;

		} else {

			$showtitle 		= true;
			$showseccat 	= true;
			$showcustomtext = true;
			$showplaintext 	= true;
			$showreadmore 	= true;

		}

?>
<script type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	if (trim(form.name.value) == "") {
		alert( "<?php echo _FPSS_SLIDE_ALERT_MUST_NAME; ?>" );
	} else if ( form.catid.value == "0" ){
		alert( "<?php echo _FPSS_SLIDE_ALERT_MUST_CATEGORY; ?>" );
	} else if ( form.linkto[2].checked==true && form.customlink.value=="" ){
		alert( "<?php echo _FPSS_SLIDE_ALERT_MUST_MENU; ?>" );
	} else if ( form.linkto[0].checked==true && form.contentid.value==0 ){
		alert( "<?php echo _FPSS_SLIDE_ALERT_MUST_MENU; ?>" );
	} else if ( form.registers.value == "-1" ){
		alert( "<?php echo _FPSS_SLIDE_ALERT_MUST_REGIST; ?>" );
	} else {
		submitform( pressbutton );
	}
}
</script>
<?php if($config->articlelist) { ?>
<script type="text/javascript">	
var ajax = new sack();
function getCategoryList(sel) {
	//check radio
	document.adminForm.linkto[0].checked=true;
	var sectionId = sel.options[sel.selectedIndex].value;
	document.getElementById('categoryid').options.length = 0;	// Empty category select box
	document.getElementById('contentid').options.length = 0;	// Empty category select box
	if(sectionId.length>0){
		ajax.requestFile = '<?php echo $mainframe->getSiteURL(); ?>index2.php?option=<?php echo $option; ?>&task=getcategories&sid='+sectionId;	// Specifying which file to get
		ajax.onCompletion = createCategories;	// Specify function that will be executed after file has been found
		ajax.runAJAX();		// Execute AJAX function
	}
}
function createCategories() {
	var obj = document.getElementById('categoryid');
	eval(ajax.response);	// Executing the response from Ajax as Javascript code
	var obj2 = document.getElementById('contentid');
	obj2.options[obj2.options.length] = new Option('<?php echo _FPSS_SEL_CONTENT; ?>','0');
}
function getContentList(sel) {
	var categoryId = sel.options[sel.selectedIndex].value;
	document.getElementById('contentid').options.length = 0;	// Empty category select box
	if(categoryId.length>0){
		ajax.requestFile = '<?php echo $mainframe->getSiteURL(); ?>index2.php?option=<?php echo $option; ?>&task=getcontents&cid='+categoryId;	// Specifying which file to get
		ajax.onCompletion = createContents;	// Specify function that will be executed after file has been found
		ajax.runAJAX();		// Execute AJAX function
	}
}
function createContents() {
	var obj = document.getElementById('contentid');
	eval(ajax.response);	// Executing the response from Ajax as Javascript code
}
</script>
<?php } ?>
<h2><?php if($slide->name) echo _FPSS_SLIDE_EDIT_IMAGE." <span>".$slide->name."</span>"; else echo _FPSS_SLIDE_NEW_IMAGE; ?></h2>
<form action="index2.php" method="post" enctype="multipart/form-data" name="adminForm">
  <table class="adminForm">
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_SLIDE_EDIT_INTROTEXT; ?></h3></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_IMAGENAME; ?></th>
      <td><input class="inputbox extended" type="text" name="name" value="<?php echo $slide->name; ?>" /></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_CATEGORY; ?></th>
      <td><?php echo $lists["categories"]; ?></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_PUBLISHED; ?></th>
      <td><input type="checkbox" name="published" value="1" <?php if(!$slide->id) echo 'checked="checked"'; else  echo $slide->state ? 'checked="checked"' : ''; ?> /></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_START_PUBLISHING; ?></th>
      <td><?php echo $lists['publish_up']; ?></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_FINISH_PUBLISHING; ?></th>
      <td><?php echo $lists['publish_down']; ?></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_REGISTERS; ?></th>
      <td><?php echo $lists["groups"]; ?></td>
    </tr>    
    <tr>
      <th><?php echo _FPSS_SLIDE_CUSTOM_TEXT; ?></th>
      <td><?php
		if($config->editor==1) {
			echo $lists['editor'];
		} else {
			echo "<textarea class=\"inputbox\" name=\"ctext\" cols=\"75\" rows=\"3\">".$row->ctext."</textarea>";
		}
        ?>
      </td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_CUSTOM_PLAINTEXT; ?></th>
      <td><input class="inputbox extended" name="plaintext" type="text" value="<?php echo $slide->plaintext; ?>" /></td>
    </tr>
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_SLIDE_LINKOPTIONS; ?></h3></td>
    </tr>    
    <tr>
      <th><?php echo _FPSS_SLIDE_LINKTO; ?></th>
      <td><input type="radio" name="linkto" value="2" <?php if($slide->itemlink!=0) echo "checked"; ?> />
        <?php 
        if($config->articlelist) echo $lists["jsections"]." ".$lists["jcategories"]." ".$lists["jcontents"];
        else echo mosFPSlideShow::builtDropDown($slide->itemlink);

        echo _FPSS_SLIDE_LINKS_CI;
        ?>
        <br />
        <input type="radio" name="linkto" value="3" <?php if($slide->menulink!=0) echo "checked"; ?> />
        <?php echo $lists["menu"]; ?> <?php echo _FPSS_SLIDE_LINKS_MI; ?><br />
        <input type="radio" name="linkto" id="linkto_first" value="1" <?php if($slide->customlink!="" ) echo "checked=\"checked\""; ?> />
        <input class="inputbox extended" type="text" name="customlink" value="<?php echo $slide->customlink; ?>" onclick="this.form.linkto[2].checked=true" />
        <?php echo _FPSS_SLIDE_LINKS_NORMAL; ?> <br />
        <input type="radio" name="linkto" value="4" <?php if(!$slide->id || $slide->nolink) echo "checked=\"checked\""; ?> />
        <?php echo _FPSS_SLIDE_LINKS_NOLINK; ?> </td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_TARGET; ?></th>
      <td><?php echo $lists["target"]; ?></td>
    </tr>
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_SLIDE_DISPLAYOPTIONS; ?></h3></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_SHOWTITLE; ?></th>
      <td><input type="checkbox" name="showtitle" value="1" <?php echo $showtitle ? 'checked="checked"' : ''; ?> /></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_SHOWSECCAT; ?></th>
      <td><input type="checkbox" name="showseccat" value="1" <?php echo $showseccat ? 'checked="checked"' : ''; ?> /></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_SHOWCUSTOMTEXT; ?></th>
      <td><input type="checkbox" name="showcustomtext" value="1" <?php echo $showcustomtext ? 'checked="checked"' : ''; ?> /></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_SHOWPLAINTEXT; ?></th>
      <td><input type="checkbox" name="showplaintext" value="1" <?php echo $showplaintext ? 'checked="checked"' : ''; ?> /></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_SHOWREADMORE; ?></th>
      <td><input type="checkbox" name="showreadmore" value="1" <?php echo $showreadmore ? 'checked="checked"' : ''; ?> /></td>
    </tr>
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_SLIDE_IMAGEACTION; ?></h3></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_UPLOADFROMPC; ?></th>
      <td><input type="radio" name="imageaction" value="2" <?php if(!$slide->name) echo "checked"; ?> />
        <input type="file" name="image" onclick="this.form.imageaction[0].checked=true" />
        <br />
        <?php if($config->show_width==0) {echo _FPSS_SLIDES_UPLOADFROMPC_DEF_EXP; } ?>
        <?php if($config->show_width==1 || $config->show_quality==1) { ?>
        <?php echo _FPSS_SLIDES_UPLOADCUSTOM; ?>
        <?php if($config->show_width==1) { ?>
        <b><?php echo _FPSS_SLIDES_UPLOADWIDTH ?></b>
        <input class="inputbox" type="text" name="resize_x" size="4">
        <?php } ?>
        <?php if($config->show_quality==1) { ?>
        <b><?php echo _FPSS_SLIDES_UPLOADQUALITY ?></b>
        <input class="inputbox" type="text" name="quality" size="4">
        <?php } ?>
        <?php } ?>        
        </td>
    </tr>
    <tr>
        <th><?php echo _FPSS_SLIDE_SERVERIMAGE; ?></th>
        <td>
			<input type="radio" name="imageaction" value="3" /><input class="inputbox" type="text" name="serverimage" onclick="this.form.imageaction[1].checked=true" /><a href="<?php echo $mainframe->getSiteURL(); ?>index.php?option=<?php echo $option; ?>&tmpl=component&task=getimages&KeepThis=true&TB_iframe=true&height=560&width=800&modal=true" class="smoothbox"><?php echo _FPSS_SLIDE_BROWSE; ?></a> <?php echo _FPSS_SLIDE_BROWSE_EXP; ?>
		</td>
    </tr>
    <?php if($slide->name) { ?>
    <tr>
      <th><?php echo _FPSS_SLIDE_NOACTION; ?></th>
      <td><input type="radio" name="imageaction" value="1" checked="checked" /></td>
    </tr>
    <?php } else { ?>
    <div style="display:none;">
      <input type="radio" name="imageaction" value="1" />
    </div>
    <?php } ?>
    <?php if ($slide->path) { ?>
    <!--
    <tr>
      <th><?php echo _FPSS_SLIDE_EXIMG_PATH; ?></th>
      <td><?php echo $slide->path; ?></td>
    </tr>
    -->
    <tr>
      <th><?php echo _FPSS_SLIDE_EXIMG_PREVIEW; ?></th>
      <td><img class="showimg" src="<?php echo $mainframe->getSiteURL().$slide->path; ?>" /> </td>
    </tr>
    <?php } ?>
    <?php if($config->septhumb) { ?>
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_SLIDE_THUMBACTION; ?></h3></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_SLIDE_THUMB_UPLOADFROMPC; ?></th>
      <td><input type="file" name="thumb" /></td>
    </tr>
    <?php } ?>
    <?php if($slide->thumb) { ?>
    <tr>
      <th><?php echo _FPSS_SLIDE_EXTHUMB_PREVIEW; ?></th>
      <td><img class="showthb" src="<?php echo $mainframe->getSiteURL().$slide->thumb; ?>" /> </td>
    </tr>
    <?php } ?>
  </table>
  <input type="hidden" name="oldpathtype" value="<?php echo $slide->path_type; ?>" />
  <input type="hidden" name="id" value="<?php echo $slide->id; ?>" />
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="hidemainmenu" value="0" />
  <input type="hidden" name="filter_catid" value="<?php echo $slide->catid; ?>" />
  <?php echo JHTML::_( 'form.token' ); //KeepThis=true&TB_iframe=true&height=400&width=600&modal=true ?>
</form>
<?php
	}



	// CATEGORIES
	function showCategories( &$rows, &$pageNav, $option ) {
		global $mainframe;

		$db =& JFactory::getDBO();
		$config	=& JFactory::getConfig();
		JHTML::_('behavior.tooltip');

		// action buttons
		JToolBarHelper::title( '<h2 id="jwfpss-logo"></h2>','' );
		JToolBarHelper::publishList('publish_category');
		JToolBarHelper::unpublishList('unpublish_category');
		JToolBarHelper::addNew('new_category');
		JToolBarHelper::deleteList(_FPSS_SLIDES_ALERT_DELETE,'delete_category');
		?>
<h2><?php echo _FPSS_CATEGORIES_LIST; ?></h2>
<form action="index2.php" method="post" name="adminForm">
  <table class="adminlist">
    <thead>
      <tr>
        <th>#</th>
        <th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows );?>);" /></th>
        <th style="text-align:left;"><?php echo _FPSS_CATEGORIES_ID; ?></th>
        <th><?php echo _FPSS_CATEGORIES_NAME; ?></th>
        <th><?php echo _FPSS_CATEGORIES_PUBLISHED; ?></th>
        <th><?php echo _FPSS_CATEGORIES_NUM_SLIDES; ?></th>
        <th></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="10"><?php echo $pageNav->getListFooter(); ?> </td>
      </tr>
    </tfoot>
    <tbody>
      <?php
    $k = 0;
    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
    	$row 		= &$rows[$i];
    	$link 		= JFilterOutput::ampReplace( 'index2.php?option='.$option.'&amp;task=edit_category&amp;hidemainmenu=1&amp;cid[]='. $row->id );
    	$checked 	= JHTML::_('grid.checkedout',   $row, $i );
    	$published 	= mosFPSlideShow::PublishedProcessing( $row, $i, "publish_category", "unpublish_category" );
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td><?php echo $pageNav->getRowOffset( $i ); ?> </td>
        <td><?php echo $checked; ?> </td>
        <td width="5px"><?php echo $row->id; ?> </td>
        <td><a href="<?php echo $link; ?>"><?php echo htmlspecialchars($row->name, ENT_QUOTES); ?></a> </td>
        <td><?php echo $published;?> </td>
        <td><?php echo intval(mosFPSlideShow::showNumSlides($row->id));?> </td>
        <td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?option=<?php echo $option; ?>&amp;task=slides&amp;filter_catid=<?php echo $row->id; ?>"> <?php echo _FPSS_CATEGORIES_SHOWSLIDES_LINK; ?> </a> </td>
        <?php $k = 1 - $k; ?>
      </tr>
      <?php
    }
		?>
    </tbody>
  </table>
  <input type="hidden" name="option" value="<?php echo $option?>" />
  <input type="hidden" name="task" value="categories" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="hidemainmenu" value="0" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
	}

	function editCategory( &$row, &$lists, $option ) {
		global $mainframe;

		$text = intval($row->id) ? JText::_( 'Edit' ) : JText::_( 'New' );

		// action buttons
		JToolBarHelper::title( '<h2 id="jwfpss-logo"></h2>','' );
		JToolBarHelper::save('save_category');
		JToolBarHelper::cancel('cancel_category');

		// clean item data
		JFilterOutput::objectHTMLSafe( $row );

		// add scripts to the head
		$jw_fpss_head_edit = '
<script type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == "cancel_category") {
		submitform( pressbutton );
		return;
	}
	// do field validation
	if (form.name.value == ""){
		alert( "'._FPSS_CATEGORY_ALERT_MUST_NAME.'" );
	} else {
		submitform( pressbutton );
	}
}
//-->
</script>
		';

		$mainframe->addCustomHeadTag($jw_fpss_head_edit);

?>
<h2>
  <?php if($row->name) echo _FPSS_CATEGORIES_EDIT_CATEGORY." <span>".$row->name."</span>"; else echo _FPSS_CATEGORIES_NEW_CATEGORY; ?>
</h2>
<form action="index2.php" method="post" name="adminForm">
  <table class="adminForm">
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_CATEGORY_GENERALPARAMS; ?></h3></td>
    </tr>
    <tr>
      <th><b><?php echo _FPSS_CATEGORIES_CATEGORYNAME; ?></b></th>
      <td><input class="inputbox extended" type="text" name="name" value="<?php echo $row->name; ?>" /></td>
    </tr>
    <tr>
      <th><b><?php echo _FPSS_CATEGORIES_PUBLISHED; ?></b></th>
      <td><?php echo $lists['published']; ?></td>
    </tr>
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_CATEGORY_SLIDEPARAMS; ?></h3></td>
    </tr>
    <tr>
      <th><b><?php echo _FPSS_CATEGORY_DEFAULT_WIDTH; ?></b></th>
      <td><input class="inputbox" type="text" name="width" value="<?php echo ($row->width)?$row->width:400; ?>" /></td>
    </tr>
    <tr>
      <th><b><?php echo _FPSS_CATEGORY_DEFAULT_QUALITY; ?></b></th>
      <td><input class="inputbox" type="text" name="quality" value="<?php echo ($row->quality)?$row->quality:80; ?>" /></td>
    </tr>
    <tr>
      <th><b><?php echo _FPSS_CATEGORY_DEFAULT_WIDTH_THUMBS; ?></b></th>
      <td><input class="inputbox" type="text" name="width_thumb" value="<?php echo ($row->width_thumb)?$row->width_thumb:100; ?>" /></td>
    </tr>
    <tr>
      <th><b><?php echo _FPSS_CATEGORY_DEFAULT_QUALITY_THUMBS; ?></b></th>
      <td><input class="inputbox" type="text" name="quality_thumb" value="<?php echo ($row->quality_thumb)?$row->quality_thumb:75; ?>" /></td>
    </tr>
  </table>
  <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="task" value="" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php	
	}



	// Configuration Page
	function editConfig( &$fpss_config, $lists, $option ) {
		global $mainframe;

		// Make sure the user is authorized to view this page
		$user = & JFactory::getUser();
		if (!$user->authorize( 'com_config', 'manage' )) {
			$mainframe->redirect('index.php', JText::_('ALERTNOTAUTH'));
		}

		// action buttons
		JToolBarHelper::title( '<h2 id="jwfpss-logo"></h2>','' );
		JToolBarHelper::save('save_config');

?>
<script type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	submitform( pressbutton );
}
</script>
<h2><?php echo _FPSS_CONFIG_TITLE; ?></h2>
<form method="post" action="index.php" name="adminForm">
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="option" value="<?php echo $option; ?>" />
  <?php echo JHTML::_( 'form.token' ); ?>
  <table class="adminForm">
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_CONFIG_FORMPARAMS; ?></h3></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_CONFIG_EDITOR; ?></th>
      <td><?php echo $lists['editor']; ?></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_CONFIG_ARTICLELIST_TEXT; ?></th>
      <td><?php echo $lists['articlelist']; ?></td>
    </tr>
    <tr>
      <td colspan="2"><h3><?php echo _FPSS_CONFIG_ADVPARAMS; ?></h3></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_CONFIG_SEPTHUMB_TEXT; ?></th>
      <td><?php echo $lists['septhumb']; ?></td>
    </tr>    
    <tr>
      <th><?php echo _FPSS_CONFIG_SHOW_WIDTH; ?></th>
      <td><?php echo $lists['show_width']; ?></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_CONFIG_SHOW_QUALITY; ?></th>
      <td><?php echo $lists['show_quality']; ?></td>
    </tr>
    <tr>
      <th><?php echo _FPSS_CONFIG_BASEPATH_TEXT; ?></th>
      <td><input class="inputbox extended" type="text" name="basepath" value="<?php echo $fpss_config->basepath; ?>" /></td>
    </tr>
  </table>
</form>
<?php
	}



	// Credits / About page
	function viewCredits( $option ) {

		JToolBarHelper::title( '<h2 id="jwfpss-logo"></h2>','' );
		echo '<h2>'._FPSS_FE_CREDITS.'</h2><div class="jwpds-comments">'._FPSS_ABOUT.'</div>';

	}
	
	

	// END CLASS
}

?>