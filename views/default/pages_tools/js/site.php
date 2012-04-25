<?php
?>

elgg.provide("elgg.pages_tools");

elgg.pages_tools.init = function(){
	$("a.pages-tools-lightbox").fancybox({
		titleShow: false
	});
	
	$("#pages-tools-export-form .elgg-button").live("click", function(){
		$.fancybox.close();
	});
}

elgg.register_hook_handler("init", "system", elgg.pages_tools.init);