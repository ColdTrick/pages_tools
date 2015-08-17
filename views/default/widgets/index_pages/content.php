<?php

$widget = elgg_extract("entity", $vars);

$count = (int) $widget->pages_count;
if ($count < 1) {
	$count = 8;
}

$options = array(
	"type" => "object",
	"subtype" => "page_top",
	"limit" => $count,
	"full_view" => false,
	"pagination" => false
);

$wheres = pages_tools_get_publication_wheres();
if (!empty($wheres)) {
	$options["wheres"] = $wheres;
}

$result = elgg_list_entities($options);
if (empty($result)) {
	echo elgg_echo("pages:none");
	return;
}

$more_link = elgg_view("output/url", array(
	"href" => "pages/all",
	"text" => elgg_echo("pages:more"),
	"is_trusted" => true
));
$result .= "<span class='elgg-widget-more'>" . $more_link . "</span>";

echo $result;