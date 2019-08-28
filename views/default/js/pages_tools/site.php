<?php
?>
//<script>
elgg.provide("elgg.pages_tools.tree");

elgg.pages_tools.tree.init = function() {

	// initialize tree
	$tree = $('#pages-tools-navigation');

	if ($tree.length) {
		$tree.tree({
			rules: {
				multiple: false,
				drag_copy: false
			},
			ui: {
				theme_name: "classic"
			},
			callback: {
				onload: function(tree) {
					$selected_branch = $tree.find('a[href="' + window.location.href + '"]').parent('li');

					tree.select_branch($selected_branch);
					tree.open_branch($selected_branch);

					$tree.next().hide();
					$tree.show();
				},
				onselect: function(node, tree) {
					var href = $(node).find('a:first').attr("href");

					if(window.location.href != href){
						window.location.href = href;
					}
				},
				beforemove: function(node, ref_node, type, tree_obj) {
					return !$(node).hasClass('no-edit');
				},
				onmove: function(node, ref_node, type, tree_obj, rb) {
					parent = tree_obj.parent(node);
					parent_guid = elgg.pages_tools.tree.get_guid_from_tree_element(parent);
					
					children = tree_obj.children(parent);
					order = new Array();

					$.each(children, function(index, elm) {
						order.push(elgg.pages_tools.tree.get_guid_from_tree_element(elm));
					});
					
					elgg.action("pages/reorder", {
						data: {
							parent_guid: parent_guid,
							order: order
						}
					});
				}
			}
		});
	}
}

elgg.pages_tools.tree.get_guid_from_tree_element = function(element) {
	return $(element).find('a:first').attr('rel');
};

elgg.register_hook_handler("init", "system", elgg.pages_tools.tree.init);
