<?php
/**
* @version        2.0.3    2009-04-25
* @package        Joomla
* @copyright    Copyright (C) 2008-09 Omar Ramos, Imperial Valley College. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
*/

/// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'params.php');   

class modArtCatsHelper extends modArtCatsParamsHelper
{
    var $aid = '';
    var $nulldate = '';
    var $now = '';
    var $access = '';
    
    /**
    * modArtCatsHelper Constructor
    * Adds a call to the parent modArtCatsParamsHelper
    * object and passes the params object to it.
    * 
    * Initializes other member variables.
    * 
    * @param mixed $params
    * @return modArtCatsHelper
    */
    function __construct(&$params)
    {
        parent::__construct($params);
        
        $user =& JFactory::getUser();
        $this->aid = $user->get('aid', 0);
        
        $db =& JFactory::getDBO();
        $this->nulldate = $db->Quote($db->getNullDate());
        
        $date =& JFactory::getDate();
        $this->now = $db->Quote($date->toMySQL());
        
        $contentConfig =& JComponentHelper::getParams( 'com_content' );
        $this->access = !$contentConfig->get('shownoauth');
    }
    
    /**
    * Gets list which will be used
    * by the template to render the links.
    * 
    */
    function getList()
    {
        $queries = $this->getQueries();
        $rows  = $this->getRows($queries);
        $list  = $this->getProcessedRows($rows);
        
        return $list;        
    }

// BEGIN QUERY SECTION
    
    /**
    * Gets the correct query to use using
    * the parameters chosen by the user.
    * 
    * It just goes through a number of if statements
    * to figure out the correct query to use to create
    * the lists.
    * 
    * @return array
    */
    function getQueries()
    {
        /**
        * Container Array for Our Queries
        * if we are going to be returning more
        * than one query. I will always use the 
        * convention of 'art' and 'cat' for the 
        * keys relating to each query.
        */
        // Superseded by getCombinedQueries() method
        
        /**
        * Start Archive Mode On Section
        */
        if ($this->is_archive_mode())
        {
        
        }
        
        /**
        * Start Sitemap Mode On Section
        */
        if ($this->is_sitemap_mode())
        {
            return $this->getSitemapQueries() ;
        }
        
        /**
        * Start Expandable Mode On Section
        */
        if ($this->is_expandable_mode())
        {
            if ($this->is_on_section_page() or 
                $this->is_on_category_page() or 
                $this->is_on_article_page())
            {
                if ($this->is_combine_on())
                {
                    return $this->getCombinedQueries();
                }
            }
        }
        
        
        /**
        * Start Both Mode On Section
        */
        if ($this->is_both_mode())
        {
            if ($this->is_on_section_page())
            {
                if ($this->is_combine_on())
                {
                    return $this->getCombinedQueries();
                }
                return $this->getCategoryQuery();
            }
            if (($this->is_on_category_page() and $this->is_show_on_categories()) or 
                ($this->is_on_article_page()) and $this->is_show_on_articles())
            { 
                if ($this->is_combine_on())
                {
                    return $this->getCombinedQueries();
                }
                return $this->getArticleQuery();
            }
            return $this->getNullQuery();
        }
        
        /**
        * Start Categories Mode On Section
        */
        if ($this->is_categories_mode())
        {
            
            if (($this->is_on_section_page()) or
                ($this->is_show_on_categories() and $this->is_on_category_page()) or
                ($this->is_show_on_articles() and $this->is_on_article_page()) or
                ($this->is_auto_mode_off()))
            {
                if ($this->is_combine_on()) 
                {
                    return $this->getCombinedQueries();
                }
                return $this->getCategoryQuery();
            }
            return $this->getNullQuery();
        }
        
        /**
        * Start Articles Mode On Section
        */
        if ($this->is_articles_mode())
        {
            if (($this->is_on_category_page()) or
                ($this->is_show_on_articles() and $this->is_on_article_page()) or
                ($this->is_auto_mode_off()))
            {
                return $this->getArticleQuery();
            }
            return $this->getNullQuery();
        }
        
        /**
        * If none of the combinations above works out
        * then we should return false.
        */
        return false;    
    }
    
    /**
    * Returns the query that returns the list of articles
    * 
    * @return string
    */
    function getArticleQuery()
    {
        $where = 'a.state = 1'
            . ' AND ( a.publish_up = '.$this->nulldate.' OR a.publish_up <= '.$this->now.' )'
            . ' AND ( a.publish_down = '.$this->nulldate.' OR a.publish_down >= '.$this->now.' )'
            ;

        $query = 'SELECT a.id, a.sectionid, a.title, a.catid,' .
            ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
            ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
            ' FROM #__content AS a' .
            ' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
            ' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
            ' WHERE '. $where .' AND s.id > 0 AND a.catid='. $this->getCategoryID() .
            ($this->access ? ' AND a.access <= ' .(int) $this->aid. ' AND cc.access <= ' .(int) $this->aid. ' AND s.access <= ' .(int) $this->aid : '').
            ' AND s.published = 1' .
            ' AND cc.published = 1' .
            ' ORDER BY a.'. $this->getArticleOrderByField() .' '. $this->getArticleOrdering();
        
        return $query;
    }
    
