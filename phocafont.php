<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if (!JFactory::getUser()->authorise('core.manage', 'com_phocafont')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
// Require the base controller and helpers
require_once( JPATH_COMPONENT.DS.'controller.php' );
jimport('joomla.filesystem.folder');
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocafontrenderadmin.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocafontutils.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocafont.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocafontcp.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocafontrender.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadminview.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadminviews.php' );
jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaFontCp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>