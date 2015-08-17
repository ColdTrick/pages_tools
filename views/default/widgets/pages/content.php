<?php
/**
 * Elgg pages widget
 *
 * @package ElggPages
 */

$widget = elgg_extract("entity", $vars);

$pages_num = (int) $widget->pages_num;
if ($pages_num < 1) {
	$pages_num = 4;
}

$options = array(
	"type" => "object",
	"subtype" => "page_top",
	"container_guid" => $widget->getOwnerGUID(),
	"limit" => $pages_num,
	"full_view" => FALSE,
	"pagination" => FALSE,
);

$wheres = pages_tools_get_publication_wheres();
if (!empty($wheres)) {
	$options["wheres"] = $wheres;
}

$content = elgg_list_entities($options);
if (empty($content)) {
	echo elgg_echo("pages:none");
	return;
}

$owner = $widget->getOwnerEntity();
if (elgg_instanceof($owner, "group")) {
	$url = "pages/group/" . $owner->getGUID() . "/all";
} else {
	$url = "pages/owner/" . $owner->username;
}

$more_link = elgg_view('output/url', array(
	'href' => $url,
	'text' => elgg_echo('pages:more'),
	'is_trusted' => true,
));
$content .= "<span class='elgg-widget-more'>" . $more_link . "</span>";

echo $content;