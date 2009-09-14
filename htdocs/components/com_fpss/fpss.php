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
defined('_JEXEC') or die('Restricted access');
error_reporting(0);
// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'tables');

$mosConfig_absolute_path 	= JPATH_SITE;
$mosConfig_live_site 		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);

// INCLUDE LANGUAGE FILES
if (file_exists($mosConfig_absolute_path.'/administrator/components/'.$option.'/language/'.$langTag.'.php')) {
	include_once ($mosConfig_absolute_path.'/administrator/components/'.$option.'/language/'.$langTag.'.php');
} else {
	include_once ($mosConfig_absolute_path.'/administrator/components/'.$option.'/language/en-GB.php');
}

require_once( JApplicationHelper::getPath('class') );
require_once( JApplicationHelper::getPath('front_html') );

$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
JArrayHelper::toInteger($cid, array(0));

$user = & JFactory::getUser();
if ( !$user->gid && !in_array($task,array("getcategories","getcontents","getimages")) ) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
	exit();
}

$id 	= JRequest::getVar('id', 0, '', 'int');

if($task=="getimages")  HTML_FPSlideShow::showHeaderIM($option, $task);

switch ($task) {

	// HELPERS
	case 'getcategories':
		getcategories();
		break;

	case 'getcontents':
		getcontents();
		break;

	case 'getimages':
		getimages();
		break;

		// DEFAULT
	default:
		showNothing( $option );
		break;
}

if($task=="getimages")  HTML_FPSlideShow::showFooter();

function showNothing($option) {

    echo "<div>Nothing to see here! ;-)</div>";

}

/////////////////////////////// HELPERS  ////////////////////////////////////////////////

// AJAX
function getcategories() {

	$db =& JFactory::getDBO();

	$sid = JRequest::getVar( 'sid', 0, 'get', 'int' );

	if (!headers_sent()) {

		while(@ob_end_clean());
		ob_start();

		// build list of sections
		$query = "SELECT cc.id AS value, cc.title AS text"
		. "\n FROM #__categories AS cc"
		. "\n WHERE published = '1'"
		. "\n AND section = '$sid'"
		. "\n ORDER BY cc.ordering"
		;

		$db->setQuery($query);
		$categories = $db->loadObjectList();
		//echo $query;
		echo "obj.options[obj.options.length] = new Option('"._FPSS_SEL_CATEGORY."','0');";
		foreach ($categories as $category) {
			$s = str_replace("'","\'",$category->text);
			echo "obj.options[obj.options.length] = new Option('$s','$category->value');";
		}

		$out = ob_get_contents();

		ob_end_clean();

		echo $out;

		exit();

	} else echo "Headers sent!";

}

function getcontents() {

	$db =& JFactory::getDBO();

	$cid = JRequest::getVar( 'cid', 0, 'get', 'int' );

	if (!headers_sent()) {

		while(@ob_end_clean());
		ob_start();

		// build list of sections
		$query = "SELECT c.id AS value, c.title AS text"
		. "\n FROM #__content AS c"
		. "\n WHERE state = '1'"
		. "\n AND catid = '$cid'"
		. "\n ORDER BY c.ordering"
		;

		$db->setQuery($query);
		$contents = $db->loadObjectList();
		//echo $query;
		echo "obj.options[obj.options.length] = new Option('"._FPSS_SEL_CONTENT."','0');";
		foreach ($contents as $content) {
			$c = str_replace("'","\'",$content->text);
			echo "obj.options[obj.options.length] = new Option('$c','$content->value');";
		}

		$out = ob_get_contents();

		ob_end_clean();

		echo $out;

		exit();

	} else {
		echo "Headers sent!";
	}

}


// FPSS IMAGE MANAGER (POPUP)

function makeSafe( $file ) {
	return str_replace( '..', '', urldecode( $file ) );
}

