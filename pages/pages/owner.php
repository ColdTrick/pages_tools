<?php
/**
 * List a user's or group's pages
 *
 * @package ElggPages
 */

$owner = elgg_get_page_owner_entity();
if (!$owner) {
	forward('pages/all');
}

// access check for closed groups
group_gatekeeper();

$title = elgg_echo('pages:owner', array($owner->name));

elgg_push_breadcrumb(elgg_echo("pages"), "pages/all");
elgg_push_breadcrumb($owner->name);

elgg_register_title_button();

$options = array(
	'types' => 'object',
	'subtypes' => 'page_top',
	'container_guid' => elgg_get_page_owner_guid(),
	'full_view' => false,
);

// show everything if you can edit the page owner
if(!$owner->canEdit() && ($wheres = pages_tools_get_publication_wheres())){
	$options["wheres"] = $wheres;
}

if (!($content = elgg_list_entities($options))) {
	$content = '<p>' . elgg_echo('pages:none') . '</p>';
}

$filter_context = '';
if (elgg_get_page_owner_guid() == elgg_get_logged_in_user_guid()) {
	$filter_context = 'mine';
}

$sidebar = elgg_view('pages/sidebar/navigation');
$sidebar .= elgg_view('pages/sidebar');

$params = array(
	'filter_context' => $filter_context,
	'content' => $content,
	'title' => $title,
	'sidebar' => $sidebar,
);

if (elgg_instanceof($owner, 'group')) {
	$params['filter'] = '';
}

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
