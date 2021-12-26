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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Client\ClientHelper;
jimport('joomla.client.helper');
jimport('joomla.application.component.controllerform');

class PhocaFontCpControllerPhocaFontFont extends FormController
{
	protected	$option 		= 'com_phocafont';

	function __construct() {
		parent::__construct();
	}

	protected function allowAdd($data = array()) {
		$user		= Factory::getUser();
		$allow		= null;
		$allow	= $user->authorise('core.create', 'com_phocafont');
		if ($allow === null) {
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id') {
		$user		= Factory::getUser();
		$allow		= null;
		$allow	= $user->authorise('core.edit', 'com_phocafont');
		if ($allow === null) {
			return parent::allowEdit($data, $key);
		} else {
			return $allow;
		}
	}


	function delete() {

		$app	= Factory::getApplication();
		$cid	= Factory::getApplication()->input->get('cid', array());
		//$cid 	= JFactory::getApplication()->input->get( 'cid', array(), '', 'array' );// POST (Icon), GET (Small Icon)

		ArrayHelper::toInteger($cid);

		if (count($cid ) < 1) {

			throw new Exception(Text::_( 'COM_PHOCAFONT_SELECT_ITEM_TO_DELETE' ) , 500);
			return false;
		}

		$model 	= $this->getModel( 'phocafontfonts' );

		$errorMsg = '';
		$link = 'index.php?option=com_phocafont&view=phocafontfonts';
 		if(!$model->delete($cid, $errorMsg)) {
			$msg = Text::_( 'COM_PHOCAFONT_ERROR_DELETING_FONT' ) . '<br />' . $errorMsg;
			$this->setRedirect( $link, $msg, 'error');
		}
		else {
			$msg = Text::_( 'COM_PHOCAFONT_SUCCESS_DELETING_FONT' );
			$this->setRedirect( $link, $msg );
		}


	}

	function install() {
		// Check for request forgeries
		Session::checkToken() or die( 'Invalid Token' );
		$post = Factory::getApplication()->input->get('post');
		$ftp = ClientHelper::setCredentialsFromRequest('ftp');
		$model	= $this->getModel( 'phocafontfonts' );

		$errorMsg = '';
		$link = 'index.php?option=com_phocafont&view=phocafontfonts';
		if (!$model->install($errorMsg)) {
			$msg = Text::_('COM_PHOCAFONT_ERROR_NEW_FONT_NOT_INSTALLED') . '<br />'. $errorMsg;
			$this->setRedirect( $link, $msg );
		} else {
			$cache = Factory::getCache('mod_menu');
			$cache->clean();
			$msg = Text::_('COM_PHOCAFONT_SUCCESS_NEW_FONT_INSTALLED');
			$this->setRedirect( $link, $msg );
		}
	}

	function setdefault() {
		// Check for request forgeries
		Session::checkToken() or jexit( 'COM_PHOCAFONT_INVALID_TOKEN' );
		$app	= Factory::getApplication();
		$cid	= Factory::getApplication()->input->get('cid', array());
		//$cid 	= JFactory::getApplication()->input->get( 'cid', array(), '', 'array' );// POST (Icon), GET (Small Icon)
		ArrayHelper::toInteger($cid);

		$link = 'index.php?option=com_phocafont&view=phocafontfonts';
		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( $link, Text::_('COM_PHOCAFONT_ERROR_NO_FONT_SELECTED') );
			return false;
		}


		$model 	= $this->getModel( 'phocafontfonts' );

		if (!$model->isDefaultPublished($id)) {
			$this->setRedirect( $link, Text::_('COM_PHOCAFONT_ERROR_DEFAULT_FONT_MUST_BE_PUBLISHED') );
			return false;
		}

		if ($model->setDefault($id)) {
			$msg = Text::_( 'COM_PHOCAFONT_SUCCESS_DEFAULT_FONT_SET' );
		} else {
			$msg = $model->getError();
		}
		$this->setRedirect( $link, $msg );
	}

}
?>
