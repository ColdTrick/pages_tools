<?php
/**
 * List a user's friends' pages
 *
 * @package ElggPages
 */

$owner = elgg_get_page_owner_entity();
if (!$owner) {
	forward('', '404');
}

elgg_push_breadcrumb(elgg_echo("pages"), "pages/all");
elgg_push_breadcrumb($owner->name, "pages/owner/$owner->username");
elgg_push_breadcrumb(elgg_echo('friends'));

elgg_register_title_button();

$title = elgg_echo('pages:friends');

$options = array(
	'type' => 'object',
	'subtype' => 'page_top',
	'full_view' => false,
	'relationship' => 'friend',
	'relationship_guid' => $owner->guid,
	'relationship_join_on' => 'container_guid',
	'no_results' => elgg_echo('pages:none'),
);

$wheres = pages_tools_get_publication_wheres();
if (!empty($wheres)) {
	$options['wheres'] = $wheres;
}

$content = elgg_list_entities_from_relationship($options);

$params = array(
	'filter_context' => 'friends',
	'content' => $content,
	'title' => $title,
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
