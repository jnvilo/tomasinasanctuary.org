<?php // no direct access
/**
* @package RokNewsFlash
* @copyright Copyright (C) 2007 RocketWerx. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
defined('_JEXEC') or die('Restricted access'); 
?>
<script type="text/javascript">
	window.addEvent('domready', function() {
		var x = new RokNewsFlash('newsflash', {
			controls: <?php echo $params->get('controls')==1?"true":"false"; ?>,
			delay: <?php echo $params->get('delay'); ?>,
			duration: <?php echo $params->get('duration'); ?>,
			blankimage: '<?php echo JURI::base().'images/blank.png'; ?>'
		});
	});
</script>
<div id="newsflash" class="roknewsflash<?php echo $params->get('moduleclass_sfx'); ?>">
    <span class="flashing"><?php echo $params->get('pretext'); ?></span>
    <ul>
<?php foreach ($list as $item) :  ?>
		<li>
		    <a href="<?php echo $item->link; ?>">
		    <?php
		    if ($params->get('usetitle')==1) {
		        echo ($item->title);
		    } else {
		        echo ($item->introtext . '...');
		    }
		    ?>
  		    </a>
		</li>
<?php endforeach; ?>
    </ul>
</div>