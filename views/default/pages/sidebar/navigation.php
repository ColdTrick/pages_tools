<?php
/**
 * Navigation menu for a user's or a group's pages
 *
 * @uses $vars['page'] Page object if manually setting selected item
 */

$page_guid = (int) get_input("guid");
$page = false;
if (!empty($page_guid)) {
	$page = get_entity($page_guid);
}

$selected_page = elgg_extract("page", $vars, $page);

// do we have a selected page
if (!pages_tools_is_valid_page($selected_page)) {
	return;
}

// make the navigation tree
$tree = elgg_view('pages_tools/sidebar/tree', ['entity' => $page]);
if (empty($tree)) {
	return;
}

$title = elgg_echo("pages:navigation");
$title .= elgg_format_element('span',
	array(
		"class" => "float-alt",
		"title" => elgg_echo("pages_tools:navigation:tooltip")
	),
	elgg_view_icon("info")
);

$menu = elgg_format_element('div', array('id' => 'pages-tools-navigation', 'class' => 'hidden'), $tree);
$menu .= elgg_view("graphics/ajax_loader", array("hidden" => false));

// load the correct JS/css
elgg_load_js("jquery.tree");
elgg_load_css("jquery.tree");

// draw everything
echo elgg_view_module("aside", $title, $menu);
