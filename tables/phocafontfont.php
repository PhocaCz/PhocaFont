<?php
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