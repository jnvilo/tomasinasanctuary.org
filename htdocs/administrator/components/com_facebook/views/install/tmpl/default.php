<?php
/**
 * Install Template
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
<h3><?php echo JText::_('NEXTTOINSTALL'); ?></h3>
<br/><br/>
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'LEGEND' ); ?></legend>
		<table class="admintable">
<?php 
foreach($this->showlist as  $data) {
?>		
		<tr>
			<td>
				<input type="checkbox" name="install-<?php echo $data['name']; ?>"  size="32" maxlength="250" value="1" <?php if($data['installed']||$data['require']) {echo 'checked';} ?> <?php if($data['require']) { echo 'disabled="disabled"'; } ?>/>
			</td>
			<td>
				<b><?php echo ucfirst($data['type']).': '.$data['readname'].' '.$data['version']; ?></b> (<?php echo $data['name']; ?>)  <br/>
				<?php echo $data['description']; ?> <?php if($data['require']) { echo '<b>REQUIRE!</b>'; } ?><br/> 
			</td>
		</tr>
<?php 
}
?>		
	</table>
	</fieldset>
</div>
<input type="hidden" name="option" value="com_facebook" />
<input type="hidden" name="task" value="" />
</form>
