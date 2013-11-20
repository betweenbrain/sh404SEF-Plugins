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

if (isset($Itemid)) {
	foreach ($this->item->tree as $id) {
		$item    = $this->menu->getItem($id);
		$title[] = $item->alias;
	}
}

// /new-york/collections/library-and-archives/archive-collections/A0008/

/**
 * Discover and add all vars to URL
 *
 */

/*
foreach ($vars as $key => $var) {
	$title[] = $key . '-' . $var;
}
*/

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
if (isset($view)) {
	shRemoveFromGETVarsList('view');
}

/**
 * Standard plugin finalization function - don't change
 */

if ($dosef) {
	$string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString,
		(isset($limit) ? @$limit : NULL), (isset($limitstart) ? @$limitstart : NULL),
		(isset($shLangName) ? @$shLangName : NULL));
}
