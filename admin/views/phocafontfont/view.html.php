<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
jimport( 'joomla.application.component.view' );

class phocaFontCpViewPhocaFontFont extends HtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;

	public function display($tpl = null) {

		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');

		$this->t		= PhocaFontUtils::setVars('font');
		$this->r	= new PhocaFontRenderAdminview();

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocafontcp.php';
		Factory::getApplication()->input->set('hidemainmenu', true);
		$bar 		= Toolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaFontHelperControlPanel::getActions($this->t, $this->item->id);
		$paramsC 	= ComponentHelper::getParams('com_phocafont');



		$text = $isNew ? Text::_( 'COM_PHOCAFONT_NEW' ) : Text::_('COM_PHOCAFONT_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAFONT_FONT' ).': <small><small>[ ' . $text.' ]</small></small>' , 'pencil-2');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocafontfont.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocafontfont.save', 'JToolbar_SAVE');
			ToolbarHelper::addNew('phocafontfont.save2new', 'JToolbar_SAVE_AND_NEW');

		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolbarHelper::custom('phocafontc.save2copy', 'copy.png', 'copy_f2.png', 'JToolbar_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocafontfont.cancel', 'JToolbar_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocafontfont.cancel', 'JToolbar_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocafont', true );
	}
}
?>
