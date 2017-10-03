<?php
/**
 * This page displays a lightbox where the settings for export can be set just before exporting the page
 */

$page_guid = (int) get_input("page_guid");

if (empty($page_guid)) {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	forward(REFERER);
}

$page = get_entity($page_guid);
if (!pages_tools_is_valid_page($page)) {
	register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($page_guid)));\
	forward(REFERER);
}

$form_vars = array(
	"id" => "pages-tools-export-form"
);
$body_vars = array(
	"entity" => $page
);

// show the lightbox content
echo elgg_view_form("pages/export", $form_vars, $body_vars);
