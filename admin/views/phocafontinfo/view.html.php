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

class PhocaFontCpViewPhocaFontInfo extends JViewLegacy
{
	protected $t;
	
	public function display($tpl = null) {

		$this->t	= PhocaFontUtils::setVars('info');
		JHTML::stylesheet( $this->t['s'] );
		$this->t['version'] = PhocaFontHelper::getPhocaVersion('com_phocafont');
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/phocafontcp.php';

		$state	= $this->get('State');
		$canDo	= PhocaFontHelperControlPanel::getActions($this->t);
	
		JToolbarHelper::title( JText::_( 'COM_PHOCAFONT_PF_INFO' ), 'info' );
		
		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = JToolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocafont" class="btn btn-small"><i class="icon-home-2" title="'.JText::_($this->t['l'].'_CONTROL_PANEL').'"></i> '.JText::_($this->t['l'].'_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_phocafont');
			JToolbarHelper::divider();
		}
		
		JToolbarHelper::help( 'screen.phocafont', true );
	}
}
?>
