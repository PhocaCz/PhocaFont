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
defined('JPATH_BASE') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
jimport('joomla.form.formfield');

class JFormFieldPhocaSelectFont extends FormField
{
	public $type = 'PhocaSelectFont';

	protected function getInput() {

		$db = Factory::getDBO();

		$fontId	= isset($this->element['fontid']) ? (string) $this->element['fontid'] : '';
		switch($fontId) {
			case '03':
				$font = 'font_03';
			break;
			case '02':
			default:
				$font = 'font_02';
			break;
		}

		$query = 'SELECT a.title AS '.$font.', a.id AS value'
		. ' FROM #__phocafont_font AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';

		$db->setQuery( $query );
		$items = $db->loadObjectList();

		array_unshift($items, HTMLHelper::_('select.option', '0', '- '.Text::_('COM_PHOCAFONT_SELECT_FONT').' -', 'value', $font));

		return HTMLHelper::_('select.genericlist',  $items, $this->name, 'class="form-select"', 'value', $font, $this->value, $this->id );

	}
}
