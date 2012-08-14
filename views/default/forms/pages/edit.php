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
	
	if(pages_tools_use_advanced_publication_options()){
		if(!empty($page)){
			$publication_date_value = $page->publication_date;
			$expiration_date_value = $page->expiration_date;
		}
		
		if(empty($publication_date_value)){
			$publication_date_value = "";
		}
		
		if(empty($expiration_date_value)){
			$expiration_date_value = "";
		}
		
		$publication_date = "<div class='mbs'>";
		$publication_date .= "<label for='publication_date'>" . elgg_echo("pages_tools:label:publication_date") . "</label>";
		$publication_date .= elgg_view("input/date", array(
										"name" => "publication_date", 
										"value" => $publication_date_value));
		$publication_date .= "<div class='elgg-subtext'>" . elgg_echo("pages_tools:publication_date:description") . "</div>";
		$publication_date .= "</div>";
		
		$expiration_date = "<div class='mbs'>";
		$expiration_date .= "<label for='expiration_date'>" . elgg_echo("pages_tools:label:expiration_date") . "</label>";
		$expiration_date .= elgg_view("input/date", array(
										"name" => "expiration_date", 
										"value" => $expiration_date_value));
		$expiration_date .= "<div class='elgg-subtext'>" . elgg_echo("pages_tools:expiration_date:description") . "</div>";
		$expiration_date .= "</div>";
		
		echo elgg_view_module("info", elgg_echo("pages_tools:label:publication_options"), $publication_date . $expiration_date);
	}
	
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