    /**
    * Returns the query that returns the list of categories
    * 
    * @return string
    */
    function getCategoryQuery()
    {
        $empty_category     = '';
        
        if ( !$this->is_show_empty_categories() ) {
            $empty_category = " HAVING cnt > 0";
        }
        
        $query = 'SELECT a.id, a.parent_id, a.title, a.section, COUNT(b.id) AS cnt,' .
                // Next line added to make J!1.5.7 SEF compatible
                ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as catslug'.
                ' FROM #__categories as a' .
                ' LEFT JOIN #__content as b ON b.catid = a.id' .
                ($this->access ? ' AND b.access <= '.(int) $this->aid : '') .
                ' AND ( b.publish_up = '.$this->nulldate.' OR b.publish_up <= '.$this->now.' )' .
                ' AND ( b.publish_down = '.$this->nulldate.' OR b.publish_down >= '.$this->now.' )' .
                ' WHERE a.published = 1' .
                ' AND a.section = '. $this->getSectionID() .
                ($this->access ? ' AND a.access <= '.$this->aid : '') .
                ' GROUP BY a.id '.
                $empty_category .
                ' ORDER BY a.'. $this->getCategoryOrderByField() .' '. $this->getCategoryOrdering();
        
        return $query;
    }
    
    /**
    * Returns the combined queries array
    * needed for the combined and expandable
    * modes.
    * 
    * @return array
    */
    function getCombinedQueries()
    {
        $queries = array();
        
        /**
        * Special Article Query
        * that selects all of the articles
        * for all of the categories in the
        * current section.
        */
        $empty_category     = '';
        
        if ( !$this->is_show_empty_categories() ) {
            $empty_category = " HAVING cnt > 0";
        }
        
        $in = 'SELECT b.id' .
                ' FROM #__categories as b' .
                ' WHERE b.published = 1' .
                ' AND b.section = '. $this->getSectionID() .
                ($this->access ? ' AND b.access <= '.$this->aid : '') .
                ' GROUP BY b.id '.
                ' ORDER BY b.'. $this->getCategoryOrderByField() .' '. $this->getCategoryOrdering();
        
        $where = 'a.state = 1'
            . ' AND ( a.publish_up = '.$this->nulldate.' OR a.publish_up <= '.$this->now.' )'
            . ' AND ( a.publish_down = '.$this->nulldate.' OR a.publish_down >= '.$this->now.' )'
            ;

        $query = 'SELECT a.id, a.sectionid, a.title, a.catid,' .
            ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
            ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
            ' FROM #__content AS a' .
            ' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
            ' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
            ' WHERE '. $where .' AND s.id > 0 AND a.catid IN ('. $in .') ' .
            ($this->access ? ' AND a.access <= ' .(int) $this->aid. ' AND cc.access <= ' .(int) $this->aid. ' AND s.access <= ' .(int) $this->aid : '').
            ' AND s.published = 1' .
            ' AND cc.published = 1' .
            ' ORDER BY a.'. $this->getArticleOrderByField() .' '. $this->getArticleOrdering();                
        
        $queries['art'] = $query;
        $queries['cat'] = $this->getCategoryQuery();
        
        return $queries;
    }
    
    function getSitemapQueries()
    {
        /**
        * Query to collect all published sections
        * this will be used as input to the IN prtion 
        * of the categories query below.
        * 
        * @var mixed
        */
        $sections = 'SELECT c.id' .
                    ' FROM #__sections as c' .
                    ' WHERE c.published = 1' .
                    ($this->access ? ' AND c.access <= '.$this->aid : '') .
                    ' GROUP BY c.id '.
                    ' ORDER BY c.'. $this->getCategoryOrderByField() .' '. $this->getCategoryOrdering();
        
        /**
        * Query to collect all published categories
        * from the sections returned from the query above.
        * 
        * @var mixed
        */
        $categories = 'SELECT b.id, b.parent_id, b.title, b.section, ' .
                      ' CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(":", b.id, b.alias) ELSE b.id END as catslug' .
                      ' FROM #__categories as b' .
                      ' WHERE b.published = 1' .
                      ' AND b.section IN ('. $sections .') ' .
                      ($this->access ? ' AND b.access <= '.$this->aid : '') .
                      ' GROUP BY b.id '.
                      ' ORDER BY b.'. $this->getCategoryOrderByField() .' '. $this->getCategoryOrdering();
        
        /**
        * Query to collect all published catids
        * from the sections returned from the $sections query.
        * 
        * @var mixed
        */
        $catids = 'SELECT b.id' .
                      ' FROM #__categories as b' .
                      ' WHERE b.published = 1' .
                      ' AND b.section IN ('. $sections .') ' .
                      ($this->access ? ' AND b.access <= '.$this->aid : '') .
                      ' GROUP BY b.id '.
                      ' ORDER BY b.'. $this->getCategoryOrderByField() .' '. $this->getCategoryOrdering();
        
        /**
        * Query to collect all of the published
        * articles from the catids above.
        *               
        * @var mixed
        */
        $where = 'a.state = 1'
            . ' AND ( a.publish_up = '.$this->nulldate.' OR a.publish_up <= '.$this->now.' )'
            . ' AND ( a.publish_down = '.$this->nulldate.' OR a.publish_down >= '.$this->now.' )'
            ;

        $articles = 'SELECT a.id, a.sectionid, a.title, a.catid,' .
            ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
            ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
            ' FROM #__content AS a' .
            ' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
            ' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
            ' WHERE '. $where .' AND s.id > 0 AND a.catid IN ('. $catids .') ' .
            ($this->access ? ' AND a.access <= ' .(int) $this->aid. ' AND cc.access <= ' .(int) $this->aid. ' AND s.access <= ' .(int) $this->aid : '').
            ' AND s.published = 1' .
            ' AND cc.published = 1' .
            ' ORDER BY a.'. $this->getArticleOrderByField() .' '. $this->getArticleOrdering();
        
        $queries['art'] = $articles;
        $queries['cat'] = $categories;
        
        /**
        * Query to select all sections. This will be used
        * to create the URLs for each section (otherwise we
        * would not have the title!).
        * 
        * @var mixed
        */
        $sections = 'SELECT c.id, c.title' .
                    ' FROM #__sections as c' .
                    ' WHERE c.published = 1' .
                    ($this->access ? ' AND c.access <= '.$this->aid : '') .
                    ' GROUP BY c.id '.
                    ' ORDER BY c.'. $this->getCategoryOrderByField() .' '. $this->getCategoryOrdering();
        
        $queries['sections'] = $sections;
        
        return $queries;
    }
    
