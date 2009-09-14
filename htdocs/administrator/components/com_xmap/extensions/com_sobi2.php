<?php
/**
* @author Guillermo Vargas guille@vargas.co.cr
* @version $Id: com_sobi2.php 47 2009-08-02 02:47:16Z guilleva $
* @package xmap
* @license GNU/GPL
* @authorSite http://joomla.vargas.co.cr
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/** Adds support for Sobi2 categories to Xmap */
class xmap_com_sobi2 {

	/** Get the content tree for this kind of content */
	function &getTree( &$xmap, &$parent, &$params ) {
		$tree = array();

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = xmap_com_sobi2::getParam($link_vars,'catid',1);
		$entrieid = xmap_com_sobi2::getParam($link_vars,'sobi2Id',0);

		if ( $entrieid )
			return $tree;
		
		$include_entries = xmap_com_sobi2::getParam($params,'include_entries',1);
		$include_entries = ( $include_entries == 1
		                    || ( $include_entries == 2 && $xmap->view == 'xml')
				    		|| ( $include_entries == 3 && $xmap->view == 'html')
							||   $xmap->view == 'navigator');
		$params['include_entries'] = $include_entries;

		$priority = xmap_com_sobi2::getParam($params,'cat_priority',$parent->priority);
                $changefreq = xmap_com_sobi2::getParam($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
		$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = xmap_com_sobi2::getParam($params,'entry_priority',$parent->priority);
                $changefreq = xmap_com_sobi2::getParam($params,'entry_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['entry_priority'] = $priority;
		$params['entry_changefreq'] = $changefreq;

		xmap_com_sobi2::getCategoryTree($xmap, $parent, $catid, $params);
		return $tree;
	}

	/** SOBI2 support */
	function getCategoryTree( &$xmap, &$parent, $catid, &$params ) {
		$database =& JFactory::getDBO();

		$query  = 
		 "SELECT a.catid, a.name, b.parentid as pid "
		."\n FROM #__sobi2_categories AS a, #__sobi2_cats_relations AS b "
		."\n WHERE b.parentid=$catid"
	        ."   AND a.published=1 "
		."\n AND a.catid=b.catid "
		."\n ORDER BY a.ordering ASC";

		$database->setQuery($query);

		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		$modified = time();
		$xmap->changeLevel(1);
		foreach($rows as $row) {
			$node = new stdclass;
			$node->id = $parent->id;
			$node->uid = $parent->uid.'c'.$row->catid; // Unique ID
			$node->browserNav = $parent->browserNav;
			$node->name = html_entity_decode($row->name);
			$node->modified = $modified;
			$node->link = 'index.php?option=com_sobi2&amp;catid='.$row->catid;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			$xmap->printNode($node);
			xmap_com_sobi2::getCategoryTree($xmap, $parent, $row->catid, $params);
		}
		
		if ( $params['include_entries'] ) {
			$query  = 
		 	"SELECT a.itemid, a.title,UNIX_TIMESTAMP(a.last_update), b.catid "
			."\n FROM #__sobi2_item AS a, #__sobi2_cat_items_relations AS b"
			."\n WHERE a.published=1 "
			."\n AND b.catid = $catid"
			."\n AND a.approved=1 "
			."\n AND a.publish_up<=now() "
			."\n AND (a.publish_down>=now() or a.publish_down='0000-00-00 00:00:00' ) "
			."\n AND a.itemid=b.itemid "
			."\n ORDER BY a.ordering";


			$database->setQuery( $query );
			# echo $database->getQuery( );

			$rows = $database->loadObjectList();
			foreach($rows as $row) {
				$node = new stdclass;
				$node->id = $parent->id;
				$node->uid = $parent->uid.'e'.$row->itemid; // Unique ID
				$node->browserNav = $parent->browserNav;
				$node->name = html_entity_decode($row->title);
				$node->modified = $modified;
				$node->priority = $params['entry_priority'];
				$node->changefreq = $params['entry_changefreq'];
				$node->expandible = false;
				// &sobi2Task=sobi2Details&catid=2&sobi2Id=1&Itemid=31
				$node->link = 'index.php?option=com_sobi2&amp;sobi2Task=sobi2Details&amp;catid='.$row->catid . '&amp;sobi2Id=' . $row->itemid;
				$xmap->printNode($node);
			}

		}
		$xmap->changeLevel(-1);

	}

        function &getParam($arr, $name, $def) {
                $var = JArrayHelper::getValue( $arr, $name, $def, '' );
                return $var;
        }
}
