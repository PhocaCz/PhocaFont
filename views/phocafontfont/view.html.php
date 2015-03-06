<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class phocaFontCpViewPhocaFontFont extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	
	public function display($tpl = null) {
		
		$this->t		= PhocaFontUtils::setVars('font');
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		
		JHTML::stylesheet( $this->t['s'] );

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocafontcp.php';
		JRequest::setVar('hidemainmenu', true);
		$bar 		= JToolBar::getInstance('toolbar');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaFontHelperControlPanel::getActions($this->t, $this->item->id);
		$paramsC 	= JComponentHelper::getParams('com_phocafont');

		

		$text = $isNew ? JText::_( 'COM_PHOCAFONT_NEW' ) : JText::_('COM_PHOCAFONT_EDIT');
		JToolBarHelper::title(   JText::_( 'COM_PHOCAFONT_FONT' ).': <small><small>[ ' . $text.' ]</small></small>' , 'pencil-2');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolBarHelper::apply('phocafontfont.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('phocafontfont.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::addNew('phocafontfont.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolBarHelper::custom('phocafontc.save2copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('phocafontfont.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('phocafontfont.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocafont', true );
	}
}
?>