    function getNullQuery()
    {
        return '';
    }
    
// END QUERY SECTION
// BEGIN ROWS SECTION   
 
    /**
    * Gets the object list of category/article
    * rows. Can be given a single query string
    * or an array of query strings. 
    * 
    * @param mixed $query
    */
    function getRows($query)
    {
        if (empty($query))
        {
            return '';
        }
        
        $db =& JFactory::getDBO();
        if (is_array($query))
        {
            $rows = array();
            
            if ($this->is_sitemap_mode())
            {
                if ($this->is_subcategories_on())
                {
                    $db->setQuery($query['art'], 0, $this->getCount());
                    $artList = $db->loadObjectList();
                    $rows['art'] = $this->getChildren($artList, 'article');
                    
                    $db->setQuery($query['cat'], 0, $this->getCount());
                    $catList = $db->loadObjectList();
                    $rows['cat'] = $this->getChildren($catList, 'category');
                    
                    $combined = $this->getCombinedChildren($rows['cat'], $rows['art']);
                    $root_categories = $combined[0];
                    unset($combined[0]);
                    $hierarchical_categories = $this->getHierarchicalCategoryList($root_categories, $combined);

                    $combined = $this->getChildren($hierarchical_categories, 'section');
                    
                    $db->setQuery($query['sections'], 0, $this->getCount());
                    $sectionList = $db->loadObjectList('id');
                    
                    return array('sections' => $sectionList, 'categories' => $combined);
                }
                $db->setQuery($query['art'], 0, $this->getCount());
                $artList = $db->loadObjectList();
                $rows['art'] = $this->getChildren($artList, 'article');
                
                $db->setQuery($query['cat'], 0, $this->getCount());
                $catList = $db->loadObjectList();
                $rows['cat'] = $this->getChildren($catList, 'section');
                   
                $combined = $this->getCombinedChildren($rows['cat'], $rows['art']);
                
                $db->setQuery($query['sections'], 0, $this->getCount());
                $sectionList = $db->loadObjectList('id');
                
                return array('sections' => $sectionList, 'categories' => $combined);
            }
            
            if ($this->is_subcategories_on())
            {
                $db->setQuery($query['art'], 0, $this->getCount());
                $artList = $db->loadObjectList();
                $rows['art'] = $this->getChildren($artList, 'article');
                
                $db->setQuery($query['cat'], 0, $this->getCount());
                $catList = $db->loadObjectList();
                $rows['cat'] = $this->getChildren($catList, 'category');
                   
                $combined = $this->getCombinedChildren($rows['cat'], $rows['art']);
                $root_categories = $combined[0];
                unset($combined[0]);
                $hierarchical_categories = $this->getHierarchicalCategoryList($root_categories, $combined);
                return $hierarchical_categories;
            }
            
            $db->setQuery($query['art'], 0, $this->getCount());
            $artList = $db->loadObjectList();
            $rows['art'] = $this->getChildren($artList, 'article');
            
            $db->setQuery($query['cat'], 0, $this->getCount());
            $catList = $db->loadObjectList();
            $rows['cat'] = $this->getChildren($catList, 'category');
               
            $combined = $this->getCombinedChildren($rows['cat'], $rows['art']);
            return $combined;
        }
        else
        {
            $db->setQuery($query, 0, $this->getCount());
            $rows = $db->loadObjectList();
            if ($this->is_subcategories_on() and $this->is_categories_mode())
            {
                $rows = $this->getChildren($rows, 'category');
                $root_categories = $rows[0];
                unset($rows[0]);
                $hierarchical_categories = $this->getHierarchicalCategoryList($root_categories, $rows);
                return $hierarchical_categories;
            }
            return $rows;
        }
    }
    
    /**
    * From an object list of categories
    * this method reorders the list and
    * returns an array with the parent ids
    * of the categories acting as keys and
    * the values being arrays of objects.
    * 
    * @param array $rows
    */
    function getChildren(&$rows, $mode = 'category')
    {
       // establish the hierarchy of the menu
       $children = array();
       
       if ($mode == 'category') 
       {
           // first pass - collect children
           foreach ($rows as $category)
           {
                // Assigns category parent id to $pt
                $pt = $category->parent_id;
                // Checks to see if parent id already exists
                // in $children array. If so, then $list is 
                // set to that array, else a new array is created.
                $list = @$children[$pt] ? $children[$pt] : array();
                // Add the current category to the end of the list
                // array.
                $list[$category->id] = $category;
                // Assign $list as the value for when the key is
                // equal to the current parent id.
                $children[$pt] = $list;
           }
       }
       else if ($mode == 'article') 
       {
           // first pass - collect children
           foreach ($rows as $article)
           {
                // Assigns category parent id to $pt
                $pt = $article->catid;
                // Checks to see if parent id already exists
                // in $children array. If so, then $list is 
                // set to that array, else a new array is created.
                $list = @$children[$pt] ? $children[$pt] : array();
                // Add the current category to the end of the list
                // array.
                $list[$article->id] = $article;
                // Assign $list as the value for when the key is
                // equal to the current parent id.
                $children[$pt] = $list;
           }
       }
       else if ($mode == 'section')
       {
          // first pass - collect children
           foreach ($rows as $category)
           {
                // Assigns category parent id to $pt
                $pt = $category->section;
                // Checks to see if parent id already exists
                // in $children array. If so, then $list is 
                // set to that array, else a new array is created.
                $list = @$children[$pt] ? $children[$pt] : array();
                // Add the current category to the end of the list
                // array.
                $list[$category->id] = $category;
                // Assign $list as the value for when the key is
                // equal to the current parent id.
                $children[$pt] = $list;
           }
       }
        
       return $children;
    }
    
