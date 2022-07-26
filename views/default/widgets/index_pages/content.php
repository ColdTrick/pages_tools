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
	'widget_more' => elgg_view_url(elgg_generate_url('collection:object:page:all'), elgg_echo('pages:more')),
]);
