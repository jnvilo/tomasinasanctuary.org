<?php
/**
* @version        2.0.3    2009-04-25
* @package        Joomla
* @copyright    Copyright (C) 2008-09 Omar Ramos, Imperial Valley College. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
*/

/// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class modArtCatsParamsHelper
{
    var $params = '';
    
    /**
    * modArtCatsParamsHelper Constructor
    * Adds the params object as a member variable
    * so that it can be used throughout this class
    * and may also be used in modArtCatsHelper.
    * 
    * @param mixed $params
    * @return modArtCatsParamsHelper
    */
    function __construct(&$params)
    {
        $this->params =& $params;
    }
    
// BEGIN PARAMS SECTION    
    
    /**
    * Gets mode set by user in module params
    * 
    * @return string
    */
    function getMode()
    {
        return $this->params->get('mode');
    }

    /**
    * Gets the title set by the user
    * in module params to show on the module
    * when Articles Mode is on
    * 
    * @return string
    */
    function getArticlesModeTitle()
    {
        return $this->params->get('articles_mode_title');
    }
    
    /**
    * Gets the title set by the user
    * in module params to show on the module
    * when Categories Mode is on
    * 
    * @return string
    */
    function getCategoriesModeTitle()
    {
        return $this->params->get('categories_mode_title');
    }
    
    
    /**
    * Gets count set by user in module params
    * 
    * @return integer
    */
    function getCount()
    {
        $count = (int) $this->params->get('count', 20);
        return $count;
    }
    
    /**
    * Gets module class suffix set by the
    * user in the module params
    * 
    * @return string
    */
    function getClassSuffix()
    {
        return $this->params->get('moduleclass_sfx');
    }
    
    /**
    * Gets article field to ORDER BY
    * 
    * @return string
    */
    function getArticleOrderByField()
    {
        return $this->params->get('art_order');
    }
    
    /**
    * Gets article list ordering
    * Can either by ASC or DESC
    * 
    * @return string
    */
    function getArticleOrdering()
    {
        return $this->params->get('art_order_type');
    }    
    
    /**
    * Gets category field to ORDER BY
    * 
    * @return string
    */
    function getCategoryOrderByField()
    {
        return $this->params->get('cat_order');
    }
    
    /**
    * Gets category list ordering
    * Can either by ASC or DESC
    * 
    * @return string
    */
    function getCategoryOrdering()
    {
        return $this->params->get('cat_order_type');
    }   
    
    /**
    * Gets module template to use
    * for displaying ArtCats
    * 
    * @return string
    */
    function getTemplate()
    {
        return $this->params->get('tmpl');
    }         
    
    /**
    * Gets defaultstyle template to use
    * for displaying ArtCats when Default
    * mode has been chosen and subcategories
    * is turned on.
    * 
    * @return string
    */
    function getDefaultstyleTemplate()
    {
        return $this->params->get('defaultstyle_tmpl');
    }
    
    /**
    * Gets menustyle template to use
    * for displaying ArtCats when Menustyle
    * mode has been chosen and subcategories
    * is turned on.
    * 
    * @return string
    */
    function getMenustyleTemplate()
    {
        return $this->params->get('menustyle_tmpl');
    }
    
    /**
    * Gets expandable template to use
    * for displaying ArtCats when Expandable
    * mode has been chosen
    * 
    * @return string
    */
    function getExpandableTemplate()
    {
        return $this->params->get('expandable_tmpl');
    }
    
    /**
    * Gets the expandable ID to use
    * for this particular module instance
    *
    * @return string 
    */
    function getExpandableID()
    {
        return $this->params->get('expandable_id');
    }
    
    function getExpandableJavaScript()
    {
        $id = $this->getExpandableID();
        
        $js = <<< TOGGLE
        window.addEvent('domready', function() {
            $('{$id}').getElements('img.toggle').addEvent('click', function() {
                toggle(this, '{$id}');
            });
            $('${id}ExpandAll').addEvent('click', function() {
                toggle(this, '{$id}');
            });
            $('${id}CollapseAll').addEvent('click', function() {
                toggle(this, '{$id}');
            });
        });
TOGGLE;
        return $js;
    }
    
    /**
    * Gets the correct Expandable
    * toggle value, either:
    * expand or contract.
    *
    * @return string 
    */
    function getExpandableToggle($active)
    {
        if(empty($active)) {
            return 'expand';
        } else {
            return 'contract';
        }
    } 
    
    /**
    * Gets sitemap template to use
    * for displaying ArtCats when Sitemap
    * mode has been chosen
    * 
    * @return string
    */
    function getSitemapTemplate()
    {
        return $this->params->get('sitemap_tmpl');
    }
    
    /**
    * Gets archive template to use
    * for displaying ArtCats when Archive
    * mode has been chosen
    * 
    * @return string
    */
    function getArchiveTemplate()
    {
        return $this->params->get('archive_tmpl');
    }    
    
    /**
    * Gets Article View Type to use when creating
    * URLs for ArtCats
    * 
    * @return string
    */
    function getArticleViewType()
    {
        return $this->params->get('article_view_type');
    }    
    
    /**
    * Gets Category View Type to use when creating
    * URLs for ArtCats
    * 
    * @return string
    */
    function getCategoryViewType()
    {
        return $this->params->get('category_view_type');
    }         

// END PARAMS SECTION
// BEGIN IDs SECTION

    /**
    * Gets the sectionid. This can be either
    * the section id set by the user in the module params
    * or the automatically determined section id taken from
    * the context of the current page.
    * 
    * @return integer
    */
    function getSectionID()
    {
        if (($this->is_categories_mode()) or ($this->is_both_mode()) or $this->is_expandable_mode())
        {
            if (($this->is_auto_mode_on()) and ($this->is_on_section_page()))
            {
                return JRequest::getInt( 'id', 0 );
            }
            else if ((($this->is_auto_mode_on()) and ($this->is_on_category_page())) or 
                     ($this->is_combine_on() and ($this->is_on_category_page())))
            {
                $catid = JRequest::getInt( 'id', 0 );
                
                $query = 'SELECT section' .
                        ' FROM #__categories' .
                        ' WHERE id = '. $catid;
                    
                $db =& JFactory::getDBO();
                $db->setQuery($query);
                return (int) $db->loadResult();
            }
            else if ((($this->is_auto_mode_on()) and ($this->is_on_article_page())) or 
                     ($this->is_combine_on() and ($this->is_on_article_page())))
            {

                // This is a special case that handles article links that
                // come from a menu module since those links do not contain
                // a catid or catslug, so I need to do a quick database
                // lookup to get the catid.
                $article_id = JRequest::getInt( 'id', 0);
                $query = 'SELECT sectionid' .
                        ' FROM #__content' .
                        ' WHERE id = '. $article_id;
                
                $db =& JFactory::getDBO();
                $db->setQuery($query);
                return (int) $db->loadResult();
            }
            else if ($this->is_auto_mode_off())
            {
                return $this->params->get( 'sectionid', 0 ); // the selected section, default is 0 (module is not shown)
            }
            else
            {
                return 0;
            }
        }
    }

    /**
    * Gets the categoryid. This can be either
    * the category id set by the user in the module params
    * or the automatically determined category id taken from
    * the context of the current page.
    * 
    * @return integer
    */    
    function getCategoryID()
    {
        /**
        * Handles Articles Mode or Both Mode
        */
        if ($this->is_articles_mode() or $this->is_categories_mode() or $this->is_both_mode() or $this->is_expandable_mode())
        {
            if (($this->is_auto_mode_on()) and ($this->is_on_category_page()))
            {
                $catid = JRequest::getInt( 'id', 0 );
                return $catid;
            }
            else if (($this->is_auto_mode_on()) and ($this->is_on_article_page()))
            {
                $catid = JRequest::getInt( 'catid', 0 );
                if ($catid != 0) {
                    return $catid;
                } else {
                    // This is a special case that handles article links that
                    // come from a menu module since those links do not contain
                    // a catid or catslug, so I need to do a quick database
                    // lookup to get the catid.
                    $article_id = JRequest::getInt( 'id', 0);
                    $query = 'SELECT catid' .
                            ' FROM #__content' .
                            ' WHERE id = '. $article_id;
                    
                    $db =& JFactory::getDBO();
                    $db->setQuery($query);
                    return (int) $db->loadResult();
                }
            }
            else if ($this->is_auto_mode_off())
            {
                return $this->params->get( 'catid', 0 ); // the selected category, default is 0 (module is not shown)   
            }
            else
            {
                return 0;
            }
        }
        
        /**
        * Handles Both Mode
        */
        if ($this->is_both_mode())
        {
            if (($this->is_auto_mode_on()) and ($this->is_on_article_page()))
            {
                $catid = JRequest::getInt( 'catid', 0 );
                if ($catid != 0) {
                    return $catid;
                } else {
                    // This is a special case that handles article links that
                    // come from a menu module since those links do not contain
                    // a catid or catslug, so I need to do a quick database
                    // lookup to get the catid.
                    $article_id = JRequest::getInt( 'id', 0);
                    $query = 'SELECT catid' .
                            ' FROM #__content' .
                            ' WHERE id = '. $article_id;
                    
                    $db =& JFactory::getDBO();
                    $db->setQuery($query);
                    return (int) $db->loadResult();
                }
            }
        }    
    }
    
    function getArticleID()
    {
        if ($this->is_on_article_page())
        {
            return JRequest::getInt('id', 0);
        }
        return 0;
    }
    
// END IDs SECTION
// BEGIN URLs SECTION

    /**
    * Takes in an article slug, cat slug, and section id
    * and returns the relative URL (can be normal or SEF)
    * for that article.
    * 
    * @param string $articleslug
    * @param string $catslug
    * @param string $sectionid
    * @return The translated humanly readible URL
    */
    function getArticleURL($articleslug, $catslug, $sectionid)
    {
        if ($this->getArticleViewType() == 'form') {
            $articleRoute = ContentHelperRoute::getArticleRoute($articleslug, $catslug, $sectionid);
            $itemid = JString::strpos($articleRoute, 'Itemid');
            if ($itemid) {
                $articleFormRoute = JString::str_ireplace('Itemid', 'layout=form&Itemid', $articleRoute);
            } else {
                $articleFormRoute = $articleRoute . '&layout=form';
            }            
            return JRoute::_($articleFormRoute);
        }
        return JRoute::_(ContentHelperRoute::getArticleRoute($articleslug, $catslug, $sectionid));
    }
    
    /**
    * Takes in a cat slug and section id
    * and returns the relative URL (can be normal or SEF)
    * for that category.
    * 
    * @param string $catslug
    * @param string $sectionid
    * @return The translated humanly readible URL
    */
    function getCategoryURL($catslug, $sectionid)
    {
        if ($this->getCategoryViewType() == 'default') {
            $categoryRoute = ContentHelperRoute::getCategoryRoute($catslug, $sectionid);
            $categoryListRoute = JString::str_ireplace('&layout=blog', '', $categoryRoute);
            return JRoute::_($categoryListRoute);
        } else {
            $categoryRoute = ContentHelperRoute::getCategoryRoute($catslug, $sectionid);
            if (JString::strpos($categoryRoute, '&layout=blog') !== false) {
                return JRoute::_($categoryRoute);
            } else {
                $categoryRoute = $categoryRoute . '&layout=blog';
                return JRoute::_($categoryRoute);
            }
        }
    }
    
    /**
    * Takes in a section id and returns the relative
    * blog URL for that section. 
    * 
    * @param integer $sectionid
    * @return string URL
    */
    function getSectionURL($sectionid)
    {
        return JRoute::_(ContentHelperRoute::getSectionRoute($sectionid).'&layout=blog');
    }

// END URLs SECTION
// BEGIN PREDICATES SECTION
    
    /**
    * Boolean that tells whether the user
    * has set the module mode to 'articles'
    * 
    * @return boolean
    */
    function is_articles_mode()
    {
        return ($this->getMode() == 'articles') ? TRUE : FALSE;
    }
    
    /**
    * Boolean that tells whether the user
    * has set the module mode to 'categories'
    * 
    * @return boolean
    */
    function is_categories_mode()
    {
        return ($this->getMode() == 'categories') ? TRUE : FALSE;
    }

    /**
    * Boolean that tells whether the user
    * has set the module mode to 'both'
    * 
    * @return boolean
    */
    function is_both_mode()
    {
        return ($this->getMode() == 'both') ? TRUE : FALSE;
    }
    
    /**
    * Boolean that tells whether the user
    * has set the module mode to 'expandable'
    * 
    * @return boolean
    */
    function is_expandable_mode()
    {
        return ($this->getMode() == 'expandable') ? TRUE : FALSE;
    }                  
        
    /**
    * Boolean that tells whether the user
    * has set the module mode to 'sitemap'
    * 
    * @return boolean
    */
    function is_sitemap_mode()
    {
        return ($this->getMode() == 'sitemap') ? TRUE : FALSE;
    }
    
    /**
    * Boolean that tells whether the user
    * has set the module mode to 'archive'
    * 
    * @return boolean
    */
    function is_archive_mode()
    {
        return ($this->getMode() == 'archive') ? TRUE : FALSE;
    }     
            
    /**
    * Boolean that tells whether the user is on
    * an article page or not
    * 
    * @return boolean If true, then on article page
    */
    function is_on_article_page()
    {
        static $is_on_article_page;

        if (!is_bool($is_on_article_page))
        {
            $is_on_article_page = ((JRequest::getCmd( 'view' ) == 'article') and (JRequest::getCmd( 'option' ) == 'com_content')) ? TRUE : FALSE;
        }

        return $is_on_article_page;
    }
    
    /**
    * Boolean that tells whether the user is on
    * a category page or not
    * 
    * @return boolean If true, then on category page
    */
    function is_on_category_page()
    {
        static $is_on_category_page;

        if (!is_bool($is_on_category_page))
        {
            $is_on_category_page = ((JRequest::getCmd( 'view' ) == 'category') and (JRequest::getCmd( 'option' ) == 'com_content')) ? TRUE : FALSE;
        }
        
        return $is_on_category_page;
    }    
    
    /**
    * Boolean that tells whether the user is on
    * a section page or not
    * 
    * @return boolean If true, then on section page
    */
    function is_on_section_page()
    {
        static $is_on_section_page;

        if (!is_bool($is_on_section_page))
        {
            $is_on_section_page = ((JRequest::getCmd( 'view' ) == 'section') and (JRequest::getCmd( 'option' ) == 'com_content')) ? TRUE : FALSE;
        }
        
        return $is_on_section_page;
    }
    
    /**
    * Boolean that tells whether the user set
    * auto mode to on
    * 
    * @return boolean If true, then auto mode is on
    */
    function is_auto_mode_on()
    {
        $auto = $this->params->get('automatic'); 
        return ($auto == 1) ? TRUE : FALSE;
    }
    
    /**
    * Boolean that tells whether the user set
    * auto mode to off
    * 
    * @return boolean If true, then auto mode is off
    */
    function is_auto_mode_off()
    {
        $auto = $this->params->get('automatic'); 
        return ($auto == 0) ? TRUE : FALSE;
    }
    
    /**
    * Boolean that tells whether we should
    * show ArtCats on article pages
    * 
    * @return boolean If 0 (false), then don't show on article pages
    */
    function is_show_on_articles()
    {
        return $this->params->get('show_on_articles');
    }
    
    /**
    * Boolean that tells whether we should
    * show ArtCats on category pages
    * 
    * @return boolean If 0 (false), then don't show on category pages
    */
    function is_show_on_categories()
    {
        return $this->params->get('show_on_categories');
    }
    
    /**
    * Boolean that tells whether we should
    * show ArtCats on category pages
    * 
    * @return boolean If 0 (false), then don't show on category pages
    */
    function is_show_empty_categories()
    {
        return $this->params->get('show_empty_categories');
    }  
    
    /**
    * Boolean that tells whether we should
    * show Article Counts on the Category
    * link titles
    * 
    * @return boolean If 0 (false), then don't show the article count
    */
    function is_show_article_count()
    {
        return $this->params->get('show_article_count');
    }  

    /**
    * Boolean that tells whether the user
    * enabled subcategories support in the module params
    * 
    * @return boolean If true, then subcategories support is on
    */
    function is_subcategories_on()
    {
        $subcategories = $this->params->get('subcategories'); 
        return ($subcategories == 1) ? TRUE : FALSE;
    }
    
    /**
    * Boolean that tells whether the user
    * disabled subcategories support in the module params
    * 
    * @return boolean If true, then subcategories support is off
    */
    function is_subcategories_off()
    {
        $subcategories = $this->params->get('subcategories'); 
        return ($subcategories == 0) ? TRUE : FALSE;
    }
    
    /**
    * Boolean that tells whether the user
    * enabled the Category and Article Lists
    * to be combined in the module params
    * 
    * @return boolean If true, then combine mode is on
    */
    function is_combine_on()
    {
        if ($this->is_expandable_mode())
        {
            return TRUE;
        }
        $combine = $this->params->get('combine'); 
        return ($combine == 1) ? TRUE : FALSE;
    }
    
    /**
    * Boolean that tells whether the user
    * disabled the Category and Article Lists
    * to be combined in the module params
    * 
    * @return boolean If true, then combine mode is off
    */
    function is_combine_off()
    {
        $combine = $this->params->get('combine'); 
        return ($combine == 0) ? TRUE : FALSE;
    }                                         

// END PREDICATES SECTION
}