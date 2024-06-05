<?php

/* @var $plugin \ElggPlugin */
$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('pages_tools:settings:enable_export'),
	'name' => 'params[enable_export]',
	'checked' => !empty($plugin->enable_export),
	'switch' => true,
	'value' => 1,
]);
