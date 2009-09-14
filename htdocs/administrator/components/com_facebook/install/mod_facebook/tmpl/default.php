<?php 
/**
 * mod_facebook Template
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
defined( '_JEXEC' ) or die();
?>

<a href="http://www.facebook.com/login.php?api_key=<?php echo $apikey; ?>&v=1.0">
<?php if($showimg) {
echo '<img src="'.$imgpath.'" border=0 />';
} ?>
<?php echo JText::_('SITELOGIN'); ?>
</a>
<br/>
<?php if($showadmin) { ?>
<a href="http://www.facebook.com/login.php?api_key=<?php echo $apikey; ?>&v=1.0&next=%26redirect%3Dadmin">
<?php if($showimg) {
echo '<img src="'.$imgpath.'" border=0 />';
} ?>
<?php echo JText::_('ADMINLOGIN'); ?>
</a>
<?php } ?>

