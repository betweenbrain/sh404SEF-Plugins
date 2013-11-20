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
$this->db   = JFactory::getDBO();
$this->menu = $this->app->getMenu();
$this->item = $this->menu->getItem($Itemid);

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
if (isset($category_id)) {
	shRemoveFromGETVarsList('category_id');
}
if (isset($secondarycategory_id)) {
	shRemoveFromGETVarsList('secondarycategory_id');
}

/**
 * Build SEF URL based on menu tree
 */
if (isset($Itemid)) {
	foreach ($this->item->tree as $id) {
		$item    = $this->menu->getItem($id);
		$title[] = $item->alias;
	}
}

if ($event_date) {
	$dates = explode('-', $event_date);
	foreach ($dates as $date) {
		$title[] = $date;
	}

	shRemoveFromGETVarsList('event_date');
}

if (isset($instance_id)) {

	$query = 'SELECT ' . $this->db->nameQuote('event_id') . '
			FROM ' . $this->db->nameQuote('#__calendar2_eventinstances') . '
			WHERE ' . $this->db->nameQuote('eventinstance_id') . ' = ' . $this->db->quote($instance_id);

	$this->db->setQuery($query);
	$event_id = $this->db->loadResult();

	$query = 'SELECT ' . $this->db->nameQuote('event_alias') . '
			FROM ' . $this->db->nameQuote('#__calendar2_events') . '
			WHERE ' . $this->db->nameQuote('event_id') . ' = ' . $this->db->quote($event_id);

	$this->db->setQuery($query);
	$alias = $this->db->loadResult();

	$title[] = $alias;
	$title[] = $instance_id;

	shRemoveFromGETVarsList('id');
	shRemoveFromGETVarsList('instance_id');
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