function getimages() {

	global $mainframe, $option;
	$live_site = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();

	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'configuration.php');
	$config = new FPSSConfig();

	if(!defined("_FPSSBASEPATH")) define("_FPSSBASEPATH",$config->basepath);
	if(!defined("_FPSSLIVESITE")) define("_FPSSLIVESITE",$live_site);

	$listdir = JRequest::getVar( 'listdir', _FPSSBASEPATH, 'get', 'text' );
	$listdir = makeSafe($listdir);

	$js =  '<script type="text/javascript" language="javascript">
				<!--
					function sendtomain(thesi) {
						parent.document.forms[\'adminForm\'].serverimage.value = document.getElementById(\'image\' + thesi).src;
						parent.document.forms[\'adminForm\'].imageaction[1].checked=true;
						top.TB_remove();
						return false;
					}
				//-->
			</script>';

	$mainframe->addCustomHeadTag($js);

		?>
        <div id="jwfpss-imgbrowser">
		<form method="get" name="adminForm" action="index.php">
	  		<div class="changedir">
				<?php echo _FPSS_IMGBR_DIR; ?> <?php echo changeDir($listdir); ?>
                <div class="jwfpss-clr"></div>
            </div>
        	<h2><?php echo _FPSS_IMGBR_LIST; ?></h2>
        	<div class="jwpds-comments"><?php echo _FPSS_IMGBR_CLICK; ?></div>
	  		<?php echo showDir($listdir); ?>
	    	<input type="hidden" name="option" value="<?php echo $option; ?>">
	    	<input type="hidden" name="task" value="getimages">
            <input type="hidden" name="tmpl" value="component">
		</form>
        </div>
		<?php


}

function changeDir( $listdir ) {

	$imgFiles 	= listofdirectories( _FPSSBASEPATH );
	$images 	= array();
	$folders 	= array();
	$folders[] 	= JHTML::_('select.option', _FPSSBASEPATH, '/', 'id', 'name');

	$len = strlen( _FPSSBASEPATH );
	foreach ($imgFiles as $file) {
		$folders[] 	= JHTML::_('select.option', $file, substr( $file, $len ), 'id', 'name');
	}
	if (is_array( $folders )) {
		sort( $folders );
	}

	return JHTML::_('select.genericlist',  $folders, 'listdir', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'name', $listdir);
}

function listofdirectories( $listdir ) {

	static $filelist = array();
	static $dirlist = array();

	if(is_dir(JPATH_BASE.DS.$listdir)) {
		$dh = opendir(JPATH_BASE.DS.$listdir);
		while (false !== ($dir = readdir($dh))) {
			if (is_dir(JPATH_BASE.DS.$listdir .'/'. $dir) && $dir !== '.' && $dir !== '..' && strtolower($dir) !== 'cvs' && strtolower($dir) !== '.svn') {
				$subbase = $listdir .'/'. $dir;
				$dirlist[] = $subbase;
				$subdirlist = listofdirectories($subbase);
			}
		}
		closedir($dh);
	}
	return $dirlist;
}

function showDir($listdir) {

	// get list of images
	$d = @dir( JPATH_BASE.DS.$listdir);

	if($d) {
		//var_dump($d);
		$images 	= array();
		$allowable 	= 'gif|jpg|png';

		while (false !== ($entry = $d->read())) {
			$img_file = $entry;
			if(is_file( JPATH_BASE.DS.$listdir.'/'.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html' ) {
				if (eregi( $allowable, $img_file )) {
					$image_info 				= @getimagesize( JPATH_BASE.DS.$listdir.'/'.$img_file);
					$file_details['file'] 		= JPATH_BASE.DS.$listdir."/".$img_file;
					$file_details['img_info'] 	= $image_info;
					$file_details['size'] 		= filesize( JPATH_BASE.DS.$listdir."/".$img_file);
					$images[$entry] 			= $file_details;
				}
			}
		}
		$d->close();

		if(count($images) > 0 || count($folders) > 0 || count($docs) > 0) {
			//now sort the folders and images by name.
			ksort($images);

			if(count($images)>0) {
				$j=0;
				echo "<table class=\"adminList\">
						<tr>
							<th>"._FPSS_IMGBR_PREVIEW."</th>
							<th>"._FPSS_IMGBR_FILENAME."</th>
							<th>"._FPSS_IMGBR_DIMENSIONS."</th>
							<th>"._FPSS_IMGBR_SIZE."</th>
						</tr>
					";
				for($i=0; $i<count($images); $i++) {

					$image_name = key($images);
					show_image($images[$image_name]['file'], $image_name, $images[$image_name]['img_info'], $images[$image_name]['size'],$listdir,$i);
					next($images);

				}
				echo "</table>";
			}

		}
	}
}

function show_image($img, $file, $info, $size, $listdir, $i) {

	$img_file 		= basename($img);
	$img_url_link 	= _FPSSLIVESITE.$listdir ."/". rawurlencode( $img_file );
	$filesize 		= parse_size( $size );
	if ( ( $info[0] > 120 ) || ( $info[0] > 120 ) ) {
		$img_dimensions = imageResize($info[0], $info[1], 120);
	} else {
		$img_dimensions = 'width="'. $info[0] .'" height="'. $info[1] .'"';
	}
		?>
		<tr>
  			<td><img id="image<?php echo $i?>" onmouseover="this.style.cursor='pointer'" onclick="javascript:sendtomain(<?php echo $i?>);" src="<?php echo $img_url_link; ?>" <?php echo $img_dimensions; ?> border="0" /></td>
  			<td><b><?php echo htmlspecialchars( substr( $file, 0, 20 ) . ( strlen( $file ) > 20 ? '...' : ''), ENT_QUOTES ); ?></b></td>
  			<td><b class="red"><?php echo $info[0]; ?>x<?php echo $info[1]; ?>px</b></td>
  			<td><?php echo $filesize; ?></td>
		</tr>
		<?php
}

function parse_size($size){
	if($size < 1024) {
		return $size.' bytes';
	} else if($size >= 1024 && $size < 1024*1024) {
		return sprintf('%01.2f',$size/1024.0).' Kb';
	} else {
		return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';
	}
}

function imageResize($width, $height, $target) {

	if ($width > $height) {
		$percentage = ($target / $width);
	} else {
		$percentage = ($target / $height);
	}

	$width = round($width * $percentage);
	$height = round($height * $percentage);

	return "width=\"$width\" height=\"$height\"";

}
// END

?>
