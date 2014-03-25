<?php

	$english = array(
		// general
		'pages_tools:allow_comments' => "Allow comments",
		'pages_tools:unpublished' => "Unpublished",
		
		// notification
		'pages_tools:notify:edit:subject' => "Your page '%s' was edited",
		'pages_tools:notify:edit:message' => "Hi,
		
Your page '%s' was edited by %s. Check out the new version here:
%s",
	
		'pages_tools:notify:publish:subject' => "A page has been published",
		'pages_tools:notify:publish:message' => "Hi,
		
your page '%s' has been published.

You can view your page here:
%s",
		
		'pages_tools:notify:expire:subject' => "A page has expired",
		'pages_tools:notify:expire:message' => "Hi,
		
your page '%s' has expired.

You can view your page here:
%s",
		
		// export page
		'page_tools:export:format' => "Page format",
		'page_tools:export:format:a4' => "A4",
		'page_tools:export:format:letter' => "Letter",
		'page_tools:export:format:a3' => "A3",
		'page_tools:export:format:a5' => "A5",
		
		'page_tools:export:include_subpages' => "Include subpages",
		'page_tools:export:include_index' => "Include index",
		
		'pages_tools:navigation:tooltip' => "Did you know you can drag-and-drop pages to reorder the navigation tree?",
		
		// widget
		'pages_tools:widgets:index_pages:description' => "Show the latest pages on your community",
		
		// settings
		'pages_tools:settings:advanced_publication' => "Allow advanced publication options",
		'pages_tools:settings:advanced_publication:description' => "With this users can select a publication and expiration date for pages. Requires a working daily CRON.",
		
		// edit
		'pages_tools:label:publication_options' => "Publication options",
		'pages_tools:label:publication_date' => "Publication date (optional)",
		'pages_tools:publication_date:description' => "When you select a date here the page will not be published until the selected date.",
		'pages_tools:label:expiration_date' => "Expiration date (optional)",
		'pages_tools:expiration_date:description' => "The page will no longer be published after the selected date.",
		
		'pages_tools:edit:confirm' => "Someone is currently editing this page!\nAre you sure you also wish to edit this page?",
		
		// actions
		// export
		'pages_tools:export:index' => "Contents",
		
		// reorder pages
		'pages_tools:actions:reorder:error:subpages' => "No pages to reorder were supplied",
		'pages_tools:actions:reorder:success' => "Successfully reordered the pages",
		'' => "",
	
	);
	
	add_translation("en", $english);