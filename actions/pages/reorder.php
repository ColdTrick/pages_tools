<?php
/**
 * jQuery action to reorder the pages in a tree
 */

$parent_guid = (int) get_input('guid');
$order = get_input('order');

if (empty($parent_guid) || empty($order)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!is_array($order)) {
	$order = [$order];
}

$parent = get_entity($parent_guid);
if (!$parent instanceof \ElggPage) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!$parent->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$sub_pages = elgg_get_entities([
	'type' => 'object',
	'subtype' => 'page',
	'guids' => $order,
	'limit' => false,
]);
if (empty($sub_pages)) {
	return elgg_error_response(elgg_echo('pages_tools:actions:reorder:error:subpages'));
}

foreach ($sub_pages as $sub_page){
	$pos = array_search($sub_page->guid, $order) + 1;
	
	$sub_page->order = $pos;
	$sub_page->parent_guid = $parent->guid;
	
	$sub_page->save();
}

return elgg_ok_response('', elgg_echo('pages_tools:actions:reorder:success'));
