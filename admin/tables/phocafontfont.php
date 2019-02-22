<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
class TablePhocaFontFont extends JTable
{

	function __construct( &$db ) {
		parent::__construct( '#__phocafont_font', 'id', $db );
	}
	
	function check()
	{
		if (empty($this->title)) {
			$this->setError(JText::_('COM_PHOCAFONT_WARNING_FONT_MUST_HAVE_TITLE'));
			return false;
		}
		
		$this->title = str_replace('+', ' ', $this->title);


		/*
		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {
			$this->alias = JFactory::getDate()->format("%Y-%m-%d-%H-%M-%S");
		}*/
		
		return true;
	}
}
?>