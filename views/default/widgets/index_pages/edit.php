<?php

/* @var $widget \ElggWidget */
$widget = elgg_extract('entity', $vars);

echo elgg_view('object/widget/edit/num_display', [
	'name' => 'pages_count',
	'entity' => $widget,
	'default' => 8,
	'label' => elgg_echo('pages:num'),
]);
