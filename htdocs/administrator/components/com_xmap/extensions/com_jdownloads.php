<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_jdownloads.php 47 2009-08-02 02:47:16Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Remository component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_jdownloads {

	function getTree( &$xmap, &$parent, &$params) {
		if ( strpos($parent->link, 'task=view.download') ) {
			return $list;	
		}

		$link_query = parse_url( $parent->link );
                parse_str( html_entity_decode($link_query['query']), $link_vars );
                $catid = JArrayHelper::getValue($link_vars,'catid',0);

		$include_files = JArrayHelper::getValue( $params, 'include_files',1,'' );
		$include_files = ( $include_files == 1
                                  || ( $include_files == 2 && $xmap->view == 'xml') 
                                  || ( $include_files == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
		$params['include_files'] = $include_files;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'file_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'file_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['file_priority'] = $priority;
		$params['file_changefreq'] = $changefreq;

		if ( $include_files ) {
			$params['limit'] = '';
			$params['days'] = '';
			$limit = JArrayHelper::getValue($params,'max_files','','');

			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = JArrayHelper::getValue($params,'max_age','','');
			if ( intval($days) )
				$params['days'] = ' AND filedate >= \''.date('Y-m-d H:m:s', ($xmap->now - ($days*86400)) ) ."' ";
		}

		xmap_com_jdownloads::getJDownloadsTree( $xmap, $parent, $params, $catid );
	}

	function getJDownloadsTree ( &$xmap, &$parent, &$params, &$catid ) {
		$db = JFactory::getDBO();

		$db->setQuery("select cat_id, cat_title, parent_id from #__jdownloads_cats where parent_id=$catid and published = '1' order by cat_title");
		$cats = $db->loadObjectList();
		$xmap->changeLevel(1);

		foreach($cats as $cat) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'c'.$cat->cat_id;   // Uniq ID for the category
			$node->pid  = $cat->parent_id;
			$node->name = $cat->cat_title? $cat->cat_title : $cat->cat_title;
			$node->priority   = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->link = 'index.php?option=com_jdownloads&amp;task=viewcategory&amp;catid='.$cat->cat_id;
			$node->expandible = true;

			if ($xmap->printNode($node) !== FALSE ) {
				xmap_com_jdownloads::getJDownloadsTree($xmap, $parent, $params, $cat->cat_id);
			}
		}

		if ( $params['include_files'] ) {
			$db->setQuery ("select file_id, file_title, cat_id from #__jdownloads_files where cat_id IN ($catid) and published = '1' order by file_title");
			$cats = $db->loadObjectList();
			foreach($cats as $file) {
				$node = new stdclass;
				$node->id   = $parent->id;  // Itemid
				$node->uid  = $parent->uid .'d'.$file->file_id; // Uniq ID for the download
				$node->name = ($file->file_title ? $file->file_title : $file->file_title);
				$node->link = 'index.php?option=com_jdownloads&amp;task=view.download&amp;cid='.$file->file_id;
				$node->priority   = $params['file_priority'];
				$node->changefreq = $params['file_changefreq'];
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	}

}
