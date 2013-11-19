<?php defined('_JEXEC') or die;

/**
 * standard plugin initialization - don't change
 *
 * @var $shLangName : needed to handle translation, automatically set
 *
 * @var $dosef      : FALSE forces the URL back to non-sef
 */

global $sh_LANG;
$sefConfig      = & shRouter::shGetConfig();
$shLangName     = '';
$shLangIso      = '';
$title          = array();
$shItemidString = '';
$dosef          = shInitializePlugin($lang, $shLangName, $shLangIso, $option);
if ($dosef == FALSE) {
	return;
}

/**
 * load language file - adjust as needed
 *
 */
$shLangIso = shLoadPluginLanguage( 'com_XXXXX', $shLangIso, '_SEF_SAMPLE_TEXT_STRING');

/**
 * Build SEF URL based on menu tree
 *
 * @var $title           : an array to contain SEF URL parts, in order
 * @var $shCurrentItemid : contains the current page Itemid
 *
 * @since v1.0
 */

$this->app  = JFactory::getApplication();
$this->menu = $this->app->getMenu();
$this->item = $this->menu->getItem($Itemid);

foreach ($this->item->tree as $id) {
	$item    = $this->menu->getItem($id);
	$title[] = $item->alias;
}

/**
 * Remove common variables from the URL (GET vars list).
 *
 * @function shRemoveFromGETVarsList() : removes passed variable from the URL.
 *
 * All parameters in the non-sef URL are already set as variables (e.g. &task=view is $task).
 * &title= is set as $sh404SEF_title
 *
 * Test variable existence using isset() or empty() before using it.
 *
 */

shRemoveFromGETVarsList('option');

shRemoveFromGETVarsList('lang');

if (!empty($Itemid)) {
	shRemoveFromGETVarsList('Itemid');
}
if (!empty($limit)) {
	shRemoveFromGETVarsList('limit');
}
if (isset($limitstart)) {
	shRemoveFromGETVarsList('limitstart');
}

/**
 * Function to return a regular content element title
 *
 * $task can be 'section', 'category', 'blogsection', 'blogcategory' or 'view'.
 * $id is the section, category or content element id. If $id is set, then the corresponding section, category or element title will be fetched from database and returned (alias can be returned for content elements, according to sh404SEF® backend params). If no $id is passed to the function, then it will return the menu element title instead (internally using getMenuTitle())
 */

if (isset($task) && isset($id)) {
	$title[] = sef_404::getContentTitles($task, $id, $Itemid, $shLangName);
}

/**
 * Create shortened URL
 *
 */

shMustCreatePageId('set', TRUE);

/**
 * Get categories by ID
 *
 */
if (isset($id)) {
	$cats = sef_404::getcategories($id, $shLangName);
}

/**
 *
 * Exmaple of building SEF URL
 *
 * Starts by inserting the menu element title
 *
 * All cleaning up, url encoding, characters replacement, etc is done automatically by sh404SEF.
 * you should not insert yourself a language code in the SEF URL
 *
 * @function getMenuTitle() : return the menu item title corresponding to either an option or Itemid, combined with language information.
 *
 */

$task         = isset($task) ? $task : NULL;
$Itemid       = isset($Itemid) ? $Itemid : NULL;
$shSampleName = shGetComponentPrefix($option);
$shSampleName = empty($shSampleName) ? getMenuTitle($option, $task, $Itemid, NULL, $shLangName) : $shSampleName;
$shSampleName = (empty($shSampleName) || $shSampleName == '/') ? 'SampleCom' : $shSampleName;

switch ($task) {
	case 'task1':
	case 'task2' :
		$dosef = FALSE; // these tasks do not require SEF URL
		break;

	default:
		$title[] = $sh_LANG[$shLangIso]['COM_SH404SEF_VIEW_SAMPLE']; // insert a 'View sample' string,
		// according to language
		// only if you have defined the
		if (!empty($sampleId)) { // fetch some data about the content
			$q = 'SELECT id, title FROM #__samplenames WHERE id = ' . $database->Quote($sampleId); // select clause includes id, even if
			$database->setQuery($q); // we don't need it, in order for Joomfish to
			// return a translated version

			if (shTranslateUrl($option, $shLangName)) // get it in the right language
			{
				$sampleTitle = $database->loadObject();
			} else {
				$sampleTitle = $database->loadObject(FALSE);
			} // second param at false forces Joomfish to
			// return default language version instead of
			// current language

			if ($sampleTitle) { // if we found a title for this element
				$title[] = $sampleTitle->title; // insert it in URL array
				shRemoveFromGETVarsList('sampleId'); // remove sampleId var from GET vars list
				// as we have found a text equivalent
				shMustCreatePageId('set', TRUE); // NEW: ask sh404sef to create a short URL for this SEF URL (pageId)
			}
		}
		shRemoveFromGETVarsList('task'); // also remove task, as it is not needed
	// because we can revert the SEF URL without
	// it
}

/**
 * Standard plugin finalization function - don't change
 */

if ($dosef) {
	$string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString,
		(isset($limit) ? @$limit : NULL), (isset($limitstart) ? @$limitstart : NULL),
		(isset($shLangName) ? @$shLangName : NULL));
}
