<?php

	function pages_tools_route_pages_hook($hook, $type, $return_value, $params){
		$result = $return_value;
		
		if(!empty($return_value) && is_array($return_value)){
			$page = elgg_extract("segments", $result);
			
			switch($page[0]){
				case "export";
					if(isset($page[1])){
						$result = false;
						set_input("page_guid", $page[1]);
						
						include(dirname(dirname(__FILE__)) . "/pages/export.php");
					}
					break;
				case "view":
					$result = false;
					
					set_input("guid", $page[1]);
					include(dirname(dirname(__FILE__)) . "/pages/pages/view.php");
					break;
			}
		}
		
		return $result;
	}
	
	function pages_tools_entity_menu_hook($hook, $type, $return_value, $params){
		$result = $return_value;
		
		if(!empty($params) && is_array($params)){
			$entity = elgg_extract("entity", $params);
			
			if(!empty($entity) && (elgg_instanceof($entity, "object", "page_top") || elgg_instanceof($entity, "object", "page"))){
				elgg_load_css("lightbox");
				elgg_load_js("lightbox");
				
				$result[] = ElggMenuItem::factory(array(
					"name" => "export",
					"text" => elgg_view_icon("download"),
					"title" => elgg_echo("pages_tools:menu:title:export"),
					"href" => "pages/export/" . $entity->getGUID(),
					"class" => "pages-tools-lightbox",
					"priority" => 500
				));
			}
		}
		
		return $result;
	}
	
	function pages_tools_permissions_comment_hook($hook, $type, $return_value, $params){
		$result = $return_value;
		
		if(!empty($params) && is_array($params)){
			$entity = elgg_extract("entity", $params);
			
			if(pages_tools_is_valid_page($entity)){
				if($entity->allow_comments == "no"){
					$result = false;
				}
			}
		}
		
		return $result;
	}
	
	function pages_tools_widget_url_hook($hook, $type, $return_value, $params){
		$result = $return_value;
		
		if(!$result && !empty($params) && is_array($params)){
			$widget = elgg_extract("entity", $params);
		
			if(!empty($widget) && elgg_instanceof($widget, "object", "widget")){
				switch($widget->handler){
					case "pages":
						$owner = $widget->getOwnerEntity();
						
						if(elgg_instanceof($owner, "group")){
							$result = "pages/group/" . $owner->getGUID() . "/all";
						} else {
							$result = "pages/owner/" . $owner->username;
						}
						break;
					case "index_pages":
						$result = "pages/all";
						break;
				}
			}
		}
		
		return $result;
	}