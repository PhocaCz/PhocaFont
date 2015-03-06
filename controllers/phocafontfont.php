<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
jimport('joomla.client.helper');
jimport('joomla.application.component.controllerform');

class PhocaFontCpControllerPhocaFontFont extends JControllerForm
{
	protected	$option 		= 'com_phocafont';
	
	function __construct() {
		parent::__construct();
	}
	
	protected function allowAdd($data = array()) {
		$user		= JFactory::getUser();
		$allow		= null;
		$allow	= $user->authorise('core.create', 'com_phocafont');
		if ($allow === null) {
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id') {
		$user		= JFactory::getUser();
		$allow		= null;
		$allow	= $user->authorise('core.edit', 'com_phocafont');
		if ($allow === null) {
			return parent::allowEdit($data, $key);
		} else {
			return $allow;
		}
	}

	
	function delete() {
		
		$cid 	= JRequest::getVar( 'cid', array(), '', 'array' );// POST (Icon), GET (Small Icon)
	
		JArrayHelper::toInteger($cid);
	
		if (count($cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_PHOCAFONT_SELECT_ITEM_TO_DELETE' ) );
		}
		
		$model 	= $this->getModel( 'phocafontfonts' );
		
		$errorMsg = '';
		$link = 'index.php?option=com_phocafont&view=phocafontfonts';
 		if(!$model->delete($cid, $errorMsg)) {
			$msg = JText::_( 'COM_PHOCAFONT_ERROR_DELETING_FONT' ) . '<br />' . $errorMsg;
			$this->setRedirect( $link, $msg, 'error');
		}
		else {
			$msg = JText::_( 'COM_PHOCAFONT_SUCCESS_DELETING_FONT' );
			$this->setRedirect( $link, $msg );
		}

		
	}
	
	function install() {
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		$post = JRequest::get('post');
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$model	= &$this->getModel( 'phocafontfonts' );
		
		$errorMsg = '';
		$link = 'index.php?option=com_phocafont&view=phocafontfonts';
		if (!$model->install($errorMsg)) {
			$msg = JText::_('COM_PHOCAFONT_ERROR_NEW_FONT_NOT_INSTALLED') . '<br />'. $errorMsg;
			$this->setRedirect( $link, $msg );
		} else {
			$cache = &JFactory::getCache('mod_menu');
			$cache->clean();
			$msg = JText::_('COM_PHOCAFONT_SUCCESS_NEW_FONT_INSTALLED');
			$this->setRedirect( $link, $msg );
		}
	}
	
	function setdefault() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'COM_PHOCAFONT_INVALID_TOKEN' );
		$cid 	= JRequest::getVar( 'cid', array(), '', 'array' );// POST (Icon), GET (Small Icon)
		JArrayHelper::toInteger($cid);

		$link = 'index.php?option=com_phocafont&view=phocafontfonts';
		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( $link, JText::_('COM_PHOCAFONT_ERROR_NO_FONT_SELECTED') );
			return false;
		}


		$model 	= $this->getModel( 'phocafontfonts' );
		
		if (!$model->isDefaultPublished($id)) {
			$this->setRedirect( $link, JText::_('COM_PHOCAFONT_ERROR_DEFAULT_FONT_MUST_BE_PUBLISHED') );
			return false;
		}
		
		if ($model->setDefault($id)) {
			$msg = JText::_( 'COM_PHOCAFONT_SUCCESS_DEFAULT_FONT_SET' );
		} else {
			$msg = $model->getError();
		}
		$this->setRedirect( $link, $msg );
	}
	
}
?>
