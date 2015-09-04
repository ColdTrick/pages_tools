<?php
/**
 * All event handlers are bundled here
 */

/**
 * Flush menu tree cache for root page
 *
 * @param string     $event  the name of the event
 * @param string     $type   the type of the event
 * @param ElggObject $object the object affected
 *
 * @return void
 */
function pages_tools_cache_handler($event, $type, ElggObject $object) {

	if (!pages_tools_is_valid_page($object)) {
		return;
	}

	$ia = elgg_set_ignore_access(true);
	
	$root_page = pages_tools_get_root_page($object);
	pages_tools_flush_tree_html_cache($root_page);	
	
	elgg_set_ignore_access($ia);	
}
