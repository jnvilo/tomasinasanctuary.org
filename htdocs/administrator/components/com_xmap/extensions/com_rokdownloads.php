<?php
/**
* @author Jan Moehrke, http://www.joomla-cbe.de
* @email info@joomla-cbe.de
* @version $Id: com_rokdownloads.php
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Rokdownloads component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );
error_reporting(0);
class xmap_com_rokdownloads {

	function getTree( &$xmap, &$parent, &$params) {

		$link_query = parse_url( $parent->link );
                parse_str( html_entity_decode($link_query['query']), $link_vars );
                $catid = intval(JArrayHelper::getValue($link_vars,'id',0));

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

		xmap_com_rokdownloads::getRokdownloadsTree($xmap, $parent, $params, $catid );

	}

	function getRokdownloadsTree ( &$xmap, &$parent, &$params, &$catid) {
		$db = &JFactory::getDBO();

		($catid)? $cats = xmap_com_rokdownloads::getDBFolders($catid):$cats = xmap_com_rokdownloads::getDBFolders();

		$xmap->changeLevel(1);
		foreach($cats as $cat) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'o'.$cat->id;
			$node->name = $cat->displayname? $cat->displayname : $cat->name;
			$node->priority   = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			$node->link = 'index.php?option=com_rokdownloads&amp;view=folder&amp;id=' . $cat->id . ":" . strtolower($cat->name);
			$node->tree = array();

			if ($xmap->printNode($node) !== FALSE) {
				xmap_com_rokdownloads::getRokdownloadsTree($xmap, $parent, $params, $cat->id);
			}

		}

		if ($params['include_files'] && $catid) {
			$files = xmap_com_rokdownloads::getDBFilesForFolder($catid);
			foreach($files as $file) {
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->uid  = $parent->uid .$catid . 'd' . $file->id;
				$node->name = ($file->displayname ? $file->displayname : $file->name);
				$node->link = 'index.php?option=com_rokdownloads&amp;view=file&amp;id='.$file->id . ":" . strtolower($file->name);
				$node->priority   = $params['file_priority'];
				$node->changefreq = $params['file_changefreq'];
				$node->expandible = false;
				//$node->tree = array();
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	}

	/* funktion von rokdownloads */
	function getDBFolders($parentFolderId = 1, $depth = 1){
		$db = &JFactory::getDBO();

		$query = "SELECT node.*, (COUNT(parent.name) - (sub_tree.depth + 1)) AS depth "
					."FROM #__rokdownloads AS node,#__rokdownloads AS parent,#__rokdownloads AS sub_parent,"
					."(SELECT node.id, (COUNT(parent.name) - 1) AS depth FROM #__rokdownloads AS node,#__rokdownloads AS parent "
					."WHERE node.lft BETWEEN parent.lft AND parent.rgt "
				."AND node.id = " . $parentFolderId . " "
				."GROUP BY node.id ORDER BY node.lft ) AS sub_tree "
				."WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.lft BETWEEN sub_parent.lft "
				."AND sub_parent.rgt AND sub_parent.id = sub_tree.id and node.folder = 1 "
					."GROUP BY node.id HAVING depth = $depth ORDER BY node.lft";
		$db->setQuery($query);
		$folders = $db->loadObjectList();
		return $folders;
	}

	/* funktion von rokdownloads */
	function getDBFilesForFolder($catid){
		$db = &JFactory::getDBO();
		$query = "SELECT node.*, (COUNT(parent.name) - (sub_tree.depth + 1)) AS depth "
				."FROM #__rokdownloads AS node,#__rokdownloads AS parent,#__rokdownloads AS sub_parent,"
				."(SELECT node.id, (COUNT(parent.name) - 1) AS depth FROM #__rokdownloads AS node,#__rokdownloads AS parent "
				."WHERE node.lft BETWEEN parent.lft AND parent.rgt "
		        ."AND node.id = " . $catid . " "
		        ."GROUP BY node.id ORDER BY node.lft ) AS sub_tree "
		        ."WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.lft BETWEEN sub_parent.lft "
		        ."AND sub_parent.rgt AND sub_parent.id = sub_tree.id and node.folder = 0 "
				."GROUP BY node.id HAVING depth = 1 ORDER BY node.lft";

		$db->setQuery($query);
		# echo $db->getQuery();
		$files = $db->loadObjectList();
		return $files;
	}

}
