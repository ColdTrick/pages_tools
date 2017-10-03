<?php

$page = elgg_extract('entity', $vars);

$body = elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('page_tools:export:format'),
	'name' => 'format',
	'options_values' => [
		'A4' => elgg_echo('page_tools:export:format:a4'),
		'letter' => elgg_echo('page_tools:export:format:letter'),
		'A3' => elgg_echo('page_tools:export:format:a3'),
		'A5' => elgg_echo('page_tools:export:format:a5'),
	],
]);

$body .= elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('page_tools:export:include_subpages'),
	'name' => 'include_children',
	'value' => 1,
]);

$body .= elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('page_tools:export:include_index'),
	'name' => 'include_index',
	'value' => 1,
]);

$body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $page->guid,
]);

echo elgg_view_module('info', elgg_echo('export'), $body);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('export'),
	'onclick' => '$.colorbox.close();',
]);

elgg_set_form_footer($footer);
