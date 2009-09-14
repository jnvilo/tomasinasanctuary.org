<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
    $document =& JFactory::getDocument();
    $url = JURI::base().'modules/mod_artcats/tmpl/toggle.js';
    $document->addScript($url);
    $url = JURI::base().'modules/mod_artcats/tmpl/css/sitemap/'.$artcats->getSitemapTemplate();
    $document->addStyleSheet($url);
    $document->addScriptDeclaration($artcats->getExpandableJavaScript());
?>

<?php if ($artcats->is_subcategories_off()) : ?>
    <ul id="artcats_sitemap" class="artcats">
        <li><a id="<?php echo $artcats->getExpandableID(); ?>ExpandAll" class="expandAll"  class="toggle" href="#">Expand All</a></li>
        <li><a id="<?php echo $artcats->getExpandableID(); ?>CollapseAll" class="collapseAll" class="toggle" href="#">Collapse All</a></li>           
    <?php foreach($list['sections'] as $sectionid => $section) : ?>
        <li class="sitemap">
            <img src="modules/mod_artcats/tmpl/img/contract.gif" class="toggle" />
            <a href="<?php echo $section->link; ?>" class=""><?php echo $section->text;?></a>
            <ul class="contract">
                <?php foreach ($list['categories'][$sectionid] as $category) : ?>
                <li class="sitemap">
                    <img src="modules/mod_artcats/tmpl/img/contract.gif" class="toggle" />
                    <a href="<?php echo $category->link; ?>" class=""><?php echo $category->text, ' ', $category->count;?></a>
                    <ul class="contract">
                        <?php foreach ($category->articles as $article) : ?>
                        <li><a href="<?php echo $article->link; ?>"><?php echo $article->text; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>     
                <?php endforeach; ?>
            </ul>
    <?php endforeach; ?>
    </ul>
<?php else : ?>
    <?php echo $artcats->getDisplayableSitemap($list); ?>
<?php endif; ?>
