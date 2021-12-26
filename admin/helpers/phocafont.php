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
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
class phocaFontHelper
{
	public static function getPhocaVersion($component) {
		$folder = JPATH_ADMINISTRATOR . '/components/'.$component;
		if (Folder::exists($folder)) {
			$xmlFilesInDir = Folder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE . '/components/'.$component;
			if (Folder::exists($folder)) {
				$xmlFilesInDir = Folder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = array();
		if (!empty($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = Installer::parseXMLInstallFile($folder.'/'.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}

		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}

	public static function getStringFromItem($item) {

		if (empty($item)) {
			return '';
		}

		return $item;
	}
}


?>
