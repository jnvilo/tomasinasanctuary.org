<?php
defined('_JEXEC') or die('Restricted access'); 
?>
<div style="width: <?php echo $params->get('preview_width', '220'); ?>px;margin: 0 auto;">
	<img src="images/blank.png" name="preview" id="variation_preview" border="0" width="<?php echo $params->get('preview_width', '270'); ?>" height="<?php echo $params->get('preview_height', '227'); ?>" alt="Mixxmag" />

	<form action="index.php" name="chooserform" method="get" class="variation-chooser">

	<div class="controls">
		<img class="control-prev" id="variation_chooser_prev" title="Previous" alt="prev" src="images/blank.png" style="background-image: url(templates/rt_solarsentinel_j15/images/showcase-controls.png);" />
		<select name="tstyle" id="variation_chooser" class="button" style="float: left;">
			<?php
			for ($x=1;$x<=10;$x++) {
				echo "<option value=\"style$x\"" . getSelected('style'. $x) .">Style $x</option>\n";
			}
			?>
		</select>
		<img class="control-next" id="variation_chooser_next" title="Next" alt="next" src="images/blank.png" style="background-image: url(templates/rt_solarsentinel_j15/images/showcase-controls.png);"/>
	</div>
	<input class="button" type="submit" value="Select" />
	</form>
</div>
