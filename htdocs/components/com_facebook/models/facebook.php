<?php
/**
 * Facebook model + FBML Class
 *
 * 
 * @version		$Id: facebook.php 58 2008-03-23 05:23:39Z chalet16 $
 * @package 	Joomla-Facebook
 * @link 		http://joomlacode.org/gf/project/joomla-facebook/
 * @license		GNU/GPL, see LICENSE.php
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
 
class FBML extends JObject 
{
	var $_title;
	var $_menu;
	var $_content;
	var $_error;
	var $_page;
	function FBML($page='home') {
		global $mainframe;
		$this->_page=$page;
		JPluginHelper::importPlugin('facebook');
		$mainframe->triggerEvent('onFacebookPage',array($this->_page,&$this));
		$this->_title=ucfirst($page);
	}
	function getTitle() 
	{
		return $this->_title;
	}
	function setTitle($title) 
	{
			$this->_title=$title;
	}
	function addMenu($page,$label,$url='',$align='left',$priority=0)
	{
		if($url=='') {
			$url=$page;
		}
		if(strpos($url,'://')==false) {
			$url='http://apps.facebook.com/'.JoomlaFacebook::getAppname().'/'.$url;
		}
		$this->_menu[$align][$priority][$page]=array('label'=>$label,'url'=>$url);
	}
	function addContent($content,$priority=0) 
	{
		if($this->_content==null) {
			$this->_content=array();
		}
		@$this->_content[$priority].=$content;
	}
	function getFBML()
	{
		$this->addContent($this->getFBMLMenu(),-10);
		$this->addContent('<fb:title>'.$this->getTitle().'</fb:title>'."\n",-100);
		return $this->getFBMLContent();
	}
	function getFBMLMenu() 
	{
		global $mainframe;
		$out='<fb:tabs>'."\n";
		if($this->_menu) {
			foreach($this->_menu as $align=>$array) {
				ksort($array);
				foreach($array as $plist) {
					foreach($plist as $page=>$value) {
						$out.= '<fb:tab-item href="'.$value['url'].'" title="'.$value['label'].'" align="'.$align.'"';
						if($page==$this->_page) {
							$out.=' selected="true"';
						}
						$out.= ' />'."\n";
					}
				}
			}
		}
		$out.='</fb:tabs>'."\n";
		$mainframe->triggerEvent('onFacebookMenu',array(&$out,&$this->_menu));
		return $out;
	}
	function getFBMLContent()
	{
		$out='';
		if($this->_content) {
			ksort($this->_content);
			foreach($this->_content as $content) {
				$out.=$content;
			}
		}
		return $out;
	}

}
class FacebookModelFacebook extends JModel 
{
	function getContent() {
		$pagename = JRequest::getVar('page','home');
		if($pagename=='') {
			$pagename='home';
		}
		$page = new FBML($pagename);
		return $page->getFBML();
	}
}
