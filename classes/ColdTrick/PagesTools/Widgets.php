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
					$return_value = 'pages/group/' . $owner->getGUID() . '/all';
				} else {
					$return_value = 'pages/owner/' . $owner->username;
				}
				break;
			case 'index_pages':
				$return_value = 'pages/all';
				break;
		}
		
		return $return_value;
	}
}
