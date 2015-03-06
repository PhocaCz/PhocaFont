<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();
jimport( 'joomla.application.component.view' );
jimport('joomla.client.helper');
jimport('joomla.filesystem.file');
 
class PhocaFontCpViewPhocaFontFonts extends JViewLegacy
{

	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $ftp;


	function display($tpl = null) {
		
		$this->t	= PhocaFontUtils::setVars('font');
		// Check default value
		$model 	= $this->getModel( 'phocafontfonts' );
		$model->checkDefault();
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		foreach ($this->items as &$item) {
			$this->ordering[0][] = $item->id;
		}

		JHTML::stylesheet( $this->t['s'] );
		
		$this->ftp	=& JClientHelper::setCredentialsFromRequest('ftp');
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocafontcp.php';
		
		$state	= $this->get('State');
		$canDo	= PhocaFontHelperControlPanel::getActions($this->t);
		
		JToolBarHelper::title( JText::_('COM_PHOCAFONT_FONTS'), 'pencil-2' );
		
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::makeDefault('phocafontfont.setdefault', 'COM_PHOCAFONT_MAKE_DEFAULT');
			JToolBarHelper::divider();
		}
		
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew( 'phocafontfont.add','JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('phocafontfont.edit','JTOOLBAR_EDIT');
		}
		
		
		if ($canDo->get('core.edit.state')) {

			JToolBarHelper::divider();
			JToolBarHelper::custom('phocafontfonts.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('phocafontfonts.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);

		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList( JText::_( 'COM_PHOCAFONT_WARNING_DELETE_ITEMS' ), 'phocafontfont.delete', 'COM_PHOCAFONT_DELETE');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocafont', true );
	}
	
	protected function getSortFields() {
		return array(
			'a.ordering'	=> JText::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> JText::_($this->t['l'] . '_TITLE'),
			'a.published' 	=> JText::_($this->t['l'] . '_PUBLISHED'),
			'a.format' 		=> JText::_($this->t['l'] . '_FORMAT'),
			'a.id' 			=> JText::_('JGRID_HEADING_ID')
		);
	}
}
?>
