<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php if (($artcats->is_on_category_page() or $artcats->is_on_article_page()) and $artcats->is_both_mode()) : ?>
    <h3><?php echo $params->get('articles_mode_title'); ?></h3>
<?php endif; ?>
<?php if ($artcats->is_on_section_page() and $artcats->is_both_mode()) : ?>
    <h3><?php echo $params->get('categories_mode_title'); ?></h3>
<?php endif; ?>

<?php if ($artcats->is_combine_on() && !$artcats->is_articles_mode()) : ?>

    <?php if ($artcats->is_subcategories_off()) : ?> 
    <ul class="articles<?php echo $params->get('moduleclass_sfx'); ?>">
    <?php foreach ($list as $category) : ?>
    <li class="parent articles<?php echo $params->get('moduleclass_sfx'); ?> <?php echo $category->active; ?>">
       <a href="<?php echo $category->link; ?>">
           <?php echo $category->text;?> <?php echo $category->count; ?>
       </a>
       <ul class="articles<?php echo $params->get('moduleclass_sfx'); ?>">
       <?php foreach ($category->articles as $article) : ?>
       <li class="articles<?php echo $params->get('moduleclass_sfx'); ?> <?php echo $article->active; ?>">
           <a href="<?php echo $article->link; ?>">
               <?php echo $article->text;?>
           </a>
       </li>     
       <?php endforeach; ?>
       </ul>
    </li>
    <?php endforeach; ?>
    </ul>
    <?php elseif ($artcats->is_subcategories_on()) : ?>
        <?php $artcats->getDisplayableDefault($list); ?>
    <?php endif; ?>

<?php elseif ($artcats->is_combine_off() || $artcats->is_articles_mode()) : ?>
    <?php if ($artcats->is_subcategories_off() || $artcats->is_articles_mode()) : ?>
    <ul class="articles<?php echo $artcats->getClassSuffix(); ?>">
        <?php foreach ($list as $item) : ?>
        <li class="articles<?php echo $artcats->getClassSuffix(); ?> <?php echo $item->active; ?>">
           <a href="<?php echo $item->link; ?>">
               <?php echo $item->text;?> <?php echo $item->count; ?> 
           </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php elseif ($artcats->is_subcategories_on()) : ?>
        <?php $artcats->getDisplayableDefault($list); ?>
    <?php endif; ?>
<?php endif; ?>