    /**
    * Combines a category/subcategory object list
    * with a category/article object list into
    * a list that contains categories and their
    * subcategories, with the articles of each
    * category attached (along with the article count).
    * 
    * @param mixed $cat_rows
    * @param mixed $art_rows
    */
    function getCombinedChildren(&$cat_rows, &$art_rows)
    {
        foreach($cat_rows as $categorylist) { 
            foreach($categorylist as $category) {
                $articleList = @$art_rows[$category->id] ? $art_rows[$category->id] : array(); // See if I need to use the @ symbol thingy
                $cnt = is_array($articleList) ? count($articleList) : 0;
                $category->articles = $articleList;
                $category->cnt = $cnt;
            }
        }
        
        $combined_list =& $cat_rows;
        return $combined_list;
    }
    
    function getHierarchicalCategoryList($root_categories, $list_of_parent_categories)
    {
        $hierarchical_list = array();
        
        foreach($root_categories as $root_category) {
            if (array_key_exists($root_category->id, $list_of_parent_categories))
            {
                $subcats = $list_of_parent_categories[$root_category->id];
                unset($list_of_parent_categories[$root_category->id]);
                $hierarchical_list[$root_category->id] = $root_category;
                $hierarchical_list[$root_category->id]->subcats = $this->getHierarchicalCategoryList($subcats, $list_of_parent_categories); 
                
            }
            else
            {
                $hierarchical_list[$root_category->id] = $root_category;
                $hierarchical_list[$root_category->id]->subcats = array();
            }
        }
        
        return $hierarchical_list;

    }
    
// END ROWS SECTION
// BEGIN PROCESSED ROWS SECTION

    function getProcessedRows(&$rows)
    {
        if (empty($rows))
        {
            return array();
        }
        $auto_mode_on       = $this->is_auto_mode_on();
        $auto_mode_off      = $this->is_auto_mode_off();

        $categories_mode_on = $this->is_categories_mode();
        $articles_mode_on   = $this->is_articles_mode();
        $both_on            = $this->is_both_mode();

        $count              = $this->getCount();
        $on_article_page    = $this->is_on_article_page();
        $on_category_page   = $this->is_on_category_page();
        $on_section_page    = $this->is_on_section_page();
        
        if ($this->is_subcategories_on() &&
            ($this->is_categories_mode() || ($this->is_both_mode() && 
                                             ($this->is_on_section_page() ||  
                                              $this->is_on_category_page() || 
                                              $this->is_on_article_page())) || 
             $this->is_expandable_mode()))
        {
            $lists = $this->getProcessedSubcategoryRows($rows);
            return $lists;
        }
        if ($this->is_sitemap_mode())
        {
            if ($this->is_subcategories_on())
            {
                $lists = $this->getProcessedSitemapSubcategoryRows($rows);
                return $lists;
            }
            $lists = $this->getProcessedSitemapRows($rows);
            return $lists;
        }
        
        if ($this->is_expandable_mode())
        {
           $lists = $this->getProcessedExpandableRows($rows);
           return $lists;
        }
        
        if ($this->is_combine_on() &&
            ($this->is_categories_mode() || 
             ($this->is_both_mode() && ($this->is_on_category_page() || 
                                        $this->is_on_article_page() || 
                                        $this->is_on_section_page()))))
        {
            $lists = $this->getProcessedCombinedRows($rows);
            return $lists;
        }
        
        if ($this->is_articles_mode())
        {
            $lists = $this->getProcessedArticleRows($rows);
            return $lists;            
        }
        
        if ($this->is_categories_mode())
        {
            $lists = $this->getProcessedCategoryRows($rows);
            return $lists;            
        }
        
        if (($auto_mode_on and $on_article_page) or
            ($auto_mode_on and $on_category_page) or
            ($auto_mode_on and $on_section_page))
            {

                // Case 1: Both Modes On and on Category or Section Page
                if ($both_on and ($on_article_page or $on_category_page or $on_section_page))
                {
                    $lists = $this->getProcessedBothRows($rows);
                    
                    return $lists;
                }

                // Case 2: Articles Mode On and on Category Page
                if ($articles_mode_on and $on_category_page)
                {
                    $lists = $this->getProcessedArticleRows($rows);

                    return $lists;
                }

                // Case 3: Categories Mode On and on Section Page
                if ($categories_mode_on and $on_section_page)
                {
                    $lists = $this->getProcessedCategoryRows($rows);

                    return $lists;
                }
            }
            else if ($auto_mode_off)
            {
                // Case 1: Articles Mode On
                if ($articles_mode_on)
                {
                    $lists = $this->getProcessedArticleRows($rows);

                    return $lists;
                }

                // Case 2: Categories Mode On
                if ($categories_mode_on)
                {
                    $lists = $this->getProcessedCategoryRows($rows);

                    return $lists;
                }
                
            }
    }
    
