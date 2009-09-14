DROP TABLE IF EXISTS `#__fpss_categories`;
DROP TABLE IF EXISTS `#__fpss_slides`;

CREATE TABLE `#__fpss_categories` (
		`id` int(3) NOT NULL auto_increment,
		`name` varchar(225) NOT NULL,
		`width` int(11) NOT NULL,
		`quality` int(11) NOT NULL,
		`width_thumb` int(11) NOT NULL,
		`quality_thumb` int(11) NOT NULL,
		`published` tinyint(1) NOT NULL,
		PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `#__fpss_slides` (
		`id` int(11) unsigned NOT NULL auto_increment,
		`catid` int(11) NOT NULL,
		`name` varchar(225) NOT NULL default '',
		`path` varchar(225) NOT NULL default '',
		`path_type` varchar(110) NOT NULL default '',
		`thumb` varchar(225) NOT NULL,
		`state` tinyint(3) NOT NULL default '0',
		`publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
		`publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
		`itemlink` int(11) NOT NULL default '0',
		`menulink` int(11) NOT NULL default '0',
		`target` tinyint(3) NOT NULL default '0',
		`customlink` varchar(225) default NULL,
		`nolink` tinyint(1) NOT NULL,
		`ctext` text NOT NULL,
		`plaintext` text NOT NULL,
		`registers` tinyint(3) NOT NULL default '0',
		`showtitle` tinyint(3) NOT NULL default '0',
		`showseccat` tinyint(3) NOT NULL default '0',
		`showcustomtext` tinyint(3) NOT NULL default '0',
		`showplaintext` tinyint(3) NOT NULL default '0',
		`showreadmore` tinyint(3) NOT NULL default '0',  
		`ordering` int(11) NOT NULL default '0',
		PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `#__fpss_categories` (`id`, `name`, `width`, `quality`, `width_thumb`, `quality_thumb`, `published`) VALUES(1, 'Demo category', 400, 80, 100, 75, 1);

INSERT INTO `#__fpss_slides` (`id`, `catid`, `name`, `path`, `path_type`, `thumb`, `state`, `publish_up`, `publish_down`, `itemlink`, `menulink`, `target`, `customlink`, `nolink`, `ctext`, `plaintext`, `registers`, `showtitle`, `showseccat`, `showcustomtext`, `showplaintext`, `showreadmore`, `ordering`) VALUES (2, 1, 'Slide linked to menu item', 'components/com_fpss/images/2007_transformers_014_1.jpg', '1', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 34, 0, '', 0, 'This slide is linked to a Joomla! menu item. Click on it and you''ll get redirected to the article &quot;What''s New In 1.5?&quot;.', 'Image taken from the movie "Transformers"', 0, 1, 1, 1, 1, 1, 4);
INSERT INTO `#__fpss_slides` (`id`, `catid`, `name`, `path`, `path_type`, `thumb`, `state`, `publish_up`, `publish_down`, `itemlink`, `menulink`, `target`, `customlink`, `nolink`, `ctext`, `plaintext`, `registers`, `showtitle`, `showseccat`, `showcustomtext`, `showplaintext`, `showreadmore`, `ordering`) VALUES (3, 1, 'Slide linked to content item', 'components/com_fpss/images/the_kingdom_20070820114258369.jpg', '1', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 19, 0, 1, '', 0, 'This slide is linked to a regular URL. If you click on it you''ll get redirected to the main JoomlaWorks website. This option is really helpful for example when you want to use Frontpage Slideshow with VirtueMart to showcase your products.', 'Image taken from the movie "The Kingdom"', 0, 1, 1, 1, 1, 1, 3);
INSERT INTO `#__fpss_slides` (`id`, `catid`, `name`, `path`, `path_type`, `thumb`, `state`, `publish_up`, `publish_down`, `itemlink`, `menulink`, `target`, `customlink`, `nolink`, `ctext`, `plaintext`, `registers`, `showtitle`, `showseccat`, `showcustomtext`, `showplaintext`, `showreadmore`, `ordering`) VALUES (4, 1, 'Frontpage Slideshow is Search Engine Friendly!', 'components/com_fpss/images/TVD_TR_02183_2.jpg', '1', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 'http://www.frontpageslideshow.net/content/view/14/37/', 0, '<p>Unlike Flash based slideshows, <strong>Frontpage Slideshow</strong> uses unobtrusive javascript and some CSS wizardry only. The content of the slides is laid out as html code, which means it can be "read" by search engines. The <strong>proper usage</strong> (and order) of h1, h2, p (and more) tags will make sure <strong>Google</strong> (or any other search engine) regularly "scans" your latest/featured items. <strong>This is something Flash based solutions just can''t beat! ;)</strong></p>', 'Image taken from the "Invaders"', 0, 1, 0, 1, 1, 1, 2);
INSERT INTO `#__fpss_slides` (`id`, `catid`, `name`, `path`, `path_type`, `thumb`, `state`, `publish_up`, `publish_down`, `itemlink`, `menulink`, `target`, `customlink`, `nolink`, `ctext`, `plaintext`, `registers`, `showtitle`, `showseccat`, `showcustomtext`, `showplaintext`, `showreadmore`, `ordering`) VALUES (5, 1, 'Frontpage Slideshow v2.0', 'components/com_fpss/images/ao4.jpg', '1', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 'http://www.frontpageslideshow.net/content/view/12/37/', 0, 'The "<strong>Frontpage SlideShow</strong>" Component/Module pack is the most eye-catching way to display your featured articles, stories or even products in your Joomla! website, like Time.com, Joost.com or Yahoo! Movies do. "<strong>Frontpage SlideShow</strong>" creates an uber-cool slideshow with text snippets laying on top of images. These "slides" are being rotated one after the other with a nice fade effect. The slideshow features navigation and play/pause buttons, so the visitor has complete control over the slideshow''s "playback"! And best of all, Frontpage Slideshow can be skinned!', 'Image taken from the movie "Shoot ''em up"', 0, 1, 1, 1, 1, 1, 1);
