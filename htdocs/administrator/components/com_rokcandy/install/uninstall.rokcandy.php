<?php
/**
 * @version $Id: uninstall.rokcandy.php 5682 2008-08-18 05:39:07Z wonderslug $
 * @package RocketWerx
 * @subpackage	RokBrdige
 * @copyright Copyright (C) 2008 RocketWerx. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
 // no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');	
 
class Status {
	var $STATUS_FAIL = 'Failed';
	var $STATUS_SUCCESS = 'Success';
	var $infomsg = array();
	var $errmsg = array();
	var $status;
}

$rok_database = JFactory::getDBO();
$rok_install_status = array();

require "install_plugins.php";

$plg_return = rok_plugin_uninstall(&$this, "rokcandy_system", "system", $rok_database);
$rok_install_status['RokCandy System Plugin'] = $plg_return;
if ($plg_return->status == $plg_return->STATUS_FAIL) {
    var_dump ($plg_return);
}

$plg_return = rok_plugin_uninstall(&$this, "rokcandy_button", "editors-xtd", $rok_database);
$rok_install_status['RokCandy Editor XTD Plugin'] = $plg_return;
if ($plg_return->status == $plg_return->STATUS_FAIL) {
    var_dump ($plg_return);
}


function com_uninstall()
{
	echo( "RokCandy has been successfully uninstalled." );
}

?>
<h1>RokCandy Uninstallation</h1>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title"><?php echo JText::_('Element'); ?></th>
			<th width="60%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
		$i=0;
		foreach ( $rok_install_status as $component => $status ) {?>
		<tr class="row<?php echo $i; ?>">
			<td class="key"><?php echo $component; ?></td>
			<td>
				<?php echo ($status->status == $status->STATUS_SUCCESS)? '<strong>'.JText::_('Uninstalled').'</strong>' : '<em>'.JText::_('NOT Uninstalled').'</em>'?>
				<?php if (count($status->errmsg) > 0 ) {
						foreach ( $status->errmsg as $errmsg ) {
       						echo '<br/>Error: ' . $errmsg;
						}
				} ?>
				<?php if (count($status->infomsg) > 0 ) {
						foreach ( $status->infomsg as $infomsg ) {
       						echo '<br/>Info: ' . $infomsg;
						}
				} ?>
			</td>
		</tr>	
	<?php
			if ($i=0){ $i=1;} else {$i = 0;}; 
		}?>
		
	</tbody>
</table>
