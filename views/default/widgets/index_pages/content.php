<?php

/* @var $widget \ElggWidget */
$widget = elgg_extract('entity', $vars);

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => 'page',
	'metadata_name_value_pairs' => [
		'parent_guid' => 0,
	],
	'limit' => (int) $widget->pages_count ?: 8,
	'pagination' => false,
	'no_results' => elgg_echo('pages:none'),
	'widget_more' => elgg_view_url($widget->getURL(), elgg_echo('pages:more')),
]);
