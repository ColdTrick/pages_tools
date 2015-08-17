<?php
/**
 * Elgg pages widget edit
 *
 * @package ElggPages
 */

$widget = elgg_extract("entity", $vars);

// set default value
$pages_num = (int) $widget->pages_num;
if($pages_num < 1){
	$pages_num = 4;
}

$params = array(
	"name" => "params[pages_num]",
	"value" => $pages_num,
	"options" => range(1, 10),
	"class" => "mls",
);

echo "<div>";
echo elgg_echo("pages:num");
echo elgg_view('input/select', $params);
echo "</div>";