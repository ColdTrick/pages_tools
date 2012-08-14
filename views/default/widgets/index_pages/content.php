<?php 

	$widget = elgg_extract("entity", $vars);
	
	$count = (int) $widget->pages_count;
	if(empty($count)){
		$count = 8;
	}

	$options = array(
		"type" => "object",
		"subtype" => "page_top",
		"limit" => $count,
		"full_view" => false,
		"pagination" => false
	);
	
	if($wheres = pages_tools_get_publication_wheres()){
		$options["wheres"] = $wheres;
	}
	
	if($result = elgg_list_entities($options)){
		$more_link = elgg_view("output/url", array(
			"href" => "pages/all",
			"text" => elgg_echo("pages:more"),
			"is_trusted" => true
		));
		
		$result .= "<span class='elgg-widget-more'>" . $more_link . "</span>";
	} else {
		$result = elgg_echo("pages:none");
	}

	echo $result;