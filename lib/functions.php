<?php

	function pages_tools_is_valid_page($entity){
		$result = false;
		
		if(!empty($entity)){
			if(elgg_instanceof($entity, "object", "page_top") || elgg_instanceof($entity, "object", "page")){
				$result = true;
			}
		}
		
		return $result;
	}
	
	function pages_tools_get_ordered_children(ElggObject $page){
		$result = false;
		
		if(!empty($page) && pages_tools_is_valid_page($page)){
			$options = array(
				"type" => "object",
				"subtype" => "page",
				"limit" => false,
				"metadata_name_value_pairs" => array("parent_guid" => $page->getGUID())
			);
			
			if($children = elgg_get_entities_from_metadata($options)){
				$result = array();
				
				foreach($children as $child){
					$order = $child->order;
					if($order === NULL){
						$order = $child->time_created;
					}
					
					while(array_key_exists($order, $result)){
						$order++;
					}
					
					$result[$order] = $child;
				}
				
				ksort($result);
			}
		}
		
		return $result;
	}
	
	/**
	 * Render the index for every page below the provided page
	 *
	 * @param ElggObject $page
	 * @return boolean
	 */
	function pages_tools_render_index(ElggObject $page){
		$result = false;
		
		if(!empty($page) && pages_tools_is_valid_page($page)){
			if($children = pages_tools_get_ordered_children($page)){
				$result = "";
				
				foreach($children as $child){
					$result .= "<li>" . elgg_view("output/url", array("text" => $child->title, "href" => "#page_" . $child->getGUID(), "title" => $child->title));
					
					if($child_index = pages_tools_render_index($child)){
						$result .= "<ul>" . $child_index . "</ul>";
					}
					
					$result .= "</li>";
				}
			}
		}
		
		return $result;
	}
	
	function pages_tools_render_childpages(ElggObject $page){
		$result = false;
		
		if(!empty($page) && pages_tools_is_valid_page($page)){
			if($children = pages_tools_get_ordered_children($page)){
				$result = "";
				
				foreach($children as $child){
					$result .= "<h3>" . elgg_view("output/url", array("text" => $child->title, "href" => false, "name" => "page_" . $child->getgUID())) . "</h3>";
					$result .= elgg_view("output/longtext", array("value" => $child->description));
					$result .= "<p style='page-break-after:always;'></p>";
					
					if($child_pages = pages_tools_render_childpages($child)){
						$result .= $child_pages;
					}
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Register a complete tree to a menu in order to display navigation
	 *
	 * @param ElggObject $page
	 */
	function pages_tools_register_navigation_tree(ElggObject $entity){
		$result = false;
		
		if(pages_tools_is_valid_page($entity)){
			if($root_page = pages_tools_get_root_page($entity)){
				
				$class = "";
				if(!$root_page->canEdit()){
					$class = "no-edit";
				}
				
				if(pages_tools_register_navigation_tree_children($root_page)){
					$result = true;
					
					// register root page
					elgg_register_menu_item("pages_nav", array(
						"name" => "page_" . $root_page->getGUID(),
						"text" => $root_page->title,
						"href" => $root_page->getURL(),
						"rel" => $root_page->getGUID(),
						"item_class" => $class,
						"class" => "pages-tools-wrap"
					));
				}
			}
		}
		
		return $result;
	}
	
	function pages_tools_register_navigation_tree_children(ElggObject $parent_entity, $depth = 0){
		$result = false;
		
		if(pages_tools_is_valid_page($parent_entity)){
			if($children = pages_tools_get_ordered_children($parent_entity)){
				$result = true;
				
				foreach($children as $order => $child){
					$class = "";
					if(!$child->canEdit()){
						$class = "no-edit";
					}
					
					$params = array(
						"name" => "page_" . $child->getGUID(),
						"text" => $child->title,
						"title" => $child->title,
						"href" => $child->getURL(),
						"rel" => $child->getGUID(),
						"item_class" => $class,
						"parent_name" => "page_" . $parent_entity->getGUID(),
						"priority" => $order
					);
					
					if($depth < 4){
						$params["class"] = "pages-tools-wrap";
					}
					
					// register this item to the menu
					elgg_register_menu_item("pages_nav", $params);
					
					// register child elements
					pages_tools_register_navigation_tree_children($child, $depth + 1);
				}
			}
		}
		
		return $result;
	}
	
	function pages_tools_get_root_page(ElggObject $entity) {
		$result = false;
		
		if (pages_tools_is_valid_page($entity)) {
			if (elgg_instanceof($entity, "object", "page_top")) {
				$result = $entity;
			} elseif (isset($entity->parent_guid)) {
				$parent = get_entity($entity->parent_guid);
				
				if ($parent) {
					$result = pages_tools_get_root_page($parent);
				}
			}
		}
		
		return $result;
	}
	
	function pages_tools_use_advanced_publication_options(){
		static $result;
		
		if(!isset($result)){
			$result = false;
			
			if(($setting = elgg_get_plugin_setting("advanced_publication", "pages_tools")) && ($setting == "yes")){
				$result = true;
			}
		}
		
		return $result;
	}
	
	function pages_tools_get_publication_wheres(){
		static $result;
		
		if(!isset($result)){
			$result = array();
			
			if(pages_tools_use_advanced_publication_options()){
				$unpublished_id = add_metastring("unpublished");
				$dbprefix = elgg_get_config("dbprefix");
				
				$query = "(e.guid NOT IN (";
				$query .= "SELECT entity_guid";
				$query .= " FROM " . $dbprefix . "metadata";
				$query .= " WHERE name_id = " . $unpublished_id;
				$query .= "))";
				
				$result[] = $query;
			}
		}
		
		return $result;
	}
	