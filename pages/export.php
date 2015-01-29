<?php

	/**
	 * This page displays a lightbox where the settings for export can be set just before exporting the page
	 */

	$page_guid = (int) get_input("page_guid");
	$forward = true;
	
	if(!empty($page_guid)){
		if(($page = get_entity($page_guid)) && (elgg_instanceof($page, "object", "page_top") || elgg_instanceof($page, "object", "page"))){
			$forward = false;
			
			$form_vars = array(
				"id" => "pages-tools-export-form"
			);
			$body_vars = array(
				"entity" => $page
			);
			
			$lightbox = elgg_view_form("pages/export", $form_vars, $body_vars);
		} else {
			register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($page_guid)));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	if(!$forward){
		// show the lightbox content
		echo $lightbox;
	} else {
		forward(REFERER);
	}