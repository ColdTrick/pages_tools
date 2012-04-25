<?php

	require_once(dirname(__FILE__) . "/lib/functions.php");
	require_once(dirname(__FILE__) . "/lib/hooks.php");

	function pages_tools_init(){
		// register DOM PDF as a library
		elgg_register_library("dompdf", dirname(__FILE__) . "/vendors/dompdf.php");
		
		// extend site css
		elgg_extend_view("css/elgg", "pages_tools/css/site");
		
		// extend site js
		elgg_extend_view("js/elgg", "pages_tools/js/site");
	}

	// register default Elgg events
	elgg_register_event_handler("init", "system", "pages_tools_init");
	
	// register plugin hooks
	elgg_register_plugin_hook_handler("route", "pages", "pages_tools_route_pages_hook");
	elgg_register_plugin_hook_handler("register", "menu:entity", "pages_tools_entity_menu_hook");
	
	// register actions
	elgg_register_action("pages/export", dirname(__FILE__) . "/actions/export.php", "public");