<?php
/**
 * All helper functions are bundled here
 */

/**
 * Check if an entity is a page/page_top
 *
 * @param ElggEntity $entity the entity to check
 *
 * @return bool
 */
function pages_tools_is_valid_page($entity) {
	
	if (empty($entity)) {
		return false;
	}
	
	if (elgg_instanceof($entity, "object", "page_top") || elgg_instanceof($entity, "object", "page")) {
		return true;
	}
	
	return false;
}

/**
 * Get the ordered list sub pages
 *
 * @param ElggObject $page the page to get subpages for
 *
 * @return false|ElggObject[]
 */
function pages_tools_get_ordered_children(ElggObject $page) {
	
	if (!pages_tools_is_valid_page($page)) {
		return false;
	}
	
	$options = array(
		"type" => "object",
		"subtype" => "page",
		"limit" => false,
		"metadata_name_value_pairs" => array("parent_guid" => $page->getGUID())
	);
	
	$children = elgg_get_entities_from_metadata($options);
	if (empty($children)) {
		return false;
	}
	 
	$result = array();
	
	foreach ($children as $child) {
		$order = $child->order;
		if ($order === NULL) {
			$order = $child->time_created;
		}
		
		while (array_key_exists($order, $result)) {
			$order++;
		}
		
		$result[$order] = $child;
	}
	
	ksort($result);
	
	return $result;
}

/**
 * Render the index in the export for every page below the provided page
 *
 * @param ElggObject $page the page to render for
 *
 * @return false|string
 */
function pages_tools_render_index(ElggObject $page) {
	
	if (!pages_tools_is_valid_page($page)) {
		return false;
	}
	
	$children = pages_tools_get_ordered_children($page);
	if (empty($children)) {
		return false;
	}
	
	$result = "";
	
	foreach ($children as $child) {
		$content = elgg_view("output/url", array(
			"text" => $child->title,
			"href" => "#page_" . $child->getGUID(),
			"title" => $child->title,
		));
		
		$child_index = pages_tools_render_index($child);
		if (!empty($child_index)) {
			$content .= elgg_format_element('ul', array(), $child_index);
		}
		
		$result .= elgg_format_element('li', array(), $content);
	}
	
	return $result;
}

/**
 * Render the subpages content for export of the child pages
 *
 * @param ElggObject $page the page to begin with
 *
 * @return false|string
 */
function pages_tools_render_childpages(ElggObject $page) {
	
	if (!pages_tools_is_valid_page($page)) {
		return false;
	}
	
	$children = pages_tools_get_ordered_children($page);
	if (empty($children)) {
		return false;
	}
	
	$result = "";
	
	foreach ($children as $child) {
		// title
		$result .= "<h3>" . elgg_view("output/url", array(
			"text" => $child->title,
			"href" => false,
			"name" => "page_" . $child->getgUID()
		)) . "</h3>";
		// content
		$result .= elgg_view("output/longtext", array("value" => $child->description));
		$result .= "<p style='page-break-after:always;'></p>";
		
		// sub pages
		$child_pages = pages_tools_render_childpages($child);
		if (empty($child_pages)) {
			continue;
		}
		
		$result .= $child_pages;
	}
	
	return $result;
}

/**
 * Register a complete tree to a menu in order to display navigation
 *
 * @param ElggObject $page the page to register menu items for
 *
 * @return bool
 */
function pages_tools_register_navigation_tree(ElggObject $entity) {
	
	if (!pages_tools_is_valid_page($entity)) {
		return false;
	}
	
	$root_page = pages_tools_get_root_page($entity);
	if (empty($root_page)) {
		return false;
	}
	
	$class = "";
	if (!$root_page->canEdit()) {
		$class = "no-edit";
	}
	
	if (!pages_tools_register_navigation_tree_children($root_page)) {
		return false;
	}
	
	// register root page
	elgg_register_menu_item("pages_nav", array(
		"name" => "page_" . $root_page->getGUID(),
		"text" => $root_page->title,
		"href" => $root_page->getURL(),
		"rel" => $root_page->getGUID(),
		"item_class" => $class,
		"link_class" => "pages-tools-wrap",
	));
	
	return true;
}

/**
 * Register page children to the navigation menu
 *
 * @param ElggObject $parent_entity the current page to check children for
 * @param int        $depth         current tree depth
 *
 * @return bool
 */
