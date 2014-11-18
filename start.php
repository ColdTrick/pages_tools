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
		
		// register JS library
		elgg_register_js("jquery.tree", elgg_get_site_url() . "mod/pages_tools/vendors/jstree/jquery.tree.min.js");
		elgg_register_css("jquery.tree", elgg_get_site_url() . "mod/pages_tools/vendors/jstree/themes/classic/style.css");
		
		// add widgets (overrule default pages widget, to add group support)
		elgg_register_widget_type("pages", elgg_echo("pages"), elgg_echo("pages:widget:description"), "profile,dashboard,groups");
		elgg_register_widget_type("index_pages", elgg_echo("pages"), elgg_echo("pages_tools:widgets:index_pages:description"), "index", true);
		
		// register plugin hooks
		elgg_register_plugin_hook_handler("route", "pages", "pages_tools_route_pages_hook");
		elgg_register_plugin_hook_handler("register", "menu:entity", "pages_tools_entity_menu_hook");
		elgg_register_plugin_hook_handler("permissions_check:comment", "object", "pages_tools_permissions_comment_hook");
		elgg_register_plugin_hook_handler("widget_url", "widget_manager", "pages_tools_widget_url_hook");
		elgg_register_plugin_hook_handler("cron", "daily", "pages_tools_daily_cron_hook");
		
		// register actions
		elgg_register_action("pages/export", dirname(__FILE__) . "/actions/export.php", "public");
		elgg_register_action("pages/reorder", dirname(__FILE__) . "/actions/reorder.php");
		
		elgg_register_action("pages_tools/update_edit_notice", dirname(__FILE__) . "/actions/update_edit_notice.php");
		
		// overrule action
		elgg_register_action("pages/edit", dirname(__FILE__) . "/actions/pages/edit.php");
		elgg_register_action("pages/delete", dirname(__FILE__) . "/actions/pages/delete.php");
	}
	
	// register default Elgg events
	elgg_register_event_handler("init", "system", "pages_tools_init");
