<?php

$entity = elgg_extract('entity', $vars);
if (!pages_tools_is_valid_page($entity)) {
	return;
}

$root_page = pages_tools_get_root_page($entity);

$tree_data = pages_tools_get_tree_html_from_cache($root_page);
if ($tree_data !== false) {
	echo $tree_data;
	return;
}

if (!pages_tools_register_navigation_tree($entity)) {
	return;
}

// get the navigation menu
$tree_data = elgg_view_menu('pages_nav', ['class' => 'pages-nav', 'sort_by' => 'priority']);

pages_tools_save_tree_html_to_cache($root_page, $tree_data);

echo $tree_data;
