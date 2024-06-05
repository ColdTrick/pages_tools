<?php
/**
 * Widget related functions
 */

namespace ColdTrick\PagesTools;

/**
 * Widgets callbacks
 */
class Widgets {
	
	/**
	 * Add widget title url
	 *
	 * @param \Elgg\Event $event 'entity:url', 'object:widget'
	 *
	 * @return null|string
	 */
	public static function widgetURL(\Elgg\Event $event): ?string {
		$return_value = $event->getValue();
		if (!empty($return_value)) {
			return null;
		}
		
		$widget = $event->getEntityParam();
		if (!$widget instanceof \ElggWidget || $widget->handler !== 'index_pages') {
			return null;
		}
		
		return elgg_generate_url('collection:object:pages:all');
	}
}
