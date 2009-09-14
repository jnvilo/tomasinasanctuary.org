<?php
/**
 * @version $Id$
 * @package RocketWerx
 * @subpackage	RokNavMenu
 * @copyright Copyright (C) 2009 RocketWerx. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a file list from a directory in the current templates directory
 */

class JElementTemplateFilelist extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'TemplateFilelist';

	function fetchElement($name, $value, &$node, $control_name)
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		// path to images directory
		$path		= JPATH_ROOT.DS.'templates'.DS.$this->_getFrontSideTemplate().DS.$node->attributes('directory');
		if (JFolder::exists($path)) { 
			$filter		= $node->attributes('filter');
			$exclude	= $node->attributes('exclude');
			$stripExt	= $node->attributes('stripext');
			$files		= JFolder::files($path, $filter);
	
			$options = array ();
	
			if (!$node->attributes('hide_none'))
			{
				$options[] = JHTML::_('select.option', '-1', '- '.JText::_('Do not use').' -');
			}
	
			if (!$node->attributes('hide_default'))
			{
				$options[] = JHTML::_('select.option', '', '- '.JText::_('Use default').' -');
			}
	
			if ( is_array($files) )
			{
				foreach ($files as $file)
				{
					if ($exclude)
					{
						if (preg_match( chr( 1 ) . $exclude . chr( 1 ), $file ))
						{
							continue;
						}
					}
					if ($stripExt)
					{
						$file = JFile::stripExt( $file );
					}
					$options[] = JHTML::_('select.option', $file, $file);
				}
			}
	
			return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, "param$name");
		}
		else {
			return JText::_("MSG.NO_TEMPLATE_FORMATERS");
		}
	}
	
	function _getFrontSideTemplate() {
		$db =& JFactory::getDBO();
		// Get the current default template
		$query = ' SELECT template '
				.' FROM #__templates_menu '
				.' WHERE client_id = 0 '
				.' AND menuid = 0 ';
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();
		return $defaultemplate;
	}
}
