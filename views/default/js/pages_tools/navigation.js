define(function(require) {
	var $ = require('jquery');
	var elgg = require('elgg');

	var init = function() {
		$('.elgg-menu-pages-nav > li ul').sortable({
			items: '> li',
			revert: true,
			connectWith: '.elgg-menu-pages-nav > li ul',
			forcePlaceholderSize: true,
			containment: 'parent',
			tolerance: 'pointer',
			start: function(event, ui) {
				$(ui.item).find(' > a').addClass('dragged');
			},
			update: function(event, ui) {
				
				if (!$(this).is($(ui.item).parent())) {
					// only trigger update on receiving sortable
					return;
				}
				
				var $parent = $(ui.item).parent().parent();
				var parent_guid = $parent.data('menuItem');
				var new_order = [];

				$parent.find('> ul > li').each(function(index, child) {
					new_order[index] = $(child).data('menuItem');
				});
				
				elgg.action('pages/reorder', {
					data: {
						guid: parent_guid,
						order: new_order
					}
				});
			}
		});

		$('.elgg-menu-pages-nav li a').on('click', function(event) {
			if ($(this).hasClass('dragged')) {
				event.preventDefault();
				event.stopImmediatePropagation();
				$(this).removeClass('dragged');
			}
		});

		$('.elgg-menu-pages-nav li a span').on('click', function(event) {

			if ($(this).closest('a').hasClass('dragged')) {
				return;
			}
			
			var href = $(this).closest('a').attr('href');
			document.location = href;

			event.preventDefault();
			event.stopImmediatePropagation();
		});
	};

	elgg.register_hook_handler('init', 'system', init);
});
