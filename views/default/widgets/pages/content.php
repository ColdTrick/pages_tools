<?php
/**
 * Elgg pages widget
 *
 * @package ElggPages
 */

/* @var $widget \ElggWidget */
$widget = elgg_extract('entity', $vars);

$options = [
	'type' => 'object',
	'subtype' => 'page',
	'container_guid' => $widget->owner_guid,
	'metadata_name_value_pairs' => [
		'parent_guid' => 0,
	],
	'limit' => (int) $widget->pages_num ?: 4,
	'pagination' => false,
	'no_results' => elgg_echo('pages:none'),
];

$owner = $widget->getOwnerEntity();
if ($owner instanceof \ElggGroup) {
	$url = elgg_generate_url('collection:object:page:group', ['guid' => $owner->guid]);
} else {
	$url = elgg_generate_url('collection:object:page:owner', ['username' => $owner->username]);
}

if (!empty($url)) {
	$options['widget_more'] = elgg_elgg_view_url($url, elgg_echo('pages:more'));
}

echo elgg_list_entities($options);
