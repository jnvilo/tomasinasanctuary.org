<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
	RokMiN.settings['section-<?php echo $params->get('secid'); ?>-<?php echo $params->get('catid'); ?>'] = {'mousetype': '<?php echo $params->get('mouse_type', 'click'); ?>'};
</script>
<div class="block-1 micronews <?php echo $params->get('cssclass'); ?>" id="section-<?php echo $params->get('secid'); ?>-<?php echo $params->get('catid'); ?>">
	<div class="corner-tl">
		<div class="corner-tr">
			<div class="corner-bl">
				<div class="corner-br">
					<div class="micronews-headline">
						<?php if ($params->get('show_controls',1) == 1) :?>
						<div class="micronews-controls"><div class="micronews-controls2">
							<div class="mover"><span><?php echo JText::_('Move'); ?></span></div>
							<div class="collapse"><span style="display:none;">-</span></div>
						</div></div>
						<?php endif; ?>
						<h2><?php echo $module->title; ?></h2>
					</div>
					<div class="micronews-inner">
						<div class="micronews-<?php echo $params->get('navigation_position', 'right'); ?>">
							<div class="micronews-wrapper">
								<div class="micronews-titles">
									<div class="top-story"><?php echo JText::_('Top Article'); ?></div>
									<div class="other-stories"><?php echo JText::_('Other Articles'); ?></div>
								</div>
								<div class="micronews-article-wrapper">		
									<div class="micronews-articles">
									<?php for ($i=0; $i<sizeof($list); $i++) : ?>
		    							<div class="entry article-<?php $list[$i]->id; ?>">
						                    <?php if ($params->get('show_title', 0)): ?>
												<h4><?php echo $list[$i]->title; ?></h4>
											<?php endif; ?>
						                    <div class="micronews-article">
		    		    						<?php if ($list[$i]->thumb != false && $params->get('image',1) == 1) :?>
												<?php
												$sizes = getimagesize($list[$i]->thumb);
												$sizes = ($sizes[3]) ? $sizes[3] : '';
												?>
		        								<img <?php echo $sizes; ?> src="<?php echo $list[$i]->thumb; ?>" alt="<?php echo $list[$i]->title; ?>" class="micronews-thumb" />	
		        								<?php endif; ?>
		        								<?php if ($list[$i]->introtext != false) :?>
		        								<p><?php echo ($list[$i]->introtext); ?></p>
		        								<a href="<?php echo $list[$i]->link; ?>" class="readon">Read More...</a>
		        								<?php endif; ?>
		    								</div>
		    							</div>
									<?php endfor; ?>
									</div>
									<div class="micronews-list">
										<ul>
										<?php for ($i=0; $i<sizeof($list); $i++) : ?>
			    							<li><a href="<?php echo $list[$i]->link; ?>"><span><?php echo $list[$i]->title; ?></span></a></li>
										<?php endfor; ?>
										</ul>
									</div>
									<div class="clr"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="micronews-bottom"></div>
				</div>
			</div>
		</div>
	</div>
</div>
