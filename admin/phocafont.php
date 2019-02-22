<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;

if (!JFactory::getUser()->authorise('core.manage', 'com_phocafont')) {
	throw new Exception(JText::_('COM_PHOCACART_ERROR_ALERTNOAUTHOR'), 404);
	return false;
}
// Require the base controller and helpers
require_once( JPATH_COMPONENT.'/controller.php' );
jimport('joomla.filesystem.folder');
require_once( JPATH_COMPONENT.'/helpers/phocafontrenderadmin.php' );
require_once( JPATH_COMPONENT.'/helpers/phocafontutils.php' );
require_once( JPATH_COMPONENT.'/helpers/phocafont.php' );
require_once( JPATH_COMPONENT.'/helpers/phocafontcp.php' );
require_once( JPATH_COMPONENT.'/helpers/phocafontrender.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminview.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminviews.php' );
jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaFontCp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>