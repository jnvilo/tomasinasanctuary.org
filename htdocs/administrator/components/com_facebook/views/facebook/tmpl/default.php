<?php
/**
 * Facebook Template
 *
 * 
 * @version		$Id: default.php 36 2008-03-12 02:21:01Z chalet16 $
 * @package 	Joomla-Facebook
 * @link 		http://joomlacode.org/gf/project/joomla-facebook/
 * @license		GNU/GPL, see LICENSE.php
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<h2><?php echo JText::_('ADMINVIEWFACEBOOKSUCCESS'); ?></h2>
<h3><?php echo JText::_('ADMINVIEWFACEBOOKHOWTO'); ?></h3>
<b><?php echo JText::_('Module'); ?>:</b><br/>
<ul>
<?php 
if($this->showlist['module']) {
foreach($this->showlist['module'] as $ciddata) {
echo '<li><b><a href="'.JRoute::_('index.php?option=com_modules&client='.$ciddata[0]['client_id'].'&task=edit&cid[]='.$ciddata[0]['id']).'">'
		.$ciddata['manifestdata']['readname'].'</a></b><br/>'.$ciddata['manifestdata']['description'].'</li>';
}
}
?>
</ul>
<b><?php echo JText::_('Template'); ?></b><br/>
<ul>
<?php 
if($this->showlist['template']) {
foreach($this->showlist['template'] as $ciddata) {
echo '<li><b><a href="'.JRoute::_('index.php?option=com_templates&client='.$ciddata[0]['client_id'].'&task=edit&cid[]='.$ciddata[0]['id']).'">'
		.$ciddata['manifestdata']['readname'].'</a></b><br/>'.$ciddata['manifestdata']['description'].'</li>';
}
}
?>
</ul>
<b><?php echo JText::_('Plugin'); ?>:</b><br/>
<ul>
<?php 
if($this->showlist['plugin']) {
foreach($this->showlist['plugin'] as $ciddata) {
echo '<li><b><a href="'.JRoute::_('index.php?option=com_plugins&client='.$ciddata[0]['client_id'].'&task=edit&cid[]='.$ciddata[0]['id']).'">'
		.$ciddata['manifestdata']['readname'].'</a></b><br/>'.$ciddata['manifestdata']['description'].'</li>';
}
}
?>
</ul>
<input type="hidden" name="option" value="com_facebook" /> 
<input type="hidden" name="task" value="" />
</form>