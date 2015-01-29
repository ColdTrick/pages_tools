<?php

	$guid = (int) get_input("guid");
	$format = strtolower(get_input("format", "a4"));
	$font = get_input("font", "times");
	$include_subpages = (int) get_input("include_children");
	$include_index = (int) get_input("include_index");
	
	if(!empty($guid)){
		if(($page = get_entity($guid)) && pages_tools_is_valid_page($page)){
			// this could take a while
			set_time_limit(0);
			
			// begin of output
			$html = "";
			
			// make index
			if(!empty($include_index)){
				$html .= "<h3>" . elgg_echo("pages_tools:export:index") . "</h3>";
				
				$html .= "<ul>";
				$html .= "<li>" . elgg_view("output/url", array("text" => $page->title, "href" => "#page_" . $page->getGUID(), "title" => $page->title)) . "</li>";
				
				// include subpages
				if(!empty($include_subpages) && ($sub_index = pages_tools_render_index($page))){
					$html .= $sub_index;
				}
				
				$html .= "</ul>";
				$html .= "<p style='page-break-after:always;'></p>";
			}
			
			// print page
			$html .= "<h3>" . elgg_view("output/url", array("text" => $page->title, "href" => false, "name" => "page_" . $page->getgUID())) . "</h3>";
			$html .= elgg_view("output/longtext", array("value" => $page->description));
			$html .= "<p style='page-break-after:always;'></p>";
			
			// print subpages
			if(!empty($include_subpages) && ($child_pages = pages_tools_render_childpages($page))){
				$html .= $child_pages;
			}
			
			// load library
			elgg_load_library("dompdf");
			
			// render everything
			try {
				$dompdf = new DOMPDF();
				$dompdf->set_paper($format);
				$dompdf->load_html($html);
				$dompdf->render();
				$dompdf->stream(elgg_get_friendly_title($page->title) . ".pdf");
				exit();
			} catch(Exception $e){
				register_error($e->getMessage());
			}
		} else {
			register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($guid)));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	forward(REFERER);