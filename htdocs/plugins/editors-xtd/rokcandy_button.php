<?php
/**
* @version		$Id: rokcandy_button.php 10906 2008-09-05 07:27:34Z rhuk $
* @package		RokCandy
* @copyright	Copyright (C) 2008 - 2009 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgButtonRokCandy_Button extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgButtonRokCandy_Button(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onDisplay($name)
	{
		global $mainframe;
		
		$button = new JObject();
		
		$doc = & JFactory::getDocument();
		/* @var $doc JDocumentHTML */
		
		
		$declaration	= 
		"function jSelectArticle(id, title, object) {
			var content 		= tinyMCE.getContent();
			
			var articlehref = 'index.php?option=com_content&view=article&id='+id;
			var articlelink = ' <a href=\"'+articlehref+'\">'+title+'</a> ';

			jInsertEditorText( articlelink, 'text' );
			document.getElementById('sbox-window').close();
		}
	";
		
		
		$doc->addScriptDeclaration($declaration);
		
		$declaration	="
		.button2-left .linkmacro 	{ background: url(components/com_rokcandy/assets/button.png) 100% 0 no-repeat; } ";
		
		$doc->addStyleDeclaration($declaration);
		
		$template = $mainframe->getTemplate();

		$link = 'index.php?option=com_rokcandy&task=list&tmpl=component&object=id';

		JHTML::_('behavior.modal');

		
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('RokCandy Macros'));
		$button->set('name', 'linkmacro');
		$button->set('options', "{handler: 'iframe', size: {x: 700, y: 400}}");

		return $button;
	}
}