<?php

$plugin = elgg_extract("entity", $vars);

$noyes_options = array(
	"no" => elgg_echo("option:no"),
	"yes" => elgg_echo("option:yes")
);

echo "<div>";
echo elgg_echo("pages_tools:settings:advanced_publication");
echo elgg_view("input/select", array(
	"name" => "params[advanced_publication]",
	"options_values" => $noyes_options,
	"value" => $plugin->advanced_publication,
	"class" => "mls",
));
echo "<div class='elgg-subtext'>" . elgg_echo("pages_tools:settings:advanced_publication:description") . "</div>";
echo "</div>";
