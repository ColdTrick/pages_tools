<?php 

	$widget = elgg_extract("entity", $vars);
	
	$pages_count = (int) $widget->pages_count;
	if($pages_count < 1){
		$pages_count = 8;
	}
	
	echo "<div>";
	echo elgg_echo("pages:num");
	echo "&nbsp;" . elgg_view("input/text", array("name" => "params[pages_count]", "value" => $pages_count, "size" => "4", "maxlength" => "4"));
	echo "</div>";