    function getProcessedArticleRows(&$rows)
    {
        $i        = 0;
        $lists    = array();
        $artid = $this->getArticleID();
        
        foreach ( $rows as $row )
        {
            $lists[$i]->artid = $row->id;
            $lists[$i]->link = $this->getArticleURL($row->slug, $row->catslug, $row->sectionid);
            $lists[$i]->text = $this->_cleanTitle($row->title);
            $lists[$i]->count = '';
            
            if ($row->id == $artid) {
                $active = 'active';
            } else {
                $active = '';
            }
            $lists[$i]->active = $active;
            
            $i++;
        }

        return $lists;
    }
    
    function getProcessedCategoryRows(&$rows)
    {
        $i        = 0;
        $lists    = array(); 
        $catid = $this->getCategoryID();
        
        foreach ( $rows as $row )
        {
            $lists[$i]->catid = $row->id;
            $lists[$i]->link = $this->getCategoryURL($row->catslug, $row->section);
            $lists[$i]->text = $this->_cleanTitle($row->title);
            
            if ($this->is_show_article_count()) {
                $count = '('.$row->cnt.')';
            } else {
                $count = '';
            }
            $lists[$i]->count = $count;
            
            if ($row->id == $catid) {
                $active = 'active';
            } else {
                $active = '';
            }
            $lists[$i]->active = $active;
            
            $i++;
        }

        return $lists;
    }
    
    function getProcessedBothRows(&$rows)
    {
        $on_article_page    = $this->is_on_article_page();
        $on_category_page   = $this->is_on_category_page();
        $on_section_page    = $this->is_on_section_page();
        
        $i        = 0;
        $lists    = array();
        $catid = $this->getCategoryID();
        $artid = $this->getArticleID();
        // Useful for debugging
        /*$lists[10]->link = $catid;
        $lists[10]->text = $catid;
        $lists[11]->link = $article_id;
        $lists[11]->text = $article_id;*/
        foreach ( $rows as $row )
        {
            if ($on_article_page)
            {
                $lists[$i]->link = $this->getArticleURL($row->slug, $row->catslug, $row->sectionid);
                if ($row->id == $artid) {
                    $active = 'active';
                } else {
                    $active = '';
                }
                $lists[$i]->active = $active;
                $lists[$i]->count = ''; 
            }
            if ($on_category_page)
            {
                $lists[$i]->link = $this->getArticleURL($row->slug, $row->catslug, $row->sectionid);
                if ($row->id == $artid) {
                    $active = 'active';
                } else {
                    $active = '';
                }
                $lists[$i]->active = $active;
                $lists[$i]->count = ''; 
            }
            if ($on_section_page)
            {
                $lists[$i]->link = $this->getCategoryURL($row->catslug, $row->section);
                if ($this->is_show_article_count()) {
                    $count = '('.$row->cnt.')';
                } else {
                    $count = '';
                }
                $lists[$i]->count = $count;
                
                if ($row->id == $catid) {
                    $active = 'active';
                } else {
                    $active = '';
                }
                $lists[$i]->active = $active;
            }
            $lists[$i]->text = $this->_cleanTitle($row->title);
            $i++;
        }

        return $lists;        
    }
    
    function getProcessedCombinedRows(&$rows)
    {
        $on_article_page    = $this->is_on_article_page();
        $on_category_page   = $this->is_on_category_page();
        $on_section_page    = $this->is_on_section_page();
        
        $i        = 0;
        $lists    = array();
        $catid = $this->getCategoryID();
        $artid = $this->getArticleID();
        // Useful for debugging
        /*$lists[10]->link = $catid;
        $lists[10]->text = $catid;
        $lists[11]->link = $article_id;
        $lists[11]->text = $article_id;*/
        foreach($rows as $categoryList) {
            foreach($categoryList as $category) {
                $lists[$i]->link = $this->getCategoryURL($category->catslug, $category->section);
                $lists[$i]->text = $this->_cleanTitle($category->title);
                if ($this->is_show_article_count()) {
                    $count = '('.$category->cnt.')';
                } else {
                    $count = '';
                }
                $lists[$i]->count = $count;
                
                if ($category->id == $catid) {
                    $active = 'active';
                } else {
                    $active = '';
                }
                $lists[$i]->active = $active;
                
                $j = 0;
                $lists[$i]->articles = array();
                foreach($category->articles as $article) {
                    $lists[$i]->articles[$j]->link = $this->getArticleURL($article->slug, $article->catslug, $article->sectionid); 
                    $lists[$i]->articles[$j]->text = $this->_cleanTitle($article->title);
                    if ($article->id == $artid) {
                        $active = 'active';
                    } else {
                        $active = '';
                    }
                    $lists[$i]->articles[$j]->active = $active;
                    $j++;
                }
                $i++; 
            }
        }

        return $lists;
    }
    
    function getProcessedExpandableRows(&$rows)
    {
        return $this->getProcessedCombinedRows($rows);      
    }
    
