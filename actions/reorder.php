<?php

	$parent_guid = (int) get_input("parent_guid");
	$order = get_input("order");
	
	if(!empty($parent_guid) && !empty($order)){
		if(!is_array($order)){
			$order = array($order);
		}
		
		if(($parent = get_entity($parent_guid)) && pages_tools_is_valid_page($parent)){
			if($parent->canEdit()){
				$options = array(
					"guids" => $order,
					"limit" => false
				);
				var_dump($options);
				if($sub_pages = elgg_get_entities($options)){
					foreach($sub_pages as $sub_page){
						$pos = array_search($sub_page->getGUID(), $order) + 1;
						
						$sub_page->order = $pos;
						$sub_page->parent_guid = $parent->getGUID();
						
						$sub_page->save();
					}
					
					system_message(elgg_echo("pages_tools:actions:reorder:success"));
				} else {
					register_error(elgg_echo("pages_tools:actions:reorder:error:subpages"));
				}
			} else {
				register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
			}
		} else {
			register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($parent_guid)));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	forward(REFERER);