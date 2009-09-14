<?php


defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
global $mosConfig_absolute_path;

class xmap_com_myblog {

  function GetPermalink($cid)
  {
     if (defined('JPATH_SITE')) {
        $database = &JFactory::getDBO();
     } else {
        global $database;
     }
     $query = "SELECT permalink from #__myblog_permalinks where contentid=".$cid;
     $database->setQuery($query);
     $row = $database->loadObjectList();
     return $row[0]->permalink;

  }

  function getMyBlog( &$xmap, &$parent,&$params )
  {
     if (defined('JPATH_SITE')) {
        $database = &JFactory::getDBO();
	$mosConfig_absolute_path =  JPATH_SITE;
	$my =  &JFactory::getUser();
     } else {
        global $database, $mosConfig_absolute_path, $my;
     }
     $my_id = $parent->id;

     require_once ($mosConfig_absolute_path . '/components/com_myblog/functions.myblog.php');
     require_once ($mosConfig_absolute_path . '/administrator/components/com_myblog/config.myblog.php');
     $_MY_CONFIG = new MYBLOG_Config();

     $managed_sections = $_MY_CONFIG->get('managedSections');

     // include popular bloggers by default
     $query = "SELECT created_by, sum(hits) AS hits from #__content  WHERE sectionid IN (".$managed_sections.") AND state=1 group by created_by order by hits desc";
     if ($params['number_of_bloggers'])
        $query .= " limit 0, ". $params['number_of_bloggers'];
     $database->setQuery($query);
     $rows = $database->loadObjectList();
     $modified = time();

     $xmap->changeLevel(1);

     $node = new stdclass;
     $node->browserNav = $parent->browserNav;
     $node->id = $parent->id;
     $node->uid = $parent->uid.'b';
     $node->priority = $params['blogger_priority'];
     $node->changefreq = $params['blogger_changefreq'];
     $node->name = $params["text_bloggers"];
     $node->link = "index.php?option=com_myblog&task=blogs";
     $node->modified = time();
     $node->expandible = true;
     $xmap->printNode($node);

     $xmap->changeLevel(1);
     foreach($rows as $row)
     {
       $node = new stdclass;
       $database->setQuery("SELECT username FROM #__users WHERE id='{$row->created_by}'");
       $username = $database->loadResult();

       if ($username) {
        $node->id = $parent->id;
        $node->uid = $parent->uid.'b'.$node->username;
        $node->priority = $params['blogger_priority'];
        $node->changefreq = $params['blogger_changefreq'];
        $node->browserNav = $parent->browserNav;
        $node->name = $username;
        $node->modified = $modified;
        $node->link = "index.php?option=com_myblog&blogger=".$node->name;
        $node->expandible = true;
        $xmap->printNode($node);
        if ($params['include_blogger_posts'] )
        {
           $sql = "SELECT id, title, created_by, modified from #__content where created_by=".$row->created_by.  " and sectionid in (".$managed_sections.") and
state=1 order by modified desc";
           if ($params['number_of_post_per_blogger'])
              $sql .= " limit 0, ". $params['number_of_post_per_blogger'];
           $res = $database->SetQuery($sql);
           $posts = $database->loadObjectList();
           $xmap->changeLevel(1);
           foreach ($posts as $post)
           {
              $permalink = xmap_com_myblog::GetPermalink($post->id);
              $node = new stdclass;
              $node->id = $post->id;
              $node->uid = $parent->uid.'p'.$post->id;
              $node->priority = $params['entry_priority'];
              $node->changefreq = $params['entry_changefreq'];
              $node->browserNav = $parent->browserNav;
              $node->modified = intval($post->modified);
              $node->name = $post->title;
              $node->link = "index.php?option=com_myblog&show=".$permalink."&Itemid=".$my_id;
              $node->expandible = false;
              $xmap->printNode($node);
           }
           $xmap->changeLevel(-1);
        }
       }
     }
     $xmap->changeLevel(-2);


     // retrieve tag clouds
     if ($params['include_tag_clouds'])
     {
        $node = new stdclass;
        $node->browserNav = $parent->browserNav;
        $node->id = $parent->id;
        $node->uid = $parent->uid.'t';
        $node->priority = $params['cats_priority'];
        $node->changefreq = $params['cats_changefreq'];
        $node->name = "Tag Clouds";
        $node->link = "index.php?option=com_myblog&task=categories";
        $node->expandible = true;
		$xmap->printNode($node);

        // http://archive/index.php?option=com_myblog&category=sports&Itemid=8

        $query = "SELECT * from #__myblog_categories";
        $database->setQuery($query);
        $tagrows = $database->loadObjectList();

        $tag_clouds=array();
        $j=count($tagrows);
        $i=0;
		$xmap->changeLevel(1);
        while ( $i<$j )
        {
           $node = new stdclass;
           $node->id = $parent->id;
           $node->uid = $parent->uid.'t'.$tagrows[$i]->name;
           $node->priority = $params['tag_priority'];
           $node->changefreq = $params['tag_changefreq'];
           $node->browserNav = $parent->browserNav;
           $node->name = $tagrows[$i]->name;
           $node->modified = $modified;
           $node->link = "index.php?option=com_myblog&category=".$node->name;
           $node->expandible = false;
			$xmap->printNode($node);

		   if ($params['include_feed']) {
	
	           	$node = new stdclass;
	           	$node->id = $parent->id;
	           	$node->uid = $parent->uid.'f'.$tagrows[$i]->name;
	           	$node->priority = $params['feed_priority'];
	           	$node->changefreq = $params['feed_changefreq'];
	           	$node->browserNav = $parent->browserNav;
	           	$node->name = $tagrows[$i]->name . ' Feed';
	           	$node->modified = $modified;
	           	$node->link = "index.php?option=com_myblog&category=".$tagrows[$i]->name. "&task=rss";
		   		$xmap->printNode($node);
		   }

           $i++;

        }
	$xmap->changeLevel(-1);
     }

     // time to retrieve archives now
     if ( $params['include_archives'] )
     {
        $query = 'SELECT DISTINCT (date_format(jc.created,"%M-%Y")) as archive FROM #__content as jc WHERE jc.sectionid IN('.$managed_sections.') and state = 1 ORDER BY jc.created DESC';
        $database->setQuery($query);
        $objList = $database->loadObjectList();
        foreach ($objList as $obj)
        {
           $node = new stdclass;
           $node->browserNav = $parent->browserNav;
           $node->id = $parent->id;
           $node->uid = $parent->uid.'a'.$obj->archive;
           $node->priority = $params['arc_priority'];
           $node->changefreq = $params['arc_changefreq'];
           $node->name = $obj->archive;
           $node->link = "index.php?option=com_myblog&archive=".$obj->archive;
           $node->expandible = false;
			$xmap->printNode($node);
        }

     }

     if ( $params['include_feed'] )
     {
     	$node = new stdclass;
     	$node->browserNav = $parent->browserNav;
     	$node->id = $parent->id;
     	$node->uid = $parent->uid.'f';
     	$node->priority = $params['feed_priority'];
     	$node->changefreq = $params['feed_changefreq'];
     	$node->name = 'Feed';
     	$node->link = "index.php?option=com_myblog&task=rss";
     	$node->modified = time();
     	$node->expandible = false;
     	$xmap->printNode($node);
     }
  }