    function getProcessedSitemapRows(&$rows)
    {
        $on_article_page    = $this->is_on_article_page();
        $on_category_page   = $this->is_on_category_page();
        $on_section_page    = $this->is_on_section_page();
        
        $sections = array();
        $lists    = array();
        // Useful for debugging
        /*$lists[10]->link = $catid;
        $lists[10]->text = $catid;
        $lists[11]->link = $article_id;
        $lists[11]->text = $article_id;*/
        foreach($rows['categories'] as $sectionid => $categoryList) {
            $sections[$sectionid]->link = $this->getSectionURL($sectionid);
            $sections[$sectionid]->text = $this->_cleanTitle($rows['sections'][$sectionid]->title);
            $lists[$sectionid] = array();
            foreach($categoryList as $catid => $category) {
                $lists[$sectionid][$catid]->link = $this->getCategoryURL($category->catslug, $category->section);
                $lists[$sectionid][$catid]->text = $this->_cleanTitle($category->title);
                if ($this->is_show_article_count()) {
                    $count = '('.$category->cnt.')';
                } else {
                    $count = '';
                }
                $lists[$sectionid][$catid]->count = $count;
                
                $j = 0;
                $lists[$sectionid][$catid]->articles = array();
                foreach($category->articles as $article) {
                    $lists[$sectionid][$catid]->articles[$j]->link = $this->getArticleURL($article->slug, $article->catslug, $article->sectionid); 
                    $lists[$sectionid][$catid]->articles[$j]->text = $this->_cleanTitle($article->title);
                    $j++;
                }
                $i++; 
            }
        }
        
        

        return array('sections' => $sections, 'categories' => $lists);
    }
    
    function getProcessedSitemapSubcategoryRows(&$rows)
    {
        $on_article_page    = $this->is_on_article_page();
        $on_category_page   = $this->is_on_category_page();
        $on_section_page    = $this->is_on_section_page();
        
        $sections = array();
        $lists    = array();
        
        foreach($rows['categories'] as $sectionid => $categoryList) {
            $sections[$sectionid]->link = $this->getSectionURL($sectionid);
            $sections[$sectionid]->text = $this->_cleanTitle($rows['sections'][$sectionid]->title);
            $lists[$sectionid] = array();
            foreach($categoryList as $catid => $category) {
                $lists[$sectionid][$catid]->link = $this->getCategoryURL($category->catslug, $category->section);
                $lists[$sectionid][$catid]->text = $this->_cleanTitle($category->title);
                if ($this->is_show_article_count()) {
                    $count = '('.$category->cnt.')';
                } else {
                    $count = '';
                }
                $lists[$sectionid][$catid]->count = $count;
                
                $j = 0;
                $lists[$sectionid][$catid]->articles = array();
                foreach($category->articles as $article) {
                    $lists[$sectionid][$catid]->articles[$j]->link = $this->getArticleURL($article->slug, $article->catslug, $article->sectionid); 
                    $lists[$sectionid][$catid]->articles[$j]->text = $this->_cleanTitle($article->title);
                    $j++;
                }
                $i++;
                
                $lists[$sectionid][$catid]->subcats = $this->_getProcessedSitemapSubcategoryRowsHelper($category->subcats);
                 
            }
        }        

        return array('sections' => $sections, 'categories' => $lists);
    }
    
    function _getProcessedSitemapSubcategoryRowsHelper(&$subcategories)
    {
        $processed_subcats = array();
        if (count($subcategories))
        {
            foreach($subcategories as $subcatid => $subcategory)
            {
                $processed_subcats[$subcatid]->link = $this->getCategoryURL($subcategory->catslug, $subcategory->section);
                $processed_subcats[$subcatid]->text = $this->_cleanTitle($subcategory->title);
                if ($this->is_show_article_count()) {
                    $count = '('.$subcategory->cnt.')';
                } else {
                    $count = '';
                }
                $processed_subcats[$subcatid]->count = $count;
                
                $j = 0;
                $processed_subcats[$subcatid]->articles = array();
                foreach($subcategory->articles as $article) {
                    $processed_subcats[$subcatid]->articles[$j]->link = $this->getArticleURL($article->slug, $article->catslug, $article->sectionid); 
                    $processed_subcats[$subcatid]->articles[$j]->text = $this->_cleanTitle($article->title);
                    $j++;
                }
                
                // Recursive
                $processed_subcats[$subcatid]->subcats = $this->_getProcessedSitemapSubcategoryRowsHelper($subcategory->subcats);
            }
        }
        
        return $processed_subcats;
    }
    
    function getProcessedSubcategoryRows(&$hierarchical_list)
    {
        $processed = array();
        
        $activecatid = $this->getCategoryID();
        $activeartid = $this->getArticleID();
        foreach($hierarchical_list as $catid => $root_category) 
        {
            $processed[$catid]->link = $this->getCategoryURL($root_category->catslug, $root_category->section);
            $processed[$catid]->text = $this->_cleanTitle($root_category->title);
            if ($this->is_show_article_count()) {
                $count = '('.$root_category->cnt.')';
            } else {
                $count = '';
            }
            $processed[$catid]->count = $count;
            
            if ($root_category->id == $activecatid) {
                $active = 'active';
            } else {
                $active = '';
            }
            $processed[$catid]->active = $active;
            
            if ($this->is_combine_on())
            {
                $processed[$catid]->articles = array();
                foreach($root_category->articles as $id => $article)
                {
                    $processed[$catid]->articles[$id]->link = $this->getArticleURL($article->slug, $article->catslug, $article->sectionid);
                    $processed[$catid]->articles[$id]->text = $this->_cleanTitle($article->title);
                    if ($article->id == $activeartid) {
                        $active = 'active';
                    } else {
                        $active = '';
                    }
                    $processed[$catid]->articles[$id]->active = $active;
                }
            }
            
            // Recursively add subcategories
            $processed[$catid]->subcats = $this->getProcessedSubcategoryRows($root_category->subcats);
        }    
        
        return $processed;
    }

// END PROCESSED ROWS SECTION
// BEGIN DISPLAYABLE LIST SECTION

