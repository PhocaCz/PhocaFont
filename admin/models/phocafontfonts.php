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
defined( '_JEXEC' ) or die();
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Log\Log;
jimport( 'joomla.application.component.modellist' );
jimport( 'joomla.installer.installer' );
jimport( 'joomla.installer.helper' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );


class PhocaFontCpModelPhocaFontFonts extends ListModel
{
	protected	$option 		= 'com_phocafont';
	public 		$context		= 'com_phocafont.phocafontfonts';

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'format', 'a.format',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'published','a.published'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		// Initialise variables.
		$app = Factory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $state);

		//$fontId = $app->getUserStateFromRequest($this->context.'.filter.font_id', 'filter_font_id', null);
		//$this->setState('filter.font_id', $fontId);

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_phocafont');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.font_id');

		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		/*
		$query = 'SELECT a.*, u.name AS editor '
				.' FROM #__phocafont_font AS a '
				.' LEFT JOIN #__users AS u ON u.id = a.checked_out '
				. $where
				. $orderby;
		*/
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__phocafont_font` AS a');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.

		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

/*
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}
*/
		// Filter by published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		}
		else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}



		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.title LIKE '.$search.' OR a.alias LIKE '.$search);
			}
		}

		//$query->group('a.id');

		// Add the list ordering clause.
		//$orderCol	= $this->state->get('list.ordering');
		//$orderDirn	= $this->state->get('list.direction');
		$orderCol	= $this->state->get('list.ordering', 'title');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		if ($orderCol != 'a.ordering') {
			$orderCol = 'a.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}



	// - - - - - - - - - - - - - - - -
	// XML
	// - - - - - - - - - - - - - - - -
	function _isManifest($file) {

		try {
			if (File::exists($file)) {
				$xml = simplexml_load_file($file);
				if (!$xml) {
					unset ($xml);
					return null;
				}

				if (!is_object($xml) || ($xml->getName() != 'install')) {

					unset ($xml);
					return null;
				}


				return $xml;
			} else {
				return null;
			}
		} catch (Exception $e){
			return null;
		}
	}

	function _getPathDst() {
		if (empty($this->_pathd)) {
			$this->_pathd = JPATH_SITE.'/media/com_phocafont/fonts';
		}
		return $this->_pathd;
	}

	/* DELETE */
	function delete($cid = array(), &$errorMsg = '') {

		$app	= Factory::getApplication();
		$db 	= Factory::getDBO();
		$errorMsg 	= '';

		if (count( $cid )) {
			ArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'SELECT a.id, a.title, a.format, a.xmlfile, a.defaultfont'
				.' FROM #__phocafont_font AS a '
				.' WHERE a.id IN ( '.$cids.' )';

			$db->setQuery( $query );
			$deleteFont = $db->loadObjectList();
		}

		$deleteFiles = array();
		if(isset($deleteFont) && !empty($deleteFont)) {

			foreach($deleteFont as $key => $value) {

				if ($value->format == 'externalfonttype') {

				} else {
					/*if (isset($value->defaultfont) && $value->defaultfont == 1) {
						$errorMsg .= Text::_('COM_PHOCAFONT_DEFAULT_FONT_CANNOT_BE_DELETED');
						return false;
					}*/

					if (!isset($value->xmlfile) || (isset($value->xmlfile) && $value->xmlfile == '')) {
						$errorMsg .= Text::_('COM_PHOCAFONT_ERROR_NO_XML_INFO_FOUND');
						return false;
					}

					$xml 	= $this->_isManifest($this->_getPathDst() .'/'. $value->xmlfile);
					$deleteFiles[] = $this->_getPathDst() .'/'. $value->xmlfile;


					if(!empty($xml)) {

						if(!is_null($xml->children())) {
							foreach ($xml->children() as $child) {
								if (is_a($child, 'SimpleXMLElement') && $child->getName() == 'files') {
									foreach ($child->children() as $children) {

										if (is_a($children, 'SimpleXMLElement')) {
											//if ($children->data() != 'index.html') {
											if ((string)$children != 'index.html') {
												$deleteFiles[] = $this->_getPathDst() . '/' . (string)$children;
											}
										}
									}
								}
							}
						}
					}

				}
			}
		}

		foreach ($deleteFiles as $keyF => $valueF) {
			if (File::exists($valueF)) {
				if(File::delete($valueF)) {
				} else {
					$errorMsg .= $valueF . ': '.Text::_('COM_PHOCAFONT_ERROR_FILE_CANNOT_BE_DELETED') . '<br />';
				}
			}
		}

		if ($errorMsg != '') {
			return false;
		}

		/* Delete database*/
		$db = Factory::getDBO();

		$result = false;

		if (count( $cid )) {
			ArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'DELETE FROM #__phocafont_font'
					. ' WHERE id IN ( '.$cids.' )';

			$db->setQuery( $query );
			if (!$db->execute()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/* INSTALL*/
	function install(&$errorMsg) {
		$app		= Factory::getApplication();
		$package 	= $this->_getPackageFromUpload();

		if (!$package) {

			$app->enqueueMessage(Text::_('COM_PHOCAFONT_ERROR_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');
			$this->deleteTempFiles();
			return false;
		}

		if ($package['dir'] && Folder::exists($package['dir'])) {
			$this->setPath('source', $package['dir']);
		} else {

			$app->enqueueMessage(Text::_('COM_PHOCAFONT_ERROR_INSTALL_PATH_NOT_EXISTS'), 'error');
			$this->deleteTempFiles();
			return false;
		}

		// We need to find the installation manifest file
		if (!$this->_findManifest()) {

			$app->enqueueMessage(Text::_('COM_PHOCAFONT_ERROR_UNABLE_TO_FIND_INSTALL_PACKAGE_INFO'), 'error');
			$this->deleteTempFiles();
			return false;
		}

		// Files - copy files in manifest
		foreach ($this->_manifest->children() as $child)
		{
			if (is_a($child, 'SimpleXMLElement') && $child->getName() == 'files') {
				if ($this->parseFiles($child) === false) {

					$app->enqueueMessage(Text::_('COM_PHOCAFONT_ERROR_UNABLE_TO_FIND_INSTALL_PACKAGE_INFO'), 'error');
					$this->deleteTempFiles();
					return false;
				}
			}
		}

		// File - copy the xml file
		$copyFile 		= array();
		$path['src']	= $this->getPath( 'manifest' ); // XML file will be copied too
		$path['dest']	= $this->_getPathDst() . '/'. basename($this->getPath('manifest'));
		$copyFile[] 	= $path;
		$this->copyFiles($copyFile);
		$this->deleteTempFiles();


		// Everything should be ok, write the data into the database
		$data = array();
		$data['xmlfile'] = basename($this->getPath('manifest'));
		foreach ($this->_manifest->children() as $child) {
			if (is_a($child, 'SimpleXMLElement') && $child->getName() == 'name') {
				$data['title'] = (string)$child;// $this->_manifest->children()->name;
			}
			if (is_a($child, 'SimpleXMLElement') && $child->getName() == 'types') {
				foreach ($child->children() as $children) {
					if (is_a($children, 'SimpleXMLElement')) {
						$data[$children->getName()] = (string)$children;
					}
				}
			}
		}

		if (isset($data) && !empty($data)) {
			$rowId = $this->store($data);
			if ($rowId > 0) {
				return true;
			} else {
				$errorMsg = Text::_('COM_PHOCAFONT_ERROR_DATA_COULD_NOT_BE_SAVED_DB');
				return false;
			}
		} else {
			$errorMsg = Text::_('COM_PHOCAFONT_ERROR_FONT_TYPE_NOT_FOUND');
			return false;
		}

		return false;
	}

	protected function _getPackageFromUpload() {
		// Get the uploaded file information
		//$userfile = JFactory::getApplication()->input->get('Filedata', null, 'files', 'array' );
		$userfile = Factory::getApplication()->input->files->get( 'Filedata', null, 'raw');

		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			throw new Exception(Text::_('COM_PHOCAFONT_WARNING_INSTALL_FILE'), 500);
			return false;
		}
		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			throw new Exception(Text::_('COM_PHOCAFONT_WARNING_INSTALL_ZLIB'), 500);
			return false;
		}
		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			throw new Exception(Text::_('COM_PHOCAFONT_ERROR_NO_FILE_SELECTED'), 500);
			return false;
		}
		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 ) {
			throw new Exception(Text::_('COM_PHOCAFONT_ERROR_UPLOAD_FILE'), 500);
			return false;
		}

		// Build the appropriate paths
		$config 	= Factory::getConfig();
		$tmp_dest 	= $config->get('tmp_path').'/'.$userfile['name'];
		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = File::upload($tmp_src, $tmp_dest, false, true);

		// Unpack the downloaded package file
		$package = self::unpack($tmp_dest);
		$this->_manifest =& $manifest;

		$this->setPath('packagefile', $package['packagefile']);
		$this->setPath('extractdir', $package['extractdir']);

		return $package;
	}

	function getPath($name, $default=null){
		return (!empty($this->_paths[$name])) ? $this->_paths[$name] : $default;
	}

	function setPath($name, $value) {
		$this->_paths[$name] = $value;
	}

	protected function _findManifest() {
		// Get an array of all the xml files from the installation directory
		$xmlfiles = Folder::files($this->getPath('source'), '.xml$', 1, true);

		// If at least one xml file exists
		if (count($xmlfiles) > 0) {
			foreach ($xmlfiles as $file)
			{
				// Is it a valid joomla installation manifest file?
				$manifest = $this->_isManifest($file);
				if (!is_null($manifest)) {

					$attr = $manifest->attributes();

					if ((string)$attr['type'] != 'phocafontfonts') {

						throw new Exception(Text::_('COM_PHOCAFONT_ERROR_NOT_PHOCA_FONT_FILE'), 500);
						return false;
					}

					// Set the manifest object and path
					$this->_manifest =& $manifest;
					$this->setPath('manifest', $file);

					// Set the installation source path to that of the manifest file
					$this->setPath('source', dirname($file));

					return true;
				}
			}

			// None of the xml files found were valid install files

			throw new Exception(Text::_('COM_PHOCAFONT_ERROR_NOT_FIND_XML_SETUP_FILE'), 500);
			return false;
		} else {
			// No xml files were found in the install folder

			throw new Exception(Text::_('COM_PHOCAFONT_ERROR_NOT_FIND_XML_SETUP_FILE'), 500);
			return false;
		}
	}

	function parseFiles($element, $cid=0) {
		// Initialize variables
		$copyfiles = array ();

		if (!is_a($element, 'SimpleXMLElement') || !count($element->children())) {
			return 0;// Either the tag does not exist or has no children therefore we return zero files processed.
		}

		$files = $element->children(); // Get the array of file nodes to process
		if (count($files) == 0) {
			return 0; // No files to process
		}

		$source 	 = $this->getPath('source');
		$destination = $this->_getPathDst();
		// Process each file in the $files array (children of $tagName).

		/*foreach ($files as $file) {
			$path['src']	= $source.'/'.$file->data();
			$path['dest']	= $destination.'/'.$file->data();

			// Add the file to the copyfiles array
			$copyfiles[] = $path;
		}*/

		if (!empty($files->filename)) {
			foreach($files->filename as $fik => $fiv) {
				$path['src']	= $source.'/'.$fiv;
				$path['dest']	= $destination.'/'.$fiv;
				$copyfiles[] = $path;
			}
		}

		return $this->copyFiles($copyfiles);
	}

	function copyFiles($files) {


		if (is_array($files) && count($files) > 0) {

			$folder = $this->_getPathDst();
			if (isset($folder)) {
				if (!Folder::exists($folder)) {
					if (!(Folder::create($folder))) {

						throw new Exception(Text::_('COM_PHOCAFONT_ERROR_CREATE_FOLDER_FONTS'), 500);
						return false;
					}
				}
			}

			foreach ($files as $file)
			{
				// Get the source and destination paths
				$filesource	= Path::clean($file['src']);
				$filedest	= Path::clean($file['dest']);

				if (!file_exists($filesource)) {

					throw new Exception(Text::_('COM_PHOCAFONT_FILE_NOT_EXISTS') .' ('. $filesource . ')', 500);
					return false;
				} else {
					 if (!(File::copy($filesource, $filedest))) {

						throw new Exception(Text::_('COM_PHOCAFONT_ERROR_COPY_FILE').' ('. $filesource .'<br />'. $filedest. ')', 500);
						return false;
					}
				}
			}
		} else {

			throw new Exception(Text::_('COM_PHOCAFONT_ERROR_INSTALLATION'), 500);
			return false;
		}

		return count($files);
	}

	function deleteTempFiles () {
		// Delete Temp files
		$path = $this->getPath('source');
		if (is_dir($path)) {
			$val = Folder::delete($path);
		} else if (is_file($path)) {
			$val = File::delete($path);
		}
		$packageFile = $this->getPath('packagefile');
		if (is_file($packageFile)) {
			$val = File::delete($packageFile);
		}
		$extractDir = $this->getPath('extractdir');
		if (is_dir($extractDir)) {
			$val = Folder::delete($extractDir);
		}
	}

	// - - - - - - - - - - - - - - - -
	// DB + XML
	// - - - - - - - - - - - - - - - -
	function store($data) {

		$row = $this->getTable('phocafontfont');



		$db = Factory::getDBO();
		// Get id if current font is installed
		$query = 'SELECT a.id'
				.' FROM #__phocafont_font AS a '
				.' WHERE a.title = '.$db->Quote($data['title']);

		$db->setQuery($query);
		$currentFont = $db->loadObject();
		if(isset($currentFont->id) && $currentFont->id > 0) {
			$row->id = $currentFont->id;
		}

		// Get info about rows, if there is no row item, set default for first uploaded
		$query = 'SELECT a.id'
				.' FROM #__phocafont_font AS a ';

		$db->setQuery($query);
		$existsFont = $db->loadObject();
		if(!isset($existsFont->id)) {
			$row->defaultfont = 1;
		}

		$row->published = 1;
		$row->params 	= phocaFontHelper::getStringFromItem($row->params);
		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// if new item, order last in appropriate group
		if (!$row->id) {
			$row->ordering = $row->getNextOrder( );
		}

		// Make sure table is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $row->id;
	}


	function isDefaultPublished($id) {

		$table 	= Table::getInstance('PhocaFontFont', 'Table');
		$table->load($id);
		if(!$table->get('published')) {
			return false;
		}

		return true;
	}

	function setDefault( $id ) {
		$db = $this->getDBO();

		// Clear default field for all other items
		$query = 'UPDATE #__phocafont_font' .
				' SET defaultfont = 0' .
				' WHERE defaultfont = 1';
		$db->setQuery( $query );
		$db->execute();

		// Set the given item to home
		$query = 'UPDATE #__phocafont_font' .
				' SET defaultfont = 1' .
				' WHERE id = '.(int) $id;
		$db->setQuery( $query );
		$db->execute();
		return true;
	}

	/*
	 * Check if there is a default font
	 * if not set the first row
	 */
	function checkDefault() {

		$db = $this->getDBO();

		$query = 'SELECT a.id'
				.' FROM #__phocafont_font AS a '
				.' WHERE a.defaultfont = 1';

		$db->setQuery($query);
		$db->execute();

		$defaultFont = $db->loadObject();
		if(isset($defaultFont->id) && $defaultFont->id > 0) {
			return true;// There is default font - OK return true
		}

		// Default Font not found, set the first
		$query = 'SELECT a.id'
				.' FROM #__phocafont_font AS a '
				.' LIMIT 0,1';

		$db->setQuery($query);
		$db->execute();

		$firstFont = $db->loadObject();
		if(isset($firstFont->id) && $firstFont->id > 0) {
			// Set the given item to home
			$query = 'UPDATE #__phocafont_font' .
					' SET defaultfont = 1' .
					' WHERE id = '.(int) $firstFont->id;
			$db->setQuery( $query );
			$db->execute();
		}

		return true;
	}

	public static function unpack($p_filename)
	{
		// Path to the archive
		$archivename = $p_filename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		// Clean the paths to use for archive extraction
		$extractdir = Path::clean(dirname($p_filename) . '/' . $tmpdir);
		$archivename = Path::clean($archivename);

		// Do the unpacking of the archive
		try
		{
			$archive = new \Joomla\Archive\Archive;
			$archive->extract($archivename, $extractdir);
		}
		catch (Exception $e)
		{
			return false;
		}

		/*
		 * Let's set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$retval['extractdir'] = $extractdir;
		$retval['packagefile'] = $archivename;

		/*
		 * Try to find the correct install directory.  In case the package is inside a
		 * subdirectory detect this and set the install directory to the correct path.
		 *
		 * List all the items in the installation directory.  If there is only one, and
		 * it is a folder, then we will set that folder to be the installation folder.
		 */
		$dirList = array_merge(Folder::files($extractdir, ''), Folder::folders($extractdir, ''));

		if (count($dirList) == 1)
		{
			if (Folder::exists($extractdir . '/' . $dirList[0]))
			{
				$extractdir = Path::clean($extractdir . '/' . $dirList[0]);
			}
		}

		/*
		 * We have found the install directory so lets set it and then move on
		 * to detecting the extension type.
		 */
		$retval['dir'] = $extractdir;

		/*
		 * Get the extension type and return the directory/type array on success or
		 * false on fail.
		 */
		$retval['type'] = self::detectType($extractdir);
		if ($retval['type'])
		{
			return $retval;
		}
		else
		{
			return false;
		}
	}

	public static function detectType($p_dir)
	{
		// Search the install dir for an XML file
		$files = Folder::files($p_dir, '\.xml$', 1, true);

		if (!count($files))
		{
			Log::add(Text::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'), Log::WARNING, 'error');

			return false;
		}

		foreach ($files as $file)
		{
			$xml = simplexml_load_file($file);

			if (!$xml)
			{
				continue;
			}

			if ($xml->getName() != 'install')
			{
				unset($xml);
				continue;
			}

			$type = (string) $xml->attributes()->type;

			// Free up memory
			unset($xml);
			return $type;
		}

		Log::add(Text::_('JLIB_INSTALLER_ERROR_NOTFINDJOOMLAXMLSETUPFILE'), Log::WARNING, 'error');

		// Free up memory.
		unset($xml);
		return false;
	}

}

?>
