<?php
	/**
	 * Create or edit a page
	 *
	 * @package ElggPages
	 */
	
	$user = elgg_get_logged_in_user_entity();
	$variables = elgg_get_config("pages");
	$input = array();
	foreach ($variables as $name => $type) {
		$input[$name] = get_input($name);
		if ($name == "title") {
			$input[$name] = htmlspecialchars(get_input($name, "", false), ENT_QUOTES, "UTF-8");
		}
		if ($type == "tags") {
			$input[$name] = string_to_tag_array($input[$name]);
		}
	}
	
	// Get guids
	$page_guid = (int) get_input("page_guid");
	$container_guid = (int) get_input("container_guid");
	$parent_guid = (int) get_input("parent_guid");
	
	// allow comments
	$allow_comments = get_input("allow_comments", "yes");
	
	elgg_make_sticky_form("page");
	
	if (!$input["title"]) {
		register_error(elgg_echo("pages:error:no_title"));
		forward(REFERER);
	}
	
	if ($page_guid) {
		$page = get_entity($page_guid);
		if (!$page || !$page->canEdit()) {
			register_error(elgg_echo("pages:error:no_save"));
			forward(REFERER);
		}
		$new_page = false;
	} else {
		$page = new ElggObject();
		if ($parent_guid) {
			$page->subtype = "page";
		} else {
			$page->subtype = "page_top";
		}
		$new_page = true;
	}
	
	if (sizeof($input) > 0) {
		// don't change access if not an owner/admin
		$can_change_access = true;
		
		if ($user && $page) {
			$can_change_access = ($user->isAdmin() || ($user->getGUID() == $page->owner_guid));
		}
		
		foreach ($input as $name => $value) {
			if (($name == "access_id" || $name == "write_access_id") && !$can_change_access) {
				continue;
			}
			if ($name == "parent_guid") {
				continue;
			}
			
			$page->$name = $value;
		}
	}
	
	// need to add check to make sure user can write to container
	$page->container_guid = $container_guid;
	
	// allow moving of subpages
	if ($parent_guid && ($parent_guid != $page_guid)) {
		// Check if parent isn't below the page in the tree
		if (!$new_page && ($page->parent_guid != $parent_guid)) {
			$tree_page = get_entity($parent_guid);
			
			while (($tree_page->parent_guid > 0) && ($page_guid != $tree_page->getGUID())) {
				$tree_page = get_entity($tree_page->parent_guid);
			}
			
			// If is below, bring all child elements forward
			if ($page_guid == $tree_page->getGUID()) {
				$previous_parent = (int) $page->parent_guid;
				
				$options = array(
					"type" => "object",
					"subtype" => "page",
					"container_guid" => $page->getContainerGUID(),
					"limit" => false,
					"metadata_name_value_pairs" => array(
						"name" => "parent_guid",
						"value" => $page->getGUID()
					)
				);
				
				if ($children = elgg_get_entities_from_metadata($options)) {
					foreach ($children as $child) {
						$child->parent_guid = $previous_parent;
					}
				}
			}
		}
		$page->parent_guid = $parent_guid;
	}
	
	// allow comments
	$page->allow_comments = $allow_comments;
	
	// check for publication/expiration date
	$publication_date = get_input("publication_date");
	$expiration_date = get_input("expiration_date");
	
	// first reset publication status
	unset($page->unpublished);
	
	$page->publication_date = $publication_date;
	if(!empty($publication_date)){
		if(strtotime($publication_date) > time()){
			$page->unpublished = true;
		}
	}
	
	$page->expiration_date = $expiration_date;
	if(!empty($expiration_date)){
		if($new_page){
			// new pages can't expire directly
			if(strtotime($expiration_date) < time()){
				register_error(elgg_echo("pages_tools:actions:edit:error:expiration_date"));
			}
		} else {
			if(strtotime($expiration_date) < time()){
				$page->unpublished = true;
			}
		}
	}
	
	// save the page
	if ($page->save()) {
	
		elgg_clear_sticky_form("page");
		
		// unset edit notice
		$page->removePrivateSetting("edit_notice");
		
		// Now save description as an annotation
		$page->annotate("page", $page->description, $page->access_id);
	
		system_message(elgg_echo("pages:saved"));
	
		if ($new_page && !$page->unpublished) {
			add_to_river("river/object/page/create", "create", $user->getGUID(), $page->getGUID());
		} elseif($page->getOwnerGUID() != $user->getGUID()) {
			// not the owner edited the page, notify the owner
			$subject = elgg_echo("pages_tools:notify:edit:subject", array($page->title));
			$msg = elgg_echo("pages_tools:notify:edit:message", array($page->title, $user->name, $page->getURL()));
			
			notify_user($page->getOwnerGUID(), $user->getGUID(), $subject, $msg);
		}
	
		forward($page->getURL());
	} else {
		register_error(elgg_echo("pages:error:no_save"));
		forward(REFERER);
	}
