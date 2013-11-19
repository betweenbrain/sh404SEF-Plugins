<?php defined('_JEXEC') or die;

/**
 * sh404SEF support for com_XXXXX component.
 * Author :
 * contact :
 *
 * This is a sample sh404SEF native plugin file
 *
 */


/**
 * standard plugin initialize function - don't change
 *
 * @var $shLangName needed to handle tranlsation, automatically set within the Initialization section of the plugin
 *
 * Setting $dosef to false forces the URL back to non-sef.
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
 */

//$shLangIso = shLoadPluginLanguage( 'com_XXXXX', $shLangIso, '_SEF_SAMPLE_TEXT_STRING');


/**
 * Application and menu objects
 *
 */

$this->app  = JFactory::getApplication();
$this->menu = $this->app->getMenu();
$this->item = $this->menu->getItem($Itemid);


/**
 * Build SEF URL based on menu tree
 *
 * @var $title: an array to contain SEF URL parts in order
 *
 * @var $shCurrentItemid : contains the current page Itemid
 *
 * @since v1.0
 */

foreach ($this->item->tree as $id) {
	$item    = $this->menu->getItem($id);
	$title[] = $item->alias;
}

/**
 * Remove common variables from the URL (GET vars list).
 *
 * @function shRemoveFromGETVarsList() : tells sh404SEF® that a variable has been turned into its SEF equivalent, or is not required, and to not add to the URL anymore.
 *
 * All parameters contained in the non-sef URL have already been extracted and set as variables (e.g. &task=view is included as $view).
 *
 * If using title as a parameter in URL, the content &title= is stored in the $sh404SEF_title variable
 *
 * Test variable existence using isset() or empty()  before using a variable.
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
} // limitstart can be zero

/**
 * start by inserting the menu element title (just an idea, this is not required at all)
 *
 * all cleaning up, url encoding, characters replacement, etc is done automatically by sh404SEF.
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
 * standard plugin finalize function - don't change
 */

if ($dosef) {
	$string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString,
		(isset($limit) ? @$limit : NULL), (isset($limitstart) ? @$limitstart : NULL),
		(isset($shLangName) ? @$shLangName : NULL));
}
