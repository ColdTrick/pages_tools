<?php
/**
 * List all pages
 *
 * @package ElggPages
 */

$title = elgg_echo('pages:all');

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('pages'));

elgg_register_title_button();

$options = [
	'types' => 'object',
	'subtypes' => 'page_top',
	'full_view' => false,
	'no_results' => elgg_echo('pages:none'),
];

if (!elgg_is_admin_logged_in() && ($wheres = pages_tools_get_publication_wheres())) {
	$options["wheres"] = $wheres;
}

$content = elgg_list_entities($options);

$body = elgg_view_layout('content', array(
	'filter_context' => 'all',
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('pages/sidebar'),
));

echo elgg_view_page($title, $body);
