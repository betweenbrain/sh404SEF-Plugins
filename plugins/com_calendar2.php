<?php defined('_JEXEC') or die();

// ------------------  standard plugin initialize function - don't change ---------------------------
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
// ------------------  standard plugin initialize function - don't change ---------------------------

$this->app  = JFactory::getApplication();
$this->menu = $this->app->getMenu();
$this->item = $this->menu->getItem($Itemid);

// die('<pre>' . print_r($task, TRUE) . '<pre>');
// echo('<pre>' . print_r($this->item, true) . '<pre>');

// remove common URL from GET vars list

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

shRemoveFromGETVarsList('category_id');

shRemoveFromGETVarsList('secondarycategory_id');

/**
 * Build SEF URL based on menu tree
 */

foreach ($this->item->tree as $id) {
	$item    = $this->menu->getItem($id);
	$title[] = $item->alias;
}

if ($event_date) {
	$dates = explode('-', $event_date);
	foreach ($dates as $date) {
		$title[] = $date;
	}

	shRemoveFromGETVarsList('event_date');
}

shMustCreatePageId('set', TRUE);

shRemoveFromGETVarsList('task');

shRemoveFromGETVarsList('view');

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef) {
	$string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString,
		(isset($limit) ? @$limit : NULL), (isset($limitstart) ? @$limitstart : NULL),
		(isset($shLangName) ? @$shLangName : NULL));
}
// ------------------  standard plugin finalize function - don't change ---------------------------
  