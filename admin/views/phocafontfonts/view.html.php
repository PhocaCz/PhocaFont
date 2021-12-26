<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );
jimport('joomla.client.helper');
jimport('joomla.filesystem.file');

class PhocaFontCpViewPhocaFontFonts extends HtmlView
{

	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $ftp;
	protected $r;
	public $filterForm;
	public $activeFilters;


	function display($tpl = null) {


		// Check default value
		$model 	= $this->getModel( 'phocafontfonts' );
		$model->checkDefault();

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->r = new PhocaFontRenderAdminViews();
		$this->t = PhocaFontUtils::setVars('font');

		foreach ($this->items as &$item) {
			$this->ordering[0][] = $item->id;
		}

		$this->ftp	= ClientHelper::setCredentialsFromRequest('ftp');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocafontcp.php';

		$state	= $this->get('State');
		$canDo	= PhocaFontHelperControlPanel::getActions($this->t);

		ToolbarHelper::title( Text::_('COM_PHOCAFONT_FONTS'), 'pencil-2' );

		if ($canDo->get('core.edit.state')) {
			ToolbarHelper::makeDefault('phocafontfont.setdefault', 'COM_PHOCAFONT_MAKE_DEFAULT');
			ToolbarHelper::divider();
		}

		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew( 'phocafontfont.add','JToolbar_NEW');
		}
		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('phocafontfont.edit','JToolbar_EDIT');
		}


		if ($canDo->get('core.edit.state')) {

			ToolbarHelper::divider();
			ToolbarHelper::custom('phocafontfonts.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			ToolbarHelper::custom('phocafontfonts.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);

		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList( Text::_( 'COM_PHOCAFONT_WARNING_DELETE_ITEMS' ), 'phocafontfont.delete', 'COM_PHOCAFONT_DELETE');
		}
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocafont', true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> Text::_($this->t['l'] . '_TITLE'),
			'a.published' 	=> Text::_($this->t['l'] . '_PUBLISHED'),
			'a.format' 		=> Text::_($this->t['l'] . '_FORMAT'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}
?>
