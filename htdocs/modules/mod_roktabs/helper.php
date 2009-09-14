<?php
/**
* @version		$Id: helper.php 9764 2008-08-12 07:48:11Z djamil $
* @package		RocketTheme
* @copyright	Copyright (C) 2005 - 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class modRokTabsHelper
{
	
	function getList(&$params)
	{
		global $mainframe;

		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$userId		= (int) $user->get('id');

		$count		= 15; //hardcode a max of 15 as that is the max available to display
		$catid		= trim( $params->get('catid') );
		$secid		= trim( $params->get('secid') );
		$show_front	= $params->get('show_front', 1);
		$aid		= $user->get('aid', 0);
		
		$text_length = intval($params->get( 'preview_count', 200) );

		$contentConfig = &JComponentHelper::getParams( 'com_content' );
		$access		= !$contentConfig->get('shownoauth');

		$nullDate	= $db->getNullDate();

		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$where		= 'a.state = 1'
			. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
			. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
			;

		// User Filter
		switch ($params->get( 'user_id' ))
		{
			case 'by_me':
				$where .= ' AND (created_by = ' . (int) $userId . ' OR modified_by = ' . (int) $userId . ')';
				break;
			case 'not_me':
				$where .= ' AND (created_by <> ' . (int) $userId . ' AND modified_by <> ' . (int) $userId . ')';
				break;
		}

		// Ordering
		switch ($params->get( 'ordering' ))
		{
			case 'm_dsc':
				$ordering		= 'a.modified DESC, a.created DESC';
				break;
			case 'c_dsc':
			default:
				$ordering		= 'a.created DESC';
				break;
		}

		if ($catid)
		{
			$ids = explode( ',', $catid );
			JArrayHelper::toInteger( $ids );
			$catCondition = ' AND (cc.id=' . implode( ' OR cc.id=', $ids ) . ')';
		}
		if ($secid)
		{
			$ids = explode( ',', $secid );
			JArrayHelper::toInteger( $ids );
			$secCondition = ' AND (s.id=' . implode( ' OR s.id=', $ids ) . ')';
		}

		// Content Items only
		$query = 'SELECT a.*, a.introtext as text, ' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' .
			($show_front == '0' ? ' LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id' : '') .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
			' WHERE '. $where .' AND s.id > 0' .
			($access ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
			($catid ? $catCondition : '').
			($secid ? $secCondition : '').
			($show_front == '0' ? ' AND f.content_id IS NULL ' : '').
			' AND s.published = 1' .
			' AND cc.published = 1' .
			' ORDER BY '. $ordering;
		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();

		// Process the prepare content plugins
		    JPluginHelper::importPlugin('content');

		$i		= 0;
		$lists	= array();
		foreach ( $rows as $row )
		{

			$dispatcher   =& JDispatcher::getInstance();
			$results = $dispatcher->trigger('onPrepareContent', array (& $row, & $params, 0));
			
						
			$lists[$i]->id = $row->id;
			$lists[$i]->created = $row->created;
			$lists[$i]->modified = $row->modified;
			$lists[$i]->link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
			$lists[$i]->title = htmlspecialchars( $row->title );
			$lists[$i]->introtext = modRokTabsHelper::prepareContent( $row->text, $text_length);
			$i++;
		}
		
		return $lists;
	}
	
	function write_tabs($tabs, $tabs_position, &$list, $tabs_title, $tabs_incremental, $tabs_hideh6) {
		if ($tabs_position == 'hidden') $tabstyle = 'style="display: none;"'; 
		else $tabstyle = '';

		$return = '';
		$return .= "<div class='roktabs-links' $tabstyle>\n";
		$return .= "<ul class='roktabs-$tabs_position'>\n";
				$tabs = intval($tabs);

				if ($tabs == 0 || $tabs > count($list)) $tabs = count($list);
								
				for($i = 0; $i < $tabs; $i++) {
					if ($list[$i]->introtext != '') {
						$class = '';
						if (!$i) $class = 'class="first active"';
						if ($i == $tabs - 1) $class = 'class="last"';

						switch($tabs_title) {
							case 'incremental':
								if (strlen($tabs_incremental) > 0) $title = $tabs_incremental . '' . ($i + 1);
								else $title = $tabs_incremental . '' . ($i + 1);						
								break;
							case 'h6':
								$regex = '/<h6(?:.+)?>(.+?)<\/h6>/is';
								preg_match($regex, $list[$i]->introtext, $matches);
								if (count($matches) && strlen($matches[1]) > 0) {
									$title = $matches[1];
									if ($tabs_hideh6 == "1") {
										$list[$i]->introtext = str_replace($matches[0], '', $list[$i]->introtext);
									}
									break; 
								}
							default: 
								$title = $list[$i]->title;
						}

						$return .= "<li $class><span>$title</span></li>\n";
					}
				}

		$return .= "</ul>\n";
		$return .= "</div>\n";


		return $return;
	}
	
	function prepareContent( $text, $length=200 ) {
		// strips tags won't remove the actual jscript
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );

		// cut off text at word boundary if required
		if (strlen($text) > $length) {
			//$text = substr($text, 0, strpos($text, ' ', $length)) . "..." ;
		} 
		return $text;
	}
}
