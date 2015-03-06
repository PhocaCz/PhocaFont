<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modeladmin');

class PhocaFontCpModelPhocaFontFont extends JModelAdmin
{
	protected	$option 		= 'com_phocafont';
	protected 	$text_prefix	= 'com_phocafont';
	
	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		if ($record->id) {
			return $user->authorise('core.delete', 'com_phocafont.phocafontfont.'.(int) $record->id);
		} else {
			return parent::canDelete($record);
		}
	}
	
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if ($record->id) {
			return $user->authorise('core.edit.state', 'com_phocafont.phocafontfont.'.(int) $record->id);
		} else {
			return parent::canEditState($record);
		}
	}
	
	public function getTable($type = 'PhocaFontFont', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) {
		
		$app	= JFactory::getApplication();
		
		$form 	= $this->loadForm('com_phocafont.phocafontfont', 'phocafontfont', array('control' => 'jform', 'load_data' => $loadData));
		

		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_phocafont.edit.phocafontfont.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	protected function prepareTable(&$table) {
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		= JApplication::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = JApplication::stringURLSafe($table->title);
		}

		if (empty($table->id)) {
			// Set the values
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocafont_font');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
		}
	}
}
?>