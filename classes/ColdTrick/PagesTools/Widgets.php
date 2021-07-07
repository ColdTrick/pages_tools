<?php
/**
 * Widget related functions
 */

namespace ColdTrick\PagesTools;

class Widgets {
	
	/**
	 * Add widget title url
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:entity'
	 *
	 * @return void|string
	 */
	public static function widgetURL(\Elgg\Hook $hook){
		
		$return_value = $hook->getValue();
		if (!empty($return_value)) {
			return;
		}
		
		$widget = $hook->getEntityParam();
		if (!$widget instanceof \ElggWidget) {
			return;
		}
		
		switch ($widget->handler) {
			case 'pages':
				$owner = $widget->getOwnerEntity();
				
				if ($owner instanceof \ElggGroup) {
					return 'pages/group/' . $owner->guid . '/all';
				}
				
				return 'pages/owner/' . $owner->username;
			case 'index_pages':
				return 'pages/all';
		}
	}
}
