<?php
/**
 * Joomla Flash uploader 2.8 Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

class HTML_joomla_flash_uploader {

var $currentrow = 0;
function getRowClass() {
  global $currentrow;
  return (($currentrow++ %2 ) ==0 ) ? ' class="row0"' : ' class="row1"'; 
}

function errorRights () { 
  echo '<form action="index2.php" method="post" name="adminForm">
      <input type="hidden" name="option" value="com_joomla_flash_uploader"/>
      <input type="hidden" name="task" value="config"/>
      </form>';
  echo '<center><div class="errordiv">';
  echo JText::_('MES_RIGHTS');
  echo '</div></center>';
}

function showUpload ($row, $realfolder, $folder) { 

$language = &JFactory::getLanguage();
$Cfg_lang = $language->getTag();

$relative_dir = dirname($_SERVER['PHP_SELF']);
$relative_dir = rtrim($relative_dir,"\\/.") . '/'; // we replace to get a consistent output with different php versions!

$width=$row->display_width;
$height=floor($width*(340/650));
	
echo '<center><form action="index2.php" method="post" name="adminForm">
<input type="hidden" name="option" value="com_joomla_flash_uploader"/>
<input type="hidden" name="task" value="config"/>
</form>';

if ($row->enable_setting=="false") { // no flash only text!
  echo JFULanguage::getLanguage($row->text_top_lang,$row->text_top, "TEXT_TOP" , $row->id);
  return;
}

if (!file_exists($folder) && $realfolder != "") {
  echo JText::_('ERR_FOLDER') . " : " . $realfolder;
  return;
}

echo "<h3>";
echo JFULanguage::getLanguage($row->text_title_lang,$row->text_title, "TEXT_TITLE" ,$row->id);
echo "</h3>";
echo JFULanguage::getLanguage($row->text_top_lang,$row->text_top, "TEXT_TOP" , $row->id);
echo "<p>";

$lang = JFULanguage::mapLangJoomlatoTFU($Cfg_lang);
$base_dir = "components/com_joomla_flash_uploader/tfu";
$extra_settings = '';
if ($row->description_mode == "true") {
  $extra_settings .= '&tfu_description_mode=true';
}
if ($row->hide_remote_view == "true") {
  $extra_settings .= '&hide_remote_view=true';
}

echo '
  <div style="z-index:1">
    <div id="flashcontent"><div class="noflash">' . sprintf(JText::_('MES_NO_FLASH'),'<a href="http://www.tinywebgallery.com/en/tfu/web_jfu.php">', '</a>') . '</p></div></div>
	<script type="text/javascript" src="components/com_joomla_flash_uploader/tfu/swfobject.js"></script>
	<script type="text/javascript">
var flashvars = {};
var params = {};
var attributes = {};
params.allowfullscreen = "true";
';
if ($row->fix_overlay == "true") {
  echo 'params.wmode ="transparent";'; 
}
if ($width == '650') {
  echo 'params.scale = "noScale"';
} 
echo '
   swfobject.embedSWF("components/com_joomla_flash_uploader/tfu/tfu_291.swf?joomla=true&lang='.$lang.'&session_id='.session_id().'&base='.$base_dir.'&relative_dir='.$relative_dir.$extra_settings.'&'.$row->swf_text.'", "flashcontent", "'.$width.'", "'.$height.'", "8.0.0", "", flashvars, params, attributes);
';
echo <<< HTML
	</script>
	<!-- end include with Javascript -->
	<!-- static html include -->
	<noscript>
	Please enable Javascript
	</noscript> 
</div>
HTML;
echo "</p>";
echo "<br/>";
echo JFULanguage::getLanguage($row->text_bottom_lang, $row->text_bottom, "TEXT_BOTTOM" ,$row->id);
echo "<br/>";
if (!file_exists(dirname(__FILE__) . "/tfu/.htaccess")) {
  printf(JText::_('C_HTACCESS_CREATE'),'<a href="#createhtaccess" onclick="return submitform(\'createhtaccess\')"><b>','</b></a>');
} else {
printf(JText::_('C_HTACCESS_DELETE'),'<a href="#deletehtaccess" onclick="return submitform(\'deletehtaccess\')"><b>','</b></a>');
}
echo "</center>";
}

function listConfig($rows, $jfu_config) {
$count = count($rows) + 1; // because id starts at 1
$config = new JConfig();

$vers = $jfu_config['version'];

// we build the version string!
$latest_version = JFUHelper::getlatestVersion();
$version_description = $latest_version;
if ($latest_version == -1) {
$version_description = '<span class="jfu_nocheck">' . JText::_('C_VERSION_NO') . ' <a href="http://jfu.tinywebgallery.com" target="_blank">http://jfu.tinywebgallery.com</a> ' . JText::_('C_VERSION_NO2') . '</span>';
} else if ($latest_version != $vers) {
$version_description = '<span class="jfu_old">' . JText::_('C_VERSION_OLD1') .  ' <a href="http://jfu.tinywebgallery.com" target="_blank">http://jfu.tinywebgallery.com</a> ' . JText::_('C_VERSION_OLD2') . JText::_('C_VERSION_OLD3') . ' <b>'.$latest_version.'</b>. '.JText::_('C_VERSION_OLD4').' <b>' . $vers . '.</b><p>
'.JText::_('C_VERSION_OLD5').' <a href="http://blog.tinywebgallery.com" target="_blank">'.JText::_('C_VERSION_OLD6').'</a>.' . '</p></span>';
} else {
$version_description = '<span class="jfu_current">' . JText::_('C_VERSION_OK') . '</span>';
}

echo '
<script type="text/javascript" src="components/com_joomla_flash_uploader/jfu.js"></script>
<form action="index2.php" method="post" name="adminForm">
  <h2>'.JText::_('C_TITLE').'</h2>		
	'.JText::_('C_TEXT').'
<h3 style="text-align:left;">'.JText::_('E_H3_PROFILES').'</h3>
 <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist" >
       <thead>
       <tr>
         <th width="20">
          <input type="checkbox" name="toggle"
                 value="" onclick="checkAll('.$count.');"/>
          </th>
          <th align="left" width="2%">'.JText::_('C_ID').'</th>
          <th align="left" width="40">'.JText::_('C_GROUP').'</th>
          <th align="left" width="10%">'.JText::_('C_PROFILE').'</th>
          <th align="left" width="20%">'.JText::_('C_DESCRIPTION').'</th>
          <th align="left" width="15%">'.JText::_('C_FOLDER').'</th>
          <th align="left" width="10%">'.JText::_('C_UPLOAD_LIMIT').'</th>
          <th align="left" width="20%">'.JText::_('C_USERS').'</th>
          <th align="left" width="5%">'.JText::_('C_MASTER_PROFILE').'</th>
          <th width="5%">'.JText::_('C_ENABLED').'</th>
          <th width="10%">'.JText::_('C_DATE').'</th>          
         </tr>
         </thead>
';
        $i = 0;
        foreach ($rows as $row) {
           $evenodd = $i % 2;
           if ($row->maxfilesize == "") {
             $row->maxfilesize=JText::_('C_AUTO') . getMaximumUploadSize(); 
           }
echo <<< HTML
      <tr class="row$evenodd">
       <td>      
        <input type="checkbox" id="cb$row->id" name="cid[]"
               value="$row->id"
               onclick="isChecked(this.checked);" />
       </td>
         <td>
          <a href="#edit"
             onclick="return listItemTask('cb$row->id','edit')">
            $row->id</a>
        </td>
        <td>$row->gid&nbsp;</td>
        <td>
				 <a href="#edit"
				             onclick="return listItemTask('cb$row->id','edit')">
				            $row->config_name</a>
        </td>
        <td>$row->description&nbsp;</td>
        <td>$row->folder&nbsp;</td>
        <td>$row->maxfilesize KB</td>
        <td>$row->resize_label</td>
HTML;
           // master
           echo "<td align='center'>";
           if ($row->id != 1) {
           echo "<span style='cursor:pointer;' id='enableM".$row->id."'>";
           if ($row->master_profile == "true") {
              echo "<img onClick='disableMaster(".$row->id.")' src='images/tick.png' border='0' />";
           } else {
              echo "<img onClick='enableMaster(".$row->id.")' src='images/publish_x.png' border='0' />";
           }
           } else {
           echo '&nbsp;';
           }
           echo "</span>";
           echo "</td>";
           // enable
           echo "<td align='center'>";
           echo "<span style='cursor:pointer;' id='enableP".$row->id."'>";
           if ($row->enable_setting == "true") {
              echo "<img onClick='disableProfile(".$row->id.")' src='images/tick.png' border='0' />";
           } else {
              echo "<img onClick='enableProfile(".$row->id.")' src='images/publish_x.png' border='0' />";
           }
           echo "</span>";
           echo "</td>";
          
           echo "<td align='center'>".$row->last_modified_date."</td></tr>";
           $i++;
        }      

echo "</table><br>";

echo '
<h3 style="text-align:left;">'.JText::_('E_H3_GLOB').'</h3>
<table class="admintable">
     <thead> 
		<tr>
			<th width="20%">'.JText::_('E_H_SETTING').'</th>
			<th width="20%">'.JText::_('E_H_VALUE').'</th>
			<th width="60%">'.JText::_('E_H_DESCRIPTION').'</th>
		</tr>
     </thead> 
	 <tbody>
 	    <tr>
			<td class="key">'.JText::_('E_S_JFU_VERSION').'</td>
			<td>'.$vers.'</td>
			<td>'.$version_description.'</td>
		</tr>
 	    <tr>
			<td class="key">'.JText::_('E_S_JFU_SESSION').'</td>
			<td>'.$config->session_handler. (($config->session_handler == 'database') ? " &nbsp;<img src='../includes/js/ThemeOffice/warning.png' style='vertical-align:middle;width:16px' />" : " &nbsp;<img src='images/tick.png' style='vertical-align:middle;' />") .'</td>
			<td>'. (($config->session_handler == 'database') ? ('<span class="jfu_nocheck">' . JText::_('E_D_JFU_SESSION_DB') . '</span>') :  JText::_('E_D_JFU_SESSION_NONE')) .'</td>
		</tr>
    	<tr>
			<td class="key">'.JText::_('E_S_JFU_KEEP').'</td>
			<td>'.tfuHTML::truefalseRadioList('keep_tables',
			'class="inputbox"',$jfu_config['keep_tables']) . '</td>
			<td>'.JText::_('E_D_JFU_KEEP').'</td>
		</tr>
			<tr>
			<td class="key">'.JText::_('E_S_JFU_USE_JS_INCLUDE').'</td>
			<td>'.tfuHTML::truefalseRadioList('use_js_include',
			'class="inputbox"',$jfu_config['use_js_include']) . '</td>
			<td>'.JText::_('E_D_JFU_USE_JS_INCLUDE').'</td>
		</tr>
		<tr>
			<td class="key">'.JText::_('E_S_JFU_BACKEND_ACCESS_UPLOAD').'</td>
			<td>'.tfuHTML::showAdminSelectBox('backend_access_upload',
			'class="inputbox"',$jfu_config['backend_access_upload']) . '</td>
			<td>'.JText::_('E_D_JFU_BACKEND_ACCESS_UPLOAD').'</td>
		</tr>
			<tr>
			<td class="key">'.JText::_('E_S_JFU_BACKEND_ACCESS_CONFIG').'</td>
			<td>'.tfuHTML::showAdminSelectBox('backend_access_config',
			'class="inputbox"',$jfu_config['backend_access_config']) . '</td>
			<td>'.JText::_('E_D_JFU_BACKEND_ACCESS_CONFIG').'</td>
		</tr>
		<tr>
			<td class="key">'.JText::_('E_S_JFU_FILE_CHMOD').'</td>
			<td><input type="text" class="w50" maxsize="100"
				name="file_chmod" value="'.$jfu_config['$file_chmod'].'" /></td>
			<td>'.JText::_('E_D_JFU_FILE_CHMOD').'</td>
		</tr>
			<tr>
			<td class="key">'.JText::_('E_S_JFU_DIR_CHMOD').'</td>
			<td><input type="text" class="w50" maxsize="100"
				name="dir_chmod" value="'.$jfu_config['dir_chmod'].'" /></td>
			<td>'.JText::_('E_D_JFU_DIR_CHMOD').'</td>
		</tr>
		</tbody>
</table>		
';
echo <<< HTML
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="option" value="com_joomla_flash_uploader"/>
      <input type="hidden" name="boxchecked" value="0" />
  </form>
HTML;
}


function showConfig($row) {    
jimport('joomla.filter.output'); 
global $m;
JFilterOutput::objectHTMLSafe($row);
$folder_check_image = "tick";

// The image magick status
$im_status = check_image_magic($row->image_magic_path) ? '<p><img src="images/tick.png" style="vertical-align:middle;"" /> &nbsp;' .JText::_('E_D_USE_IMAGE_MAGIC_OK') . '</p>' : ' <p><img src="images/publish_x.png" style="vertical-align:middle;"" /> &nbsp;' .JText::_('E_D_USE_IMAGE_MAGIC_FAIL') . '</p>';
echo '
<script type="text/javascript" src="components/com_joomla_flash_uploader/jfu.js"></script>

<script type="text/javascript">
function checkValue(element, min, req) {
  var val = element.value;
  if (val == "" && req==1) {
     alert("'.JText::_('C_W_REQUIRED').'");
  } else if (!isNumeral(val)) {
     alert("'.JText::_('C_W_NUMBER').'");
  } else if (val < min) {
    alert("'.JText::_('C_W_SMALL').' " + min + ".");
  }
}

function checkUploadMaxValue(element) {
  var val = element.value;
  var max = '.getMaximumUploadSize().';
  if (val > max) {
    alert("'.JText::_('C_W_MAX').'");
  }
}
</script>

<form action="index2.php" method="post" name="adminForm">
<div class="config">
<table class="adminheading">
	<tbody>
		<tr>
			<th class="config">'.JText::_('E_HEADER').'</th>
		</tr>
	</tbody>
</table>
<p>
<div class="block-menu" >
<div id="form1h" class="form-block-menu-sel" onClick="showform(\'form1\')">
&nbsp;'.JText::_('E_HEADER_1').'&nbsp;</div>&nbsp;
<div  id="form2h" class="form-block-menu" onClick="showform(\'form2\')">
&nbsp;'.JText::_('E_HEADER_2').'&nbsp;</div>&nbsp;
<div  id="form3h" class="form-block-menu" onClick="showform(\'form3\')">
&nbsp;'.JText::_('E_HEADER_3').'&nbsp;</div>&nbsp;
</div>
</p>
<div id="form1">
<h3 style="text-align:left;">'.JText::_('E_H3_JOM').'</h3>

<table class="adminlist">
	
	 <thead>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<th width="20%">'.JText::_('E_H_SETTING').'</th>
			<th width="20%">'.JText::_('E_H_VALUE').'</th>
			<th width="60%">'.JText::_('E_H_DESCRIPTION').'</th>
		</tr>
		 </thead>
		 <tbody>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_SETTING').'</td>
			<td>'.tfuHTML::truefalseRadioList('enable_setting',
			'class="inputbox"', $row->enable_setting).'</td>
			<td>'.JText::_('E_D_ENABLE_SETTING').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ID').'</td>
			<td>'.$row->id.'</td>
			<td>'.JText::_('E_D_ID').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_GID').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="gid" value="'.$row->gid.'" /></td>
			<td>'.JText::_('E_D_GID').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_CONFIG_NAME').'</td>
			<td><input type="text" class="w250" maxsize="100"
				name="config_name" value="'.$row->config_name.'" /></td>
			<td>'.JText::_('E_D_CONFIG_NAME').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_CONFIG_DESCRIPTION').'</td>
			<td><input type="text" class="w250" maxsize="500"
				name="description" value="'.$row->description.'" /></td>
			<td>'.JText::_('E_D_CONFIG_DESCRIPTION').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_TEXT_TITLE') . '</td>
			<td>'.tfuHTML::truefalseRadioList('text_title_lang',
			'class="inputbox"', $row->text_title_lang, JText::_('E_S_USE_FILE'),
			JText::_('E_S_USE_TEXT')).'<br />
			<input type="text" class="w250" maxsize="100"
				name="text_title" value="'.$row->text_title.'" /></td>
			<td>'.JText::_('E_D_TEXT_TITLE') . " " . JText::_('E_D_TEXT') .'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_TEXT_BEFORE').'</td>
			<td>'.tfuHTML::truefalseRadioList('text_top_lang',
			'class="inputbox"', $row->text_top_lang, JText::_('E_S_USE_FILE'),
			JText::_('E_S_USE_TEXT')).'<br />
			<textarea rows="4" name="text_top" id="text_top" class="w250">'. $row->text_top .'</textarea></td>
			<td>'.JText::_('E_D_TEXT_BEFORE'). " " . JText::_('E_D_TEXT') .'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_TEXT_AFTER').'</td>
			<td>'.tfuHTML::truefalseRadioList('text_bottom_lang',
			'class="inputbox"', $row->text_bottom_lang, JText::_('E_S_USE_FILE'), JText::_('E_S_USE_TEXT') ).'<br />
			<textarea rows="4" name="text_bottom" id="text_bottom"
				class="w250">' . $row->text_bottom .'</textarea></td>
			<td>'.JText::_('E_D_TEXT_AFTER'). " " . JText::_('E_D_TEXT') .'</td>
		</tr>
		<tr'. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_FIXOVERLAY').'</td>
			<td>'.tfuHTML::truefalseRadioList('fix_overlay',
			'class="inputbox"', $row->fix_overlay).'</td>
			<td>'.JText::_('E_D_FIXOVERLAY').'</td>
		</tr>
	</tbody>
</table>
</div>
<div id="form2">
<h3 style="text-align:left;">'.JText::_('E_H3_SET').'</h3>
<table class="adminlist">
	<thead>
		<tr>
			<th width="20%">'.JText::_('E_H_SETTING').'</th>
			<th width="20%">'.JText::_('E_H_VALUE').'</th>
			<th width="60%">'.JText::_('E_H_DESCRIPTION').'</th>
		</tr>
		</thead>
		<tbody>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td><strong>'.JText::_('E_S_FOLDER').'</strong></td>
				<td><input onBlur="javascript:removeSpaces(this);" onKeyUp="javascript:testFolder();" type="text" class="w230" maxsize="100"
				name="folder" id="folder" value="'.$row->folder.'" />&nbsp;<img id="foldertestimage" height="16" src="images/'.$folder_check_image.'.png" border="0 "/></td>
			<td>'.JText::_('E_D_FOLDER').'</td>
		</tr>
';		
if 	($row->id != 1) {
echo '    	
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_MASTER_PROFILE').'</td>
			<td>'.tfuHTML::truefalseRadioList('master_profile',
			'class="inputbox"', $row->master_profile).'</td>
			<td>'.JText::_('E_D_MASTER_PROFILE').'</td>
		</tr>
			<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_MASTER_PROFILE_MODE').'</td>
			<td>'.tfuHTML::mastermodeRadioList('master_profile_mode',
			'class="inputbox"', $row->master_profile_mode).'</td>	
			<td>'.JText::_('E_D_MASTER_PROFILE_MODE').'</td>
		</tr>
			<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
		    <td>'.JText::_('E_S_MASTER_PROFILE_LOWERCASE').'</td>
			<td>'.tfuHTML::truefalseRadioList('master_profile_lowercase',
			'class="inputbox"', $row->master_profile_lowercase).'</td>	
		    <td>'.JText::_('E_D_MASTER_PROFILE_LOWERCASE').'</td>
		</tr>	
  ';
}
echo '
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_MAX_FILE_SIZE').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);checkValue(this, 0,0);checkUploadMaxValue(this);" type="text" class="w250" maxsize="100"
				name="maxfilesize" value="'.$row->maxfilesize.'" /></td>
			<td>'.JText::_('E_D_MAX_FILE_SIZE').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_WIDTH').'</td>
			<td><input onBlur="javascript:removeSpaces(this);checkValue(this, 100,1);" type="text" class="w250" maxsize="100"
				name="display_width" value="'.$row->display_width.'" /></td>
			<td>'.JText::_('E_D_WIDTH').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_RESIZE').'</td>
			<td>'.tfuHTML::truefalseRadioList('resize_show',
			'class="inputbox"', $row->resize_show).'</td>
			<td>'.JText::_('E_D_RESIZE').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_RESIZE_VALUES').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="resize_data" value="'.$row->resize_data.'" /></td>
			<td>'.JText::_('E_D_RESIZE_VALUES').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_RESIZE_LABELS').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="resize_label" value="'.$row->resize_label.'" /></td>
			<td>'.JText::_('E_D_RESIZE_LABELS').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_RESIZE_DEFAULT').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);checkValue(this, 0,0);" type="text" class="w250" maxsize="100"
				name="resize_default" value="'.$row->resize_default.'" /></td>
			<td>'.JText::_('E_D_RESIZE_DEFAULT').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ALLOWED_FILE_EXTENSIONS').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="allowed_file_extensions"
				value="'.$row->allowed_file_extensions.'" /></td>
			<td>'.JText::_('E_D_ALLOWED_FILE_EXTENSIONS').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_VORBIDDEN_FILE_EXTENSIONS').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="forbidden_file_extensions"
				value="'.$row->forbidden_file_extensions.'" /></td>
			<td>'.JText::_('E_D_VORBIDDEN_FILE_EXTENSIONS').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_HIDE_REMOTE_VIEW').'</td>
			<td>'.tfuHTML::truefalseRadioList('hide_remote_view',
			'class="inputbox"', $row->hide_remote_view).'</td>
			<td>'.JText::_('E_D_HIDE_REMOTE_VIEW').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_SHOW_DELETE').'</td>
			<td>'.tfuHTML::truefalseRadioList('show_delete',
			'class="inputbox"', $row->show_delete).'</td>
			<td>'.JText::_('E_D_SHOW_DELETE').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_FOLDER_BROWSING').'</td>
			<td>'.tfuHTML::truefalseRadioList('enable_folder_browsing',
			'class="inputbox"', $row->enable_folder_browsing).'</td>
			<td>'.JText::_('E_D_ENABLE_FOLDER_BROWSING').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_FOLDER_CREATION').'</td>
			<td>'.tfuHTML::truefalseRadioList('enable_folder_creation',
			'class="inputbox"', $row->enable_folder_creation).'</td>
			<td>'.JText::_('E_D_ENABLE_FOLDER_CREATION').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_FOLDER_DELETION').'</td>
			<td>'.tfuHTML::truefalseRadioList('enable_folder_deletion',
			'class="inputbox"', $row->enable_folder_deletion).'</td>
			<td>'.JText::_('E_D_ENABLE_FOLDER_DELETION').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_FOLDER_RENAME').'</td>
			<td>'.tfuHTML::truefalseRadioList('enable_folder_rename',
			'class="inputbox"', $row->enable_folder_rename).'</td>
			<td>'.JText::_('E_D_ENABLE_FOLDER_RENAME').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_FILE_RENAME').'</td>
			<td>'.tfuHTML::truefalseRadioList('enable_file_rename',
			'class="inputbox"', $row->enable_file_rename).'</td>
			<td>'.JText::_('E_D_ENABLE_FILE_RENAME').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_KEEP_FILE_EXTENSION').'</td>
			<td>'.tfuHTML::truefalseRadioList('keep_file_extension',
			'class="inputbox"', $row->keep_file_extension).'</td>
			<td>'.JText::_('E_D_KEEP_FILE_EXTENSION').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_SORT_FILES_BY_DATE').'</td>
			<td>'.tfuHTML::truefalseRadioList('sort_files_by_date',
			'class="inputbox"', $row->sort_files_by_date).'</td>
			<td>'.JText::_('E_D_SORT_FILES_BY_DATE').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_OVERWRITE_FILES').'</td>
			<td>'.tfuHTML::truefalseRadioList('overwrite_files',
			'class="inputbox"', $row->overwrite_files).'</td>
			<td>'.JText::_('E_D_OVERWRITE_FILES').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_WARNING_SETTING').'</td>
			<td>'.tfuHTML::warningRadioList('warning_setting',
			'class="inputbox"', $row->warning_setting).'</td>
			<td>'.JText::_('E_D_WARNING_SETTING').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_SHOW_SIZE').'</td>
			<td>'.tfuHTML::showSizeRadioList('show_size',
			'class="inputbox"', $row->show_size).'</td>
			<td>'.JText::_('E_D_SHOW_SIZE').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_HIDE_DIRECTORY_IN_TITLE').'</td>
			<td>'.tfuHTML::truefalseRadioList('hide_directory_in_title',
			'class="inputbox"', $row->hide_directory_in_title).'</td>
			<td>'.JText::_('E_D_HIDE_DIRECTORY_IN_TITLE').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_NORMALISE_FILE_NAMES').'</td>
			<td>'.tfuHTML::truefalseRadioList('normalise_file_names',
			'class="inputbox"', $row->normalise_file_names).'</td>
			<td>'.JText::_('E_D_NORMALISE_FILE_NAMES').'</td>
		</tr>
       	<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_NORMALISE_DIRECTORY_NAMES').'</td>
			<td>'.tfuHTML::truefalseRadioList('normalise_directory_names',
			'class="inputbox"', $row->normalise_directory_names).'</td>
			<td>'.JText::_('E_D_NORMALISE_DIRECTORY_NAMES').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_NORMALIZE_SPACES').'</td>
			<td>'.tfuHTML::truefalseRadioList('normalize_spaces',
			'class="inputbox"', $row->normalize_spaces).'</td>
			<td>'.JText::_('E_D_NORMALIZE_SPACES').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_UPLOAD_NOTIFICATION_EMAIL').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="upload_notification_email" value="'.$row->upload_notification_email.'" /></td>
			<td>'.JText::_('E_D_UPLOAD_NOTIFICATION_EMAIL').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_UPLOAD_NOTIFICATION_EMAIL_FROM').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="upload_notification_email_from" value="'.$row->upload_notification_email_from.'" /></td>
			<td>'.JText::_('E_D_UPLOAD_NOTIFICATION_EMAIL_FROM').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_UPLOAD_NOTIFICATION_EMAIL_SUBJECT').'</td>
			<td><input type="text" class="w250" maxsize="100"
				name="upload_notification_email_subject" value="'.$row->upload_notification_email_subject.'" /></td>
			<td>'.JText::_('E_D_UPLOAD_NOTIFICATION_EMAIL_SUBJECT').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_UPLOAD_NOTIFICATION_EMAIL_TEXT').'</td>
			<td><input type="text" class="w250" maxsize="100"
				name="upload_notification_email_text" value="'.$row->upload_notification_email_text.'" /></td>
			<td>'.JText::_('E_D_UPLOAD_NOTIFICATION_EMAIL_TEXT').'</td>
		</tr>		
        <!-- start new 2.8 -->
       	<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_LANGUAGE_DROPDOWN').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="language_dropdown" value="'.$row->language_dropdown.'" /></td>
			<td>'.JText::_('E_D_LANGUAGE_DROPDOWN').'</td>
		</tr>    
       	<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_USE_IMAGE_MAGIC').'</td>
			<td>'.tfuHTML::truefalseRadioList('use_image_magic',
			'class="inputbox"', $row->use_image_magic).'</td>
			<td>'.JText::_('E_D_USE_IMAGE_MAGIC'). $im_status . '</td>
		</tr>
        <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_IMAGE_MAGIC_PATH').'</td>
			<td><input type="text" class="w250" maxsize="100"
				name="image_magic_path" value="'.$row->image_magic_path.'" /></td>
			<td>'.JText::_('E_D_IMAGE_MAGIC_PATH').'</td>
		</tr>
		 <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_EXCLUDE_DIRECTORIES').'</td>
			<td><input type="text" class="w250" maxsize="100"
				name="exclude_directories" value="'.$row->exclude_directories.'" /></td>
			<td>'.JText::_('E_D_EXCLUDE_DIRECTORIES').'</td>
		</tr>
	    <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_FIX_UTF8').'</td>
			<td><input type="text" class="w250" maxsize="100"
				name="fix_utf8" value="'.$row->fix_utf8.'" /></td>
			<td>'.JText::_('E_D_FIX_UTF8').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_CREATION_DATE').'</td>
			<td>'.$row->creation_date.'</td>
			<td>'.JText::_('E_D_CREATION_DATE').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_LAST_MODIFIED_DATE').'</td>
			<td>'.$row->last_modified_date.'</td>
			<td>'.JText::_('E_D_LAST_MODIFIED_DATE').'</td>
		</tr>
</table>
</div>
'; 

echo '
<div id="form3">
<h3 style="text-align:left;">'.JText::_('E_H3_REG').'</h3>
';
if (!($m != "" && $m != "s" && $m !="w")) { 
  echo '<div class="redreg">'.JText::_('E_H_NOT_REG').'</div>';
}
echo ' 
<table class="adminlist">
	    <thead>
		<tr>
			<th width="20%">'.JText::_('E_H_SETTING').'</th>
			<th width="20%">'.JText::_('E_H_VALUE').'</th>
			<th width="60%">'.JText::_('E_H_DESCRIPTION').'</th>
		</tr>
		</thead>
		<tbody>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_FLASH_TITLE').'</td>
			<td><input type="text" class="w250" maxsize="100"
				name="flash_title" value="'.$row->flash_title.'" /></td>
			<td>'.JText::_('E_D_FLASH_TITLE').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_FILE_DOWNLOAD').'</td>
			<td>'.tfuHTML::downloadRadioList('enable_file_download',
			'class="inputbox"', $row->enable_file_download).'</td>
			<td>'.JText::_('E_D_ENABLE_FILE_DOWNLOAD').'</td>
		</tr>
		 <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_DIRECT_DOWNLOAD').'</td>
			<td>'.tfuHTML::truefalseRadioList('direct_download',
			'class="inputbox"', $row->direct_download).'</td>
			<td>'.JText::_('E_D_DIRECT_DOWNLOAD').'</td>
		</tr>   
		  <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_DOWNLOAD_MULTIPLE_FILES_AS_ZIP').'</td>
			<td>'.tfuHTML::truefalseRadioList('download_multiple_files_as_zip',
			'class="inputbox"', $row->download_multiple_files_as_zip).'</td>
			<td>'.JText::_('E_D_DOWNLOAD_MULTIPLE_FILES_AS_ZIP').'</td>
		</tr>   	
    	<!-- /new 2.7 -->
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_DIRECTORY_FILE_LIMIT').'</td>
    	<td><input  onBlur="javascript:removeSpaces(this);checkValue(this, 0,0);" type="text" class="w250" maxsize="100"
				name="directory_file_limit" value="'.$row->directory_file_limit.'" /></td>
			<td>'.JText::_('E_D_DIRECTORY_FILE_LIMIT').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_QUEUE_FILE_LIMIT').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);checkValue(this, 0,0);" type="text" class="w250" maxsize="100"
				name="queue_file_limit" value="'.$row->queue_file_limit.'" /></td>
			<td>'.JText::_('E_D_QUEUE_FILE_LIMIT').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_QUEUE_FILE_LIMIT_SIZE').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);checkValue(this, 0,0);" type="text" class="w250" maxsize="100"
				name="queue_file_limit_size" value="'.$row->queue_file_limit_size.'" /></td>
			<td>'.JText::_('E_D_QUEUE_FILE_LIMIT_SIZE').'</td>
		</tr>
		
        <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_PREVIEW_TEXTFILE_EXTENSIONS').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="preview_textfile_extensions" value="'.$row->preview_textfile_extensions.'" /></td>
			<td>'.JText::_('E_D_PREVIEW_TEXTFILE_EXTENSIONS').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_EDIT_TEXTFILE_EXTENSIONS').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="edit_textfile_extensions" value="'.$row->edit_textfile_extensions.'" /></td>
			<td>'.JText::_('E_D_EDIT_TEXTFILE_EXTENSIONS').'</td>
		</tr>

		 <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ALLOWED_VIEW_FILE_EXTENSIONS').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="allowed_view_file_extensions" value="'.$row->allowed_view_file_extensions.'" /></td>
			<td>'.JText::_('E_D_ALLOWED_VIEW_FILE_EXTENSIONS').'</td>
		</tr>
		 <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_FORBIDDEN_VIEW_FILE_EXTENSIONS').'</td>
			<td><input onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="forbidden_view_file_extensions" value="'.$row->forbidden_view_file_extensions.'" /></td>
			<td>'.JText::_('E_D_FORBIDDEN_VIEW_FILE_EXTENSIONS').'</td>
		</tr>
		  <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_SHOW_FULL_URL_FOR_SELECTED_FILE').'</td>
			<td>'.tfuHTML::truefalseRadioList('show_full_url_for_selected_file',
			'class="inputbox"', $row->show_full_url_for_selected_file).'</td>
			<td>'.JText::_('E_D_SHOW_FULL_URL_FOR_SELECTED_FILE').'</td>
		</tr>   	
			<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_UPLOAD_FINISHED_JS_URL').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="upload_finished_js_url" value="'.$row->upload_finished_js_url.'" /></td>
			<td>'.JText::_('E_D_UPLOAD_FINISHED_JS_URL').' ' . JText::_("E_D_JS_TEXT").  '</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_PREVIEW_SELECT_JS_URL').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="preview_select_js_url" value="'.$row->preview_select_js_url.'" /></td>
			<td>'.JText::_('E_D_PREVIEW_SELECT_JS_URL').' ' . JText::_("E_D_JS_TEXT"). '</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_DELETE_JS_URL').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="delete_js_url" value="'.$row->delete_js_url.'" /></td>
			<td>'.JText::_('E_D_DELETE_JS_URL').' ' . JText::_("E_D_JS_TEXT"). '</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_JS_CHANGE_FOLDER').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="js_change_folder" value="'.$row->js_change_folder.'" /></td>
			<td>'.JText::_('E_D_JS_CHANGE_FOLDER').' ' . JText::_("E_D_JS_TEXT"). '</td>
		</tr>
		<!-- new 2.7 -->
			<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_JS_CREATE_FOLDER').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="js_create_folder" value="'.$row->js_create_folder.'" /></td>
			<td>'.JText::_('E_D_JS_CREATE_FOLDER').' ' . JText::_("E_D_JS_TEXT"). '</td>
		</tr>
			<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_JS_RENAME_FOLDER').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="js_rename_folder" value="'.$row->js_rename_folder.'" /></td>
			<td>'.JText::_('E_D_JS_RENAME_FOLDER').' ' . JText::_("E_D_JS_TEXT"). '</td>
		</tr>
			<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_JS_DELETE_FOLDER').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="js_delete_folder" value="'.$row->js_delete_folder.'" /></td>
			<td>'.JText::_('E_D_JS_DELETE_FOLDER').' ' . JText::_("E_D_JS_TEXT"). '</td>
		</tr>
			<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_JS_COPYMOVE').'</td>
			<td><input  onBlur="javascript:removeSpaces(this);" type="text" class="w250" maxsize="100"
				name="js_copymove" value="'.$row->js_copymove.'" /></td>
			<td>'.JText::_('E_D_JS_COPYMOVE').' ' . JText::_("E_D_JS_TEXT").'</td>
		</tr>
 </tbody>
 </table>		
	 <h3 style="text-align:left;">'.JText::_('E_H3_REG_PROF').'</h3>
      <table class="adminlist">
        <thead>
	      <tr>
			<th width="20%">'.JText::_('E_H_SETTING').'</th>
			<th width="20%">'.JText::_('E_H_VALUE').'</th>
			<th width="60%">'.JText::_('E_H_DESCRIPTION').'</th>
		  </tr>	
	    </thead>
	    <tbody>
          <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_FOLDER_MOVECOPY').'</td>
			<td>'.tfuHTML::truefalseRadioList('enable_folder_movecopy',
			'class="inputbox"', $row->enable_folder_movecopy).'</td>
			<td>'.JText::_('E_D_ENABLE_FOLDER_MOVECOPY').'</td>
		</tr>
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_ENABLE_FILE_MOVECOPY').'</td>
			<td>'.tfuHTML::truefalseRadioList('enable_file_movecopy',
			'class="inputbox"', $row->enable_file_movecopy).'</td>
			<td>'.JText::_('E_D_ENABLE_FILE_MOVECOPY').'</td>
		</tr>
        <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_DESCRIPTION_MODE').'</td>
			<td>'.tfuHTML::truefalseRadioList('description_mode',
			'class="inputbox"', $row->description_mode).'</td>
			<td>'.JText::_('E_D_DESCRIPTION_MODE').'</td>
		</tr> 
         <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_DESCRIPTION_MODE_SHOW_DEFAULT').'</td>
			<td>'.tfuHTML::truefalseRadioList('description_mode_show_default',
			'class="inputbox"', $row->description_mode_show_default).'</td>
			<td>'.JText::_('E_D_DESCRIPTION_MODE_SHOW_DEFAULT').'</td>
		</tr>   
         <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_DESCRIPTION_MODE_MANDATORY').'</td>
			<td>'.tfuHTML::truefalseRadioList('description_mode_mandatory',
			'class="inputbox"', $row->description_mode_mandatory).'</td>
			<td>'.JText::_('E_D_DESCRIPTION_MODE_MANDATORY').'</td>
		</tr>   	
         <tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_DESCRIPTION_MODE_STORE').'</td>
			<td>'.tfuHTML::modeRadioList('description_mode_store',
			'class="inputbox"', $row->description_mode_store).'</td>
			<td>'.JText::_('E_D_DESCRIPTION_MODE_STORE').'</td>
		</tr>      
          	
		<tr '. HTML_joomla_flash_uploader::getRowClass() .'>
			<td>'.JText::_('E_S_SWF_TEXT').'</td>
			<td><textarea rows="4" name="swf_text" id="swf_text"
				class="w250">'. $row->swf_text .'</textarea></td>
			<td>'.JText::_('E_D_SWF_TEXT').'</td>
		</tr>
	</tbody>
</table>
</div>
</div>
';
echo <<< HTML
    <input type="hidden" name="id" value="$row->id" />
    <input type="hidden" name="creation_date" value="$row->creation_date" />
	<input type="hidden" name="option" value="com_joomla_flash_uploader" />
	<input type="hidden" name="task" value="saveConfig" />
</form>
<script type="text/javascript">
showform('form1');
testFolder();
</script>
HTML;
}


function listUsers($rows, $data) {
echo '
<form action="index2.php" method="post" name="adminForm">
	<h2>'.JText::_('U_TITLE').'</h2>
	<p>'.JText::_('U_TEXT').'</p>
	<div style="width:540px;float:left;">
	<h3 style="text-align:left;">'.JText::_('U_MAPPINGS').'</h3>     
  <table cellpadding="4" cellspacing="0" border="0" class="adminlist">
    <thead> 
       <tr>
          <th align="left" width="250">'.JText::_('U_PROFILE').'</th>
          <th align="left" width="250">'.JText::_('U_USER').'</th>
          <th align="left" width="40">'.JText::_('U_DELETE').'</th> 
       </tr>
    </thead>
';
        $i = 0;
        if (count($rows)==0) {
         echo '<tr class="row0">';
         echo '<td colspan="4"><center>'.JText::_('U_NO_MAPPINGS').'</center></td></tr>';
        } else {
        foreach ($rows as $row) {
           $evenodd = $i % 2;
echo <<< HTML
      <tr class="row$evenodd">
        
        <td>$row->config_name </td>
        <td>$row->username </td>
        <td style="text-align:center;">
        <!-- I stick to the joomla way - therefore not very nice here ... -->
        <input style="display:none" type="checkbox" id="cb$row->myid" name="cid[]"
		               value="$row->myid"
               onclick="isChecked(this.checked);" />
				 <a href="#deleteuser"
				             onclick="return listItemTask('cb$row->myid','deleteUser')">
				             <img src="images/publish_x.png" border="0" /></a>
        </td></tr>
HTML;
           $i++;
          }
        }
        
        $us = $data["users"];
        $pr = $data["profiles"];
echo '
      </table> 
      </div>
      <div style="clear:both"></div>
      <div style="width:540px;float:left;">
      	<h3 style="text-align:left;">'.JText::_('U_ADD_USER_TITLE').'</h3>
        <table cellpadding="4" cellspacing="0" border="0" class="adminlist">
          <thead> 
            <tr>
			   <th align="left" width="250">'.JText::_('U_SEL_PROFILE').'</th>
			   <th align="left" width="230">'.JText::_('U_SEL_USER').'</th>
			   <th align="left" width="40"> </th> 
            </tr>
          </thead> 
      <tr>
         <td style="vertical-align:top;">'.$pr.'</td>
         <td style="vertical-align:top;">'.$us.'</td>
         <td style="vertical-align:top;text-align:center;"><a href="#adduser" onclick="return submitform(\'addUser\')"><b>'.JText::_('U_ADD_USER').'</b></a></td>
      </tr>
    </table>
    <br/>
    </div>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="option" value="com_joomla_flash_uploader"/>
      <input type="hidden" name="boxchecked" value="0" />
  </form>
';
}

function showHelpRegister() {
global $m;
echo '

<style>
.install {
	margin-left: 5px;
	margin-right: 5px;
	margin-top: 10px;
	margin-bottom: 10px;
	padding: 10px;
	text-align:left;
	border: 1px solid #cccccc;
	width:720px;
	background: #F1F1F1;
}

.h3_help {
text-align:left;
border-bottom: 2px solid #DDDDDD;
}
</style>
<form action="index2.php" method="post" name="adminForm">
<h2>'.JText::_('H_TITLE').'</h2>
	<h3 class="h3_help">'.JText::_('H_H3_HELP').'</h3> 
	'.JText::_('H_H_TEXT').'
	<div style="text-align:left;float:left;">
	<ul>
		<li>'.JText::_('H_H_OVERVIEW').'</li>
		<li>'.JText::_('H_H_HELP').'</li>
		<li>'.JText::_('H_H_TWG').'</li>
		<li>'.JText::_('H_H_FORUM').'</li>
		<li>'.JText::_('H_H_CONFIG').'</li>
		<li>'.JText::_('H_H_MAMBOT').'</li>
		<li>'.JText::_('H_H_REG').'</li>
	</ul>
	</div>
	<h3 class="h3_help">'.JText::_('H_L_TITLE').'</h3>
	<div style="text-align:left;float:left;">	
	  '.JText::_('H_L_TEXT').'  
	  <div class="install" style="width:600px;margin-left:50px;">
	  <b>'.JText::_('H_L_INFOS').'</b><p> 
';
$limit = return_kbytes(ini_get('memory_limit'));

echo JText::_('H_L_NAME') . " " . $_SERVER['SERVER_NAME'] . "<br>";
	echo JText::_('H_L_LIMIT') ." " . getMaximumUploadSize(). "<br>"; 
	echo JText::_('H_L_MEMORY') . " " . $limit . " <br>"; 	
  echo JText::_('H_L_RESOLUTION') ." ";
  	if (!$limit) {
		  echo  '<font color="green">No limit</font>';
	  } else {
	    $xy = $limit * 1024 / 6;
	    $x = floor( sqrt ($xy / 0.75));
	    $y = floor( sqrt($xy / 1.33));
	    
	    if ($x > 4000) {
	      echo "<font color='green'>~ " . $x . " x " . $y . "</font>"; 
      } else if ($x > 2000) {
        echo "<font color='orange'>~ " . $x . " x " . $y . "</font>"; 
      } else {
        echo "<font color='red'>~ " . $x . " x " . $y . "</font>"; 
      }   
	  }	
    echo "<br>";
	echo JText::_('H_L_INPUT') . " " . ini_get('max_input_time') . " s<br>"; 	
	echo JText::_('H_L_EXECUT') . " " . ini_get('max_execution_time') . " s<br>"; 
	echo JText::_('H_L_SOCKET') . " " . ini_get('default_socket_timeout') . " s";
    if (substr(@php_uname(), 0, 7) != "Windows"){  
        echo '<p>' . JText::_('H_L_CHMOD1') . ' ' . substr(sprintf('%o', fileperms(dirname(__FILE__) . "/tfu/tfu_config.php")), -4) ;  
        echo '<br>' . JText::_('H_L_CHMOD2');
        echo '</p><p>
        <button onclick="this.form.task.value=\'chmod755\';this.form.submit();">'.JText::_('H_L_CHMOD755').'</button> 
        <button onclick="this.form.task.value=\'chmod644\';this.form.submit();">'.JText::_('H_L_CHMOD644').'</button> 
        <button onclick="this.form.task.value=\'chmod666\';this.form.submit();">'.JText::_('H_L_CHMOD666').'</button> 
        <button onclick="this.form.task.value=\'chmod777\';this.form.submit();">'.JText::_('H_L_CHMOD777').'</button> 
        </p>
        ';
    }
    
echo '
	  </p>
	  </div>
	</div>
	
	<h3 class="h3_help">'.JText::_('H_R_TITLE').'</h3>
	<div style="text-align:left;float:left;">
';
if ($m == "") {
echo JText::_('H_R_TEXT') .'<ul>
  <li>'.JText::_('H_R_FREEWARE').'</li
  <li>'.JText::_('H_R_REG').'</li></ul>
	  <div class="install" style="width:600px;margin-left:50px;">'.JText::_('H_R_BONUS').'</div>';
printf(JText::_('H_R_REG_10'), "<a href=\"http://www.tinywebgallery.com/en/register_tfu.php\"><b>", "</b></a>");	  
echo '<p>'.JText::_('H_R_REG_HOWTO').'</p>
<div class="install" style="width:600px;margin-left:50px;">
&lt;?php
<table><tr><td>
$l</td><td>=" <input type="text" name="l" size=80> ";</td></tr><tr><td>
$d</td><td>=" <input type="text" name="d" size=80> ";</td></tr><tr><td>
$s</td><td>=" <input type="text" name="s" size=80> ";</td></tr></table>
?&gt;
<p>
<input type="hidden" name="task" value="register" />
<button onclick="this.form.submit();">'.JText::_('H_R_REGISTER').'</button>
</p>
</div>
';
} else if ($m != "" && $m != "s" && $m !="w" ) {
include  dirname(__FILE__) . "/tfu/twg.lic.php";
echo JText::_('H_R_REG_TO') . " <b>$l</b>";
if ($l == $d) {
  echo " (Enterprise Edition License)";
} else if (strpos($d, "TWG_PROFESSIONAL") !== false) {
  echo " (Professional Edition License)";
} else if (strpos($d, "TWG_SOURCE") !== false) {
  echo " (Source code Edition License)";
} else {
  echo " (Standart Edition License)";
}
echo "<p>" . JText::_('H_R_REG_DEL');
echo '
<input type="hidden" name="task" value="dellic" />
<button onclick="this.form.submit();">'.JText::_('H_R_UNREGISTER').'</button>
</p>';
} else {
echo "<p>" . JText::_('H_R_REG_WRONG');
echo '
<input type="hidden" name="task" value="dellic" />
<button onclick="this.form.submit();">'.JText::_('H_R_UNREGISTER').'</button>
</p>';
}
echo <<< HTML
</div>
	      <input type="hidden" name="option" value="com_joomla_flash_uploader"/>
	      <input type="hidden" name="boxchecked" value="0" />
  </form>
HTML;
}
}
?>