    function getDisplayableSitemap(&$list)
    {
        $output = '';
        $output .= '<ul id="artcats_sitemap" class="artcats">';
        $output .= "<li><a id=\"{$this->getExpandableID()}ExpandAll\" class=\"expandAll\"  class=\"toggle\" href=\"#\">Expand All</a></li>";
        $output .= "<li><a id=\"{$this->getExpandableID()}CollapseAll\" class=\"collapseAll\" class=\"toggle\" href=\"#\">Collapse All</a></li>";
        
        foreach($list['sections'] as $sectionid => $section)
        {
            $output .= '<li class="sitemap">';
            $output .= '<img src="modules/mod_artcats/tmpl/img/contract.gif" class="toggle" />';
            $output .= "<a href=\"{$section->link}\" class=\"\">{$section->text}</a>";
            $output .= '<ul class="contract">';
            
            foreach ($list['categories'][$sectionid] as $category)
            {
                $output .= '<li class="sitemap">';
                $output .= '<img src="modules/mod_artcats/tmpl/img/contract.gif" class="toggle" />';
                $output .= "<a href=\"{$category->link}\" class=\"\">{$category->text} {$category->count}</a>";
                $output .= '<ul class="contract">';
                
                $output .= $this->_getDisplayableSitemapHelper($category->subcats);
                
                foreach ($category->articles as $article)
                {
                    $output .= "<li><a href=\"{$article->link}\">{$article->text}</a></li>";
                }
                
                $output .= '</ul>';
                $output .= '</li>';
            }
                
            $output .= '</ul>';        
        }
        
        $output .= '</ul>';
        
        return $output;
    }
    
    function _getDisplayableSitemapHelper(&$list)
    {
        $output = '';
        
        if (count($list))
        {
                
                foreach ($list as $category)
                {
                    $output .= '<li class="sitemap">';
                    $output .= '<img src="modules/mod_artcats/tmpl/img/contract.gif" class="toggle" />';
                    $output .= "<a href=\"{$category->link}\" class=\"\">{$category->text} {$category->count}</a>";
                    $output .= '<ul class="contract">';
                    
                    foreach ($category->articles as $article)
                    {
                        $output .= "<li><a href=\"{$article->link}\">{$article->text}</a></li>";
                    }
                    
                    $output .= '</ul>';
                    $output .= '</li>';
                }
        }
        
        return $output;
    }
    
    function getDisplayableExpandable(&$list)
    {
        $output = '';
        $output .= "<ul id=\"{$this->getExpandableID()}\" class=\"artcats\">";
        foreach($list as $root_category)
        {
            $output .= '<li class="expandable">';
            $output .= "<img src=\"modules/mod_artcats/tmpl/img/{$this->getExpandableToggle($root_category->active)}.gif\" class=\"toggle\" />";
            $output .= "<a href=\"{$root_category->link}\" class=\"{$root_category->active}\">{$root_category->text} {$root_category->count}</a>";
            $output .= "<ul class=\"{$this->getExpandableToggle($root_category->active)}\">";
            foreach ($root_category->subcats as $category)
            {
                $output .= '<li class="expandable">';
                $output .= "<img src=\"modules/mod_artcats/tmpl/img/{$this->getExpandableToggle($category->active)}.gif\" class=\"toggle\" />";
                $output .= "<a href=\"{$category->link}\" class=\"{$category->active}\">{$category->text} {$category->count}</a>";
                
                $output .= "<ul class=\"{$this->getExpandableToggle($category->active)}\">";
                
                $output .= $this->_getDisplayableExpandableHelper($category->subcats);
                
                foreach ($category->articles as $article)
                {
                    $output .= '<li class="">';
                    $output .= "<a href=\"{$article->link}\" class=\"{$article->active}\">";
                    $output .= $article->text;
                    $output .= '</a>';
                    $output .= '</li>';
                }
                $output .= '</ul>';
                
                $output .= "</li>";
                    
            }
            
            foreach ($root_category->articles as $root_article)
            {
                $output .= '<li class="">';
                $output .= "<a href=\"{$root_article->link}\" class=\"{$root_article->active}\">";
                $output .= $root_article->text;
                $output .= '</a>';
                $output .= '</li>';
            }
            
            $output .= '</ul>'; 
            
        }
        
        $output .= "<li><a id=\"{$this->getExpandableID()}ExpandAll\" class=\"expandAll\"  class=\"toggle\" href=\"#\">Expand All</a></li>";
        $output .= "<li><a id=\"{$this->getExpandableID()}CollapseAll\" class=\"collapseAll\" class=\"toggle\" href=\"#\">Collapse All</a></li>";
        $output .= '</ul>';
        
        return $output;    
    }
    
