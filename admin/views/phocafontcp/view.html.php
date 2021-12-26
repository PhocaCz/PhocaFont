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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );

class phocaFontCpViewPhocaFontCp extends HtmlView
{
	protected $t;

	public function display($tpl = null) {

		$this->t	= PhocaFontUtils::setVars('cp');
		$this->r	= new PhocaFontRenderAdminview();
		$i = ' icon-';
		$d = 'duotone ';

		$this->views= array(
		'fonts'		=> array($this->t['l'] . '_FONTS', $d.$i.'multilingual', '#9900CC'),
		'info'		=> array($this->t['l'] . '_INFO', $d.$i.'info-circle', '#3378cc')
		);
		$this->t['version'] = PhocaFontHelper::getPhocaVersion('com_phocafont');
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocafontcp.php';

		$state	= $this->get('State');
		$canDo	= PhocaFontHelperControlPanel::getActions($this->t);
		ToolbarHelper::title( Text::_( 'COM_PHOCAFONT_PF_CONTROL_PANEL' ), 'home-2 cpanel' );

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocafont" class="btn btn-small"><i class="icon-home-2" title="'.Text::_($this->t['l'].'_CONTROL_PANEL').'"></i> '.Text::_($this->t['l'].'_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_phocafont');
			ToolbarHelper::divider();
		}

		ToolbarHelper::help( 'screen.phocafont', true );
	}
}
?>
