<?php
	/**
	 * Page edit form body
	 *
	 * @package ElggPages
	 */
	
	// get the form fields
	$variables = elgg_get_config("pages");
	$page = elgg_extract("entity", $vars);
	
	$allow_comments = "yes";
	if(!empty($page)){
		$allow_comments = $page->allow_comments;
	}
	
	$yesno_options = array(
		"yes" => elgg_echo("option:yes"),
		"no" => elgg_echo("option:no")
	);
	
	// display the form fields
	foreach ($variables as $name => $type) {
		
		echo "<div>";
		echo "<label>" . elgg_echo("pages:$name") . "</label>";
		
		if ($type != "longtext") {
			echo "<br />";
		}
		
		echo elgg_view("input/$type", array("name" => $name, "value" => elgg_extract($name, $vars)));
		echo "</div>";
	}
	
	// add support to disable commenting
	echo "<div>";
	echo "<label>" . elgg_echo("pages_tools:allow_comments") . "</label>";
	echo "<br />";
	echo elgg_view("input/dropdown", array("name" => "allow_comments", "value" => $allow_comments, "options_values" => $yesno_options));
	echo "</div>";
	
	// support for categories
	$cats = elgg_view("input/categories", $vars);
	if (!empty($cats)) {
		echo $cats;
	}
	
	// final part of the form
	echo "<div class='elgg-foot'>";
	// send the guid of the page we"re editing
	if ($guid = elgg_extract("guid", $vars)) {
		echo elgg_view("input/hidden", array("name" => "page_guid", "value" => $guid));
	}
	
	// send the container guid of the page
	echo elgg_view("input/hidden", array("name" => "container_guid", "value" => elgg_extract("container_guid", $vars)));
	
	// send the parent guid of the page
	if ($parent_guid = elgg_extract("parent_guid", $vars)) {
		echo elgg_view("input/hidden", array("name" => "parent_guid", "value" => $parent_guid));
	}
	
	echo elgg_view("input/submit", array("value" => elgg_echo("save")));
	
	echo "</div>";
