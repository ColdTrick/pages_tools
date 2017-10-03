<?php

$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('pages_tools:settings:advanced_publication'),
	'#help' => elgg_echo('pages_tools:settings:advanced_publication:description'),
	'name' => 'params[advanced_publication]',
	'options_values' => [
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes'),
	],
	'value' => $plugin->advanced_publication,
]);
