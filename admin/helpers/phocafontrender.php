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
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
class PhocaFontRender
{
	function quickIconButton( $component, $link, $image, $text ) {
		
		$lang	= Factory::getLanguage();
		$button = '';
		if ($lang->isRTL()) {
			$button .= '<div class="icon-wrapper">';
		} else {
			$button .= '<div class="icon-wrapper">';
		}
		$button .=	'<div class="icon">'
				   .'<a href="'.$link.'">'
				   .HTMLHelper::_('image', 'administrator/components/'.$component.'/assets/images/'.$image, $text )
				   .'<span>'.$text.'</span></a>'
				   .'</div>';
		$button .= '</div>';

		return $button;
	}
	
	function renderFormInput($name, $title, $value, $size = 50, $maxlength = 250, $attribute = '', $style = '') {
		
		$styleOutput = '';
		if ($style != '') {
			$styleOutput = 'style="'.$style.'"';
		}
		
		$output = '<tr>'
				 .'<td width="100" align="right" class="key">'
				 .'<label for="'.$name.'">'.Text::_($title).':</label>'
				 .'</td><td>'
				 .'<input class="text_area" type="text" name="'.$name.'" id="'.$name.'" size="'.$size.'" maxlength="'.$maxlength.'" value="'.$value.'" '.$attribute.' '.$styleOutput.' />'
				.'</td></tr>';
		return $output;
	}
	
	function renderFormTextArea($name, $title, $value, $cols = 60, $rows = 5, $style = '') {
		
		$styleOutput = '';
		if ($style != '') {
			$styleOutput = 'style="'.$style.'"';
		}
		
		$output = '<tr>'
				 .'<td width="100" align="right" class="key">'
				 .'<label for="'.$name.'">'.Text::_($title).':</label>'
				 .'</td><td>'
				 .'<textarea class="text_area" cols="'.$cols.'" rows="'.$rows.'" name="'.$name.'" id="'.$name.'" '.$styleOutput.'>'.$value.'</textarea>'
				.'</td></tr>';
		return $output;
	}

	
	function renderFormItemSpecial($name, $title, $special) {
		
		$output = '<tr>'
				 .'<td width="100" align="right" class="key">'
				 .'<label for="'.$name.'">'.Text::_($title).':</label>'
				 .'</td><td>'
				 . $special
				 .'</td></tr>';
		return $output;
	}
	
	
	
	function renderFormStyle() {
	
		$output = '<style type="text/css">'
				.'table.paramlist td.paramlist_key {'
				.'width: 92px;'
				.'text-align: left;'
				.'height: 30px;'
				.'}'
				.'</style>';
		return $output;
	}
	
	function renderFTPaccess() {
	
		$ftpOutput = '<fieldset title="'.Text::_('COM_PHOCAFONT_FTP_LOGIN_LABEL'). '">'
		.'<legend>'. Text::_('COM_PHOCAFONT_FTP_LOGIN_LABEL').'</legend>'
		.Text::_('COM_PHOCAFONT_FTP_LOGIN_DESC')
		.'<table class="adminform nospace">'
		.'<tr>'
		.'<td width="120"><label for="username">'. Text::_('JGLOBAL_USERNAME').':</label></td>'
		.'<td><input type="text" id="username" name="username" class="input_box" size="70" value="" /></td>'
		.'</tr>'
		.'<tr>'
		.'<td width="120"><label for="password">'. Text::_('JGLOBAL_PASSWORD').':</label></td>'
		.'<td><input type="password" id="password" name="password" class="input_box" size="70" value="" /></td>'
		.'</tr></table></fieldset>';
		return $ftpOutput;
	}
}