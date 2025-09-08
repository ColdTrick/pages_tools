<?php

/* @var $plugin \ElggPlugin */
$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'switch',
	'#label' => elgg_echo('pages_tools:settings:enable_export'),
	'name' => 'params[enable_export]',
	'value' => $plugin->enable_export,
]);
