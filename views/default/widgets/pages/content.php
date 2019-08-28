<?php
/**
 * Elgg pages widget
 *
 * @package ElggPages
 */

$widget = elgg_extract('entity', $vars);

$num_display = (int) $widget->pages_num ?: 4;

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => 'page',
	'container_guid' => $widget->owner_guid,
	'metadata_name_value_pairs' => [
		'parent_guid' => 0,
	],
	'limit' => $num_display,
	'pagination' => false,
]);

if (empty($content)) {
	echo elgg_echo('pages:none');
	return;
}

echo $content;

$owner = $widget->getOwnerEntity();
if ($owner instanceof \ElggGroup) {
	$url = elgg_generate_url('collection:object:page:group', ['guid' => $owner->guid]);
} else {
	$url = elgg_generate_url('collection:object:page:owner', ['username' => $owner->username]);
}

$more_link = elgg_view('output/url', [
	'href' => $url,
	'text' => elgg_echo('pages:more'),
	'is_trusted' => true,
]);

echo elgg_format_element('div', ['class' => 'elgg-widget-more'], $more_link);