<?php

$widget = elgg_extract('entity', $vars);

$count = (int) $widget->pages_count ?: 8;

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => 'page',
	'metadata_name_value_pairs' => [
		'parent_guid' => 0,
	],
	'limit' => $count,
	'pagination' => false,
]);
if (empty($result)) {
	echo elgg_echo('pages:none');
	return;
}

echo $result;

$more_link = elgg_view('output/url', [
	'href' => elgg_generate_url('collection:object:page:all'),
	'text' => elgg_echo('pages:more'),
	'is_trusted' => true,
]);

echo elgg_format_element('div', ['class' => 'elgg-widget-more'], $more_link);
