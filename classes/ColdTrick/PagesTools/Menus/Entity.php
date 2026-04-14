<?php

namespace ColdTrick\PagesTools\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the entity menu
 */
class Entity {
	
	/**
	 * Adds export menu item
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return null|MenuItems
	 */
	public static function registerExportPage(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggPage) {
			return null;
		}
		
		if (!elgg_get_plugin_setting('enable_export', 'pages_tools')) {
			return null;
		}
		
		/** @var MenuItems $return_value */
		$return_value = $event->getValue();
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'export',
			'icon' => 'download',
			'text' => elgg_echo('export'),
			'href' => elgg_generate_url('ajax', [
				'type' => 'form',
				'segments' => 'pages/export',
				'guid' => $entity->guid,
			]),
			'link_class' => 'elgg-lightbox',
		]);
		
		return $return_value;
	}
}