function pages_tools_register_navigation_tree_children(ElggObject $parent_entity, $depth = 0) {
	
	if (!pages_tools_is_valid_page($parent_entity)) {
		return false;
	}
	
	$children = pages_tools_get_ordered_children($parent_entity);
	if (empty($children)) {
		return false;
	}
	
	foreach ($children as $order => $child) {
		$class = "";
		if (!$child->canEdit()) {
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
		
		if ($depth < 4) {
			$params["link_class"] = "pages-tools-wrap";
		}
		
		// register this item to the menu
		elgg_register_menu_item("pages_nav", $params);
		
		// register child elements
		pages_tools_register_navigation_tree_children($child, $depth + 1);
	}
	
	return true;
}

/**
 * Find the root page for a page
 *
 * @param ElggObject $entity the page to find for
 *
 * @return false|ElggObject
 */
function pages_tools_get_root_page(ElggObject $entity) {
	
	if (!pages_tools_is_valid_page($entity)) {
		return false;
	}
	
	$result = false;
	if (elgg_instanceof($entity, "object", "page_top")) {
		$result = $entity;
	} elseif (isset($entity->parent_guid)) {
		$parent = get_entity($entity->parent_guid);
		
		if (!empty($parent)) {
			$result = pages_tools_get_root_page($parent);
		}
	}
	
	return $result;
}

/**
 * Are advanced publication options enabled
 *
 * @return bool
 */
function pages_tools_use_advanced_publication_options() {
	static $result;
	
	if (!isset($result)) {
		$result = false;
		
		$setting = elgg_get_plugin_setting("advanced_publication", "pages_tools");
		if ($setting === 'yes') {
			$result = true;
		}
	}
	
	return $result;
}

/**
 * Get the where selector for advanced publications
 *
 * @return array
 */
function pages_tools_get_publication_wheres(){
	static $result;
	
	if (!isset($result)) {
		$result = array();
		
		if (pages_tools_use_advanced_publication_options()) {
			$unpublished_id = elgg_get_metastring_id("unpublished");
			$dbprefix = elgg_get_config("dbprefix");
			
			$query = "(e.guid NOT IN (";
			$query .= "SELECT entity_guid";
			$query .= " FROM {$dbprefix}metadata";
			$query .= " WHERE name_id = {$unpublished_id}";
			$query .= "))";
			
			$result[] = $query;
		}
	}
	
	return $result;
}

/**
 * Gets tree html from cache
 * 
 * @param ElggEntity $entity root page to get the cache for
 * 
 * @return false|string
 */
function pages_tools_get_tree_html_from_cache(ElggEntity $entity) {
	if (!$entity) {
		return false;
	}
	
	$user_guid = elgg_get_logged_in_user_guid();
	if (empty($user_guid)) {
		$user_guid = 'logged-out';
	}
	
	$file_name = 'tree_cache/' . md5($user_guid . '-cached-' . $entity->getGUID()) . '.html';
	
	$fh = new ElggFile();
	$fh->owner_guid = $entity->getGUID();
	$fh->setFilename($file_name);
	
	if (!$fh->exists()) {
		return false;
	}

	return $fh->grabFile();
}

/**
 * Saves tree html to cache
 * 
 * @param ElggEntity $entity    root page entity to save data with
 * @param string     $tree_data the data to be saved
 * 
 * @return void
 */
function pages_tools_save_tree_html_to_cache(ElggEntity $entity, $tree_data = '') {
	if (!($entity instanceof ElggEntity) || empty($tree_data)) {
		return;
	}
	
	$user_guid = elgg_get_logged_in_user_guid();
	if (empty($user_guid)) {
		$user_guid = 'logged-out';
	}
	
	$file_name = 'tree_cache/' . md5($user_guid . '-cached-' . $entity->getGUID()) . '.html';
	
	$fh = new ElggFile();
	$fh->owner_guid = $entity->getGUID();
	$fh->setFilename($file_name);
	
	$fh->open('write');
	$fh->write($tree_data);
	$fh->close();	
}

/**
 * Clears tree html cache
 * 
 * @param ElggEntity $entity the root entity to flush the cache for
 * 
 * @return void
 */
function pages_tools_flush_tree_html_cache(ElggEntity $entity) {
	if (!($entity instanceof ElggEntity)) {
		return;
	}
	
	$locator = new \Elgg\EntityDirLocator($entity->getGUID());
	
	$cache_dir = elgg_get_data_path() . $locator->getPath() . 'tree_cache/';
	
	$dh = opendir($cache_dir);
	if (empty($dh)) {
		return $return;
	}
	
	while (($filename = readdir($dh)) !== false) {
		// make sure we have a file
		if (!is_file($cache_dir . $filename)) {
			continue;
		}
		
		unlink($cache_dir . $filename);
	}
}