    function _getDisplayableExpandableHelper(&$list)
    {
        $output = '';
        if (count($list))
        {
            foreach($list as $root_category)
            {
                $output .= '<li class="expandable">';
                $output .= "<img src=\"modules/mod_artcats/tmpl/img/{$this->getExpandableToggle($root_category->active)}.gif\" class=\"toggle\" />";
                $output .= "<a href=\"{$root_category->link}\" class=\"{$root_category->active}\">{$root_category->text} {$root_category->count}</a>";
                $output .= "<ul class=\"{$this->getExpandableToggle($root_category->active)}\">";
                foreach ($root_category->subcats as $category)
                {
                    $output .= '<li class="expandable">';
                    $output .= "<img src=\"modules/mod_artcats/tmpl/img/{$this->getExpandableToggle($category->active)}.gif\" class=\"toggle\" />";
                    $output .= "<a href=\"{$category->link}\" class=\"{$category->active}\">{$category->text} {$category->count}</a>";
                    
                    $output .= "<ul class=\"{$this->getExpandableToggle($category->active)}\">";
                    
                    $output .= $this->_getDisplayableExpandableHelper($category->subcats);
                    
                    foreach ($category->articles as $article)
                    {
                        $output .= '<li class="">';
                        $output .= "<a href=\"{$article->link}\" class=\"{$article->active}\">";
                        $output .= $article->text;
                        $output .= '</a>';
                        $output .= '</li>';
                    }
                    $output .= '</ul>';
                    
                    $output .= "</li>";
                        
                }
                
                foreach ($root_category->articles as $root_article)
                {
                    $output .= '<li class="">';
                    $output .= "<a href=\"{$root_article->link}\" class=\"{$root_article->active}\">";
                    $output .= $root_article->text;
                    $output .= '</a>';
                    $output .= '</li>';
                }
                
                $output .= '</ul>'; 
                
            }
            
        }
        
        return $output; 
    }

    function getDisplayableMenustyle(&$list)
    {
    ?>
        <?php if (count($list)) : ?>
            <ul class="menu<?php echo $this->getClassSuffix(); ?> parent">
            <?php foreach ($list as $root_category) : ?>
            <li class="parent_menustyle menu<?php echo $this->getClassSuffix(); ?> <?php echo $root_category->active; ?>">
               <a href="<?php echo $root_category->link; ?>">
                   <?php echo $root_category->text;?> <?php echo $root_category->count; ?>
               </a>
               <ul class="menu<?php echo $this->getClassSuffix(); ?>">
               <?php foreach($root_category->subcats as $category) : ?>
                   <li class="parent_menustyle menu<?php echo $this->getClassSuffix(); ?> <?php echo $category->active; ?>">
                       <a href="<?php echo $category->link; ?>">
                           <?php echo $category->text;?> <?php echo $category->count; ?>
                       </a>
                       <?php $this->getDisplayableMenustyle($category->subcats); ?>
                   </li>
                   <?php if ($this->is_combine_on()) : ?>
                       <?php foreach($category->articles as $article) : ?>
                       <li class="parent_menustyle menu<?php echo $this->getClassSuffix(); ?> <?php echo $article->active; ?>">
                           <a href="<?php echo $article->link; ?>">
                               <?php echo $article->text;?>
                           </a>
                       </li>     
                       <?php endforeach; ?>
                   <?php endif; ?>
               <?php endforeach; ?>
               <?php if ($this->is_combine_on()) : ?>
                   <?php foreach ($root_category->articles as $root_article) : ?>
                   <li class="menu<?php echo $this->getClassSuffix(); ?> <?php echo $root_article->active; ?>">
                       <a href="<?php echo $root_article->link; ?>">
                           <?php echo $root_article->text;?>
                       </a>
                   </li>     
                   <?php endforeach; ?>
               <?php endif; ?>  
               </ul>
            </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php
    }
    
    function getDisplayableDefault(&$list)
    {
    ?>
        <?php if (count($list)) : ?> 
            <ul class="articles<?php echo $this->getClassSuffix(); ?>">
            <?php foreach ($list as $root_category) : ?>
            <li class="parent_default articles<?php echo $this->getClassSuffix(); ?> <?php echo $root_category->active; ?>">
               <a href="<?php echo $root_category->link; ?>">
                   <?php echo $root_category->text;?> <?php echo $root_category->count; ?>
               </a>
               <ul class="articles<?php echo $this->getClassSuffix(); ?>">
               <?php foreach($root_category->subcats as $category) : ?>
                   <li class="parent_default articles<?php echo $this->getClassSuffix(); ?> <?php echo $category->active; ?>">
                       <a href="<?php echo $category->link; ?>">
                           <?php echo $category->text;?> <?php echo $category->count; ?>
                       </a>
                       <?php $this->getDisplayableDefault($category->subcats); ?>
                   </li>
                   <?php if ($this->is_combine_on()) : ?>
                       <?php foreach($category->articles as $article) : ?>
                       <li class="parent_default articles<?php echo $this->getClassSuffix(); ?> <?php echo $article->active; ?>">
                           <a href="<?php echo $article->link; ?>">
                               <?php echo $article->text;?>
                           </a>
                       </li>     
                       <?php endforeach; ?>
                   <?php endif; ?>
               <?php endforeach; ?>
                 
               </ul>
            </li>
            <?php if ($this->is_combine_on()) : ?>
               <?php foreach ($root_category->articles as $root_article) : ?>
               <li class="articles<?php echo $this->getClassSuffix(); ?> <?php echo $root_article->active; ?>">
                   <a href="<?php echo $root_article->link; ?>">
                       <?php echo $root_article->text;?>
                   </a>
               </li>     
               <?php endforeach; ?>
            <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php
    }

// END DISPLAYABLE LIST SECTION
    
    function _cleanTitle($title)
    {
        if (version_compare(PHP_VERSION, '5.2.3', '>='))
        {
            return htmlspecialchars( $title, ENT_COMPAT, 'UTF-8', FALSE );
        }
        else
        {
            return htmlspecialchars( $title, ENT_COMPAT, 'UTF-8' );
        }
    }
}