  /**   Get   the   content   tree for this kind of content */
  function getTree( &$xmap, &$parent, &$params   )
  {

     $include_tag_clouds = xmap_com_myblog::getParam($params,'include_tag_clouds',1);
     $include_tag_clouds = ( $include_tag_clouds == 1
                                  || ( $include_tag_clouds == 2 && $xmap->view == 'xml')
                                  || ( $include_tag_clouds == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
     $params['include_tag_clouds'] = $include_tag_clouds;

     $include_archives = xmap_com_myblog::getParam($params,'include_archives',1);
     $include_archives = ( $include_archives == 1
                                  || ( $include_archives == 2 && $xmap->view == 'xml')
                                  || ( $include_archives == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
     $params['include_archives'] = $include_archives;

     $include_feed = xmap_com_myblog::getParam($params,'include_feed',1);
     $include_feed = ( $include_feed == 1
                                  || ( $include_feed == 2 && $xmap->view == 'xml')
                                  || ( $include_feed == 3 && $xmap->view == 'html'));
     $params['include_feed'] = $include_feed;

     $number_of_bloggers = intval(xmap_com_myblog::getParam($params,'number_of_bloggers',8));
     $params['number_of_bloggers'] = $number_of_bloggers;

     $text_bloggers = xmap_com_myblog::getParam($params,'text_bloggers','Bloggers');
     $params['text_bloggers'] = $text_bloggers;

     $include_blogger_posts = xmap_com_myblog::getParam($params,'include_blogger_posts',1);
     $include_blogger_posts = ( $include_blogger_posts == 1
                                  || ( $include_blogger_posts == 2 && $xmap->view == 'xml')
                                  || ( $include_blogger_posts == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
     $params['include_blogger_posts'] = $include_blogger_posts;

     $number_of_post_per_blogger = intval(xmap_com_myblog::getParam($params,'number_of_post_per_blogger',32));
     $params['number_of_post_per_blogger'] = $number_of_post_per_blogger;

     //----- Set tag_priority and tag_changefreq params
     $priority = xmap_com_content::getParam($params,'tag_priority',$parent->priority);
     $changefreq = xmap_com_content::getParam($params,'tag_changefreq',$parent->changefreq);
     if ($priority  == '-1')
             $priority = $parent->priority;
     if ($changefreq  == '-1')
             $changefreq = $parent->changefreq;

     $params['tag_priority'] = $priority;
     $params['tag_changefreq'] = $changefreq;

     //----- Set feed_priority and feed_changefreq params
     $priority = xmap_com_content::getParam($params,'feed_priority',$parent->priority);
     $changefreq = xmap_com_content::getParam($params,'feed_changefreq',$parent->changefreq);
     if ($priority  == '-1')
             $priority = $parent->priority;
     if ($changefreq  == '-1')
             $changefreq = $parent->changefreq;

     $params['feed_priority'] = $priority;
     $params['feed_changefreq'] = $changefreq;

     //----- Set cats_priority and cats_changefreq params
     $priority = xmap_com_content::getParam($params,'cats_priority',$parent->priority);
     $changefreq = xmap_com_content::getParam($params,'cats_changefreq',$parent->changefreq);
     if ($priority  == '-1')
             $priority = $parent->priority;
     if ($changefreq  == '-1')
             $changefreq = $parent->changefreq; 

     $params['cats_priority'] = $priority;
     $params['cats_changefreq'] = $changefreq;

     //----- Set blogger_priority and blogger_changefreq params
     $priority = xmap_com_content::getParam($params,'blogger_priority',$parent->priority);
     $changefreq = xmap_com_content::getParam($params,'blogger_changefreq',$parent->changefreq);
     if ($priority  == '-1')
             $priority = $parent->priority;
     if ($changefreq  == '-1')
             $changefreq = $parent->changefreq; 

     $params['blogger_priority'] = $priority;
     $params['blogger_changefreq'] = $changefreq;

     //----- Set entry_priority and entry_changefreq params
     $priority = xmap_com_content::getParam($params,'entry_priority',$parent->priority);
     $changefreq = xmap_com_content::getParam($params,'entry_changefreq',$parent->changefreq);
     if ($priority  == '-1')
             $priority = $parent->priority;
     if ($changefreq  == '-1')
             $changefreq = $parent->changefreq; 

     $params['entry_priority'] = $priority;
     $params['entry_changefreq'] = $changefreq;

     //----- Set arc_priority and arc_changefreq params
     $priority = xmap_com_content::getParam($params,'arc_priority',$parent->priority);
     $changefreq = xmap_com_content::getParam($params,'arc_changefreq',$parent->changefreq);
     if ($priority  == '-1')
             $priority = $parent->priority;
     if ($changefreq  == '-1')
             $changefreq = $parent->changefreq; 

     $params['arc_priority'] = $priority;
     $params['arc_changefreq'] = $changefreq;

     xmap_com_myblog::getMyBlog($xmap,  $parent, $params);
  }
  function &getParam($arr, $name, $def) {
	$var = JArrayHelper::getValue( $arr, $name, $def, '' );
	return $var;
  }
}
