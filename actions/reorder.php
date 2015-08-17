<?php
/**
 * jQuery action to reorder the pages in a tree
 */

$parent_guid = (int) get_input("parent_guid");
$order = get_input("order");

if (empty($parent_guid) || empty($order)) {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	forward(REFERER);
}

if (!is_array($order)) {
	$order = array($order);
}

$parent = get_entity($parent_guid);
if (!pages_tools_is_valid_page($parent)) {
	register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($parent_guid)));
	forward(REFERER);
}

if (!$parent->canEdit()) {
	register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	forward(REFERER);
}

$options = array(
	"type" => "object",
	"subtypes" => array("page", "page_top"),
	"guids" => $order,
	"limit" => false
);

$sub_pages = elgg_get_entities($options);
if (empty($sub_pages)) {
	register_error(elgg_echo("pages_tools:actions:reorder:error:subpages"));
	forward(REFERER);
}

foreach ($sub_pages as $sub_page){
	$pos = array_search($sub_page->getGUID(), $order) + 1;
	
	$sub_page->order = $pos;
	$sub_page->parent_guid = $parent->getGUID();
	
	$sub_page->save();
}

system_message(elgg_echo("pages_tools:actions:reorder:success"));
forward(REFERER);
