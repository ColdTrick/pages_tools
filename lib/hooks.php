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
				case "owner":
					$result = false;
					
					include(dirname(dirname(__FILE__)) . "/pages/pages/owner.php");
					break;
				case "friends":
					$result = false;
					
					include(dirname(dirname(__FILE__)) . "/pages/pages/friends.php");
					break;
				case "all":
					$result = false;
					
					include(dirname(dirname(__FILE__)) . "/pages/pages/world.php");
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
					"title" => elgg_echo("export"),
					"href" => "pages/export/" . $entity->getGUID(),
					"class" => "pages-tools-lightbox",
					"priority" => 500
				));
				
				if(pages_tools_use_advanced_publication_options()){
					if($entity->unpublished){
						$class = "";
						if(current_page_url() == $entity->getURL()){
							$class = "pages-tools-unpublished";
						}
						
						$result[] = ElggMenuItem::factory(array(
							"name" => "unpublished",
							"text" => elgg_echo("pages_tools:unpublished"),
							"href" => false,
							"item_class" => $class,
							"priority" => 100
						));
					}
				}
				
				if ($entity->canEdit()) {
					// check if someome is still editing
					$edit_notice = (int) $entity->getPrivateSetting("edit_notice");
					if (!empty($edit_notice) && ($edit_notice > (time() - 60))) {
						// find the edit link and change some things
						foreach ($result as $menu_item) {
							if ($menu_item->getName() == "edit") {
								$menu_item->setConfirmText(elgg_echo("pages_tools:edit:confirm"));
							}
						}
					}
				}
				
				// re-add delete link
				$container = $entity->getContainerEntity();
				if ($entity->getOwnerGUID() == elgg_get_logged_in_user_guid() || (!empty($container) && $container->canEdit())) {
					$result[] = ElggMenuItem::factory(array(
						'name' => 'delete',
						'text' => elgg_view_icon('delete'),
						'title' => elgg_echo('delete:this'),
						'href' => "action/pages/delete?guid={$entity->getGUID()}",
						'confirm' => elgg_echo('deleteconfirm'),
						'priority' => 300,
					));
				}
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
	
	function pages_tools_daily_cron_hook($hook, $type, $return_value, $params){
		
		if(pages_tools_use_advanced_publication_options()){
			$publication_id = add_metastring("publication_date");
			$expiration_id = add_metastring("expiration_date");
			$dbprefix = elgg_get_config("dbprefix");
			
			$time = elgg_extract("time", $params, time());
			
			$publish_options = array(
				"type" => "object",
				"subtype" => array("page_top"),
				"limit" => false,
				"joins" => array(
					"JOIN " . $dbprefix . "metadata mdtime ON e.guid = mdtime.entity_guid",
					"JOIN " . $dbprefix . "metastrings mstime ON mdtime.value_id = mstime.id"
				),
				"metadata_name_value_pairs" => array(
					"name" => "unpublished",
					"value" => true
				),
				"wheres" => array("((mdtime.name_id = " . $publication_id . ") AND (DATE(mstime.string) = DATE(NOW())))")
			);
			
			$expire_options = array(
				"type" => "object",
				"subtypes" => array("page_top", "page"),
				"limit" => false,
				"joins" => array(
					"JOIN " . $dbprefix . "metadata mdtime ON e.guid = mdtime.entity_guid",
					"JOIN " . $dbprefix . "metastrings mstime ON mdtime.value_id = mstime.id"
				),
				"wheres" => pages_tools_get_publication_wheres()
			);
			$expire_options["wheres"][] = "((mdtime.name_id = " . $expiration_id . ") AND (DATE(mstime.string) = DATE(NOW())))";
			
			// ignore access
			$ia = elgg_set_ignore_access(true);
			
			// get unpublished pages that need to be published
			if($entities = elgg_get_entities_from_metadata($publish_options)){
				foreach($entities as $entity){
					// add river event
					add_to_river("river/object/page/create", "create", $entity->getOwner(), $entity->getGUID());
					
					// set time created
					$entity->time_created = $time;
					
					// make sure the page is listed
					unset($entity->unpublished);
					
					// notify the user
					notify_user($entity->getOwnerGUID(),
								$entity->site_guid,
								elgg_echo("pages_tools:notify:publish:subject"),
								elgg_echo("pages_tools:notify:publish:message", array(
									$entity->title,
									$entity->getURL()
								)
					));
					
					// save everything
					$entity->save();
				}
			}
			
			// get pages that have expired
			if($entities = elgg_get_entities_from_metadata($expire_options)){
				foreach($entities as $entity){
					// remove river event
					elgg_delete_river(array(
						"object_guid" => $entity->getGUID(),
						"action_type" => "create",
					));
						
					// make sure the page is no longer listed
					$entity->unpublished = true;
					
					// notify the user
					notify_user($entity->getOwnerGUID(),
								$entity->site_guid,
								elgg_echo("pages_tools:notify:expire:subject"),
								elgg_echo("pages_tools:notify:expire:message", array(
									$entity->title,
									$entity->getURL()
								)
					));
						
					// save everything
					$entity->save();
				}
			}
			
			// reset access
			elgg_set_ignore_access($ia);
		}
		
	}
	