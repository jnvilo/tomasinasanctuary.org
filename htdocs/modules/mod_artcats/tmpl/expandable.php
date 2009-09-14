<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
    $document =& JFactory::getDocument();
    $url = JURI::base().'modules/mod_artcats/tmpl/toggle.js';
    $document->addScript($url);
    $url = JURI::base().'modules/mod_artcats/tmpl/css/expandable/'.$artcats->getExpandableTemplate();
    $document->addStyleSheet($url);
    $document->addScriptDeclaration($artcats->getExpandableJavaScript());
?>
<?php if (($artcats->is_on_category_page() or $artcats->is_on_article_page()) and $artcats->is_both_mode()) : ?>
    <h3><?php echo $params->get('articles_mode_title'); ?></h3>
<?php endif; ?>
<?php if ($artcats->is_on_section_page() and $artcats->is_both_mode()) : ?>
    <h3><?php echo $params->get('categories_mode_title'); ?></h3>
<?php endif; ?>

<?php if ($artcats->is_expandable_mode()) : ?>
    <?php if ($artcats->is_subcategories_off()) : ?> 
    <ul id="<?php echo $artcats->getExpandableID(); ?>" class="artcats">
    <?php foreach($list as $category) : ?>
        <li class="expandable">
            <img src="modules/mod_artcats/tmpl/img/<?php echo $artcats->getExpandableToggle($category->active); ?>.gif" class="toggle" />
            <a href="<?php echo $category->link; ?>" class="<?php echo $category->active; ?>"><?php echo $category->text;?> <?php echo $category->count; ?></a>
            <ul class="<?php echo $artcats->getExpandableToggle($category->active); ?>">
            <?php foreach ($category->articles as $article) : ?>
            <li class="">
                <a href="<?php echo $article->link; ?>" class="<?php echo $article->active; ?>">
                    <?php echo $article->text;?>
                </a>
            </li>     
            <?php endforeach; ?>
            </ul>
    <?php endforeach; ?>
        <li><a id="<?php echo $artcats->getExpandableID(); ?>ExpandAll" class="expandAll"  class="toggle" href="#">Expand All</a></li>
        <li><a id="<?php echo $artcats->getExpandableID(); ?>CollapseAll" class="collapseAll" class="toggle" href="#">Collapse All</a></li>           
    </ul>
    <?php else : ?>
        <?php echo $artcats->getDisplayableExpandable($list); ?>
    <?php endif; ?>
<?php endif; ?>