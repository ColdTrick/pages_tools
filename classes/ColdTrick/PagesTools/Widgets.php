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
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return void|string
	 */
	public static function widgetURL(\Elgg\Event $event) {
		
		$return_value = $event->getValue();
		if (!empty($return_value)) {
			return;
		}
		
		$widget = $event->getEntityParam();
		if (!$widget instanceof \ElggWidget) {
			return;
		}
		
		switch ($widget->handler) {
			case 'pages':
				$owner = $widget->getOwnerEntity();
				return $owner instanceof \ElggGroup ? 'pages/group/' . $owner->guid . '/all' : 'pages/owner/' . $owner->username;
			case 'index_pages':
				return 'pages/all';
		}
	}
}
