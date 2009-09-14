<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="roknewspager-wrapper">
	<ul class="roknewspager">
	<?php foreach ($list as $item) :  ?>
	    <li>
	        <div class="roknewspager-div">
	            <a href="<?php echo $item->link; ?>" class="roknewspager-title"><?php echo $item->title; ?></a>
	            <?php echo $item->introtext; ?>
	        </div>
	    </li>
	<?php endforeach; ?>
	</ul>
</div>
<?php
	$disabled = ($pages == 1) ? " style='display: none;'" : '';
?>
<div class="roknewspager-pages" <?php echo $disabled; ?>>
	<div class="roknewspager-spinner"></div>
    <div class="roknewspager-pages2">
        <div class="roknewspager-prev"></div>
        <div class="roknewspager-next"></div>
        <ul class="roknewspager-numbers">
            <?php for($x=1;$x<=$pages && $x < ($params->get('maxpages',8)+1);$x++):?>
            <li <?php if($x==$curpage):?>class="active"<?php endif; ?>><?php echo $x; ?></li>
            <?php endfor;?>
        </ul>
    </div>
</div>
<?php
	$autoupdate = ($params->get('autoupdate', false)) ? 1 : 0;
	$autoupdate_delay = $params->get('autoupdate_delay', 5000);
	if ($params->get('module_ident','name')=='name') {
	    $url = JURI::Base() . "index.php?option=com_rokmodule&amp;tmpl=component&amp;type=raw&amp;module=$module_name&amp;offset=";
	} else {
	    $url = JURI::Base() . "index.php?option=com_rokmodule&amp;tmpl=component&amp;type=raw&amp;moduleid=$module_id&amp;offset=";
    }
?>
<script type="text/javascript">
	RokNewsPagerStorage.push({
		'url': '<?php echo $url; ?>',
		'autoupdate': <?php echo $autoupdate; ?>, 
		'delay': <?php echo $autoupdate_delay; ?>
	});
</script>