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
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;


$r 			= $this->r;


JFactory::getDocument()->addScriptDeclaration(

'Joomla.submitbutton = function(task) { 
	if (task != "'. $this->t['task'].'.cancel" || document.formvalidator.isValid(document.getElementById(\'adminForm\'))) {
		Joomla.submitform(task, document.getElementById("adminForm"));
	} else {

	    alert(\''.Text::_('JGLOBAL_VALIDATION_FORM_FAILED', true).'\');
	}
}'

);

echo $r->startHeader();
echo $r->startForm($this->t['o'], $this->t['task'], $this->item->id, 'adminForm', 'adminForm');
// First Column
//echo '<div class="span10 form-horizontal">';
echo '<div>';
$tabs = array (
'general' 		=> Text::_($this->t['l'].'_GENERAL_OPTIONS'),
'publishing' 	=> Text::_($this->t['l'].'_PUBLISHING_OPTIONS')
);
echo $r->navigation($tabs);

//echo '<div class="tab-content">'. "\n";

//echo '<div class="tab-pane active" id="general">'."\n";

$formArray = array ('title');
echo $r->groupHeader($this->form, $formArray);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');

$formArray = array ('alternative', 'format');
echo $r->group($this->form, $formArray);

if ($this->item->format == 'externalfonttype' || $this->item->id == 0) {
	$formArray = array('variant', 'subset' );
	echo $r->group($this->form, $formArray);
}

$formArray = array('ordering' );
echo $r->group($this->form, $formArray);
echo $r->endTab();

echo $r->startTab('publishing', $tabs['publishing']);
foreach($this->form->getFieldset('publish') as $field) {
	echo '<div class="control-group">';
	if (!$field->hidden) {
		echo '<div class="control-label">'.$field->label.'</div>';
	}
	echo '<div class="controls">';
	echo $field->input;
	echo '</div></div>';
}
echo $r->endTab();




echo $r->endTabs();
echo '</div>';
echo $r->formInputs($this->t['task']);
echo $r->endForm();
?>


