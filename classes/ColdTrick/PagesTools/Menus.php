<?php
/**
 * Register misc menu items to different menus
 */

namespace ColdTrick\PagesTools;

/**
 * Menu callbacks
 */
class Menus {
		
	/**
	 * Adds export menu item
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerExportPage(\Elgg\Event $event) {
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggPage) {
			return;
		}
		
		if (!elgg_get_plugin_setting('enable_export', 'pages_tools')) {
			return;
		}
		
		$return_value = $event->getValue();
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'export',
			'icon' => 'download',
			'text' => elgg_echo('export'),
			'href' => 'ajax/form/pages/export?guid=' . $entity->guid,
			'link_class' => 'elgg-lightbox',
		]);
		
		return $return_value;
	}
		
	/**
	 * Orders the pages navigation menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:pages_nav'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function orderPagesNav(\Elgg\Event $event) {
		$load_js = false;
		
		$return_value = $event->getValue();
		foreach ($return_value as $item) {
			$entity = get_entity($item->getName());
			if (!$load_js && $entity->canEdit()) {
				$load_js = true;
			}
			
			$item->setPriority($entity->order ?: $entity->time_created);
		}
		
		if ($load_js) {
			elgg_require_js('pages_tools/navigation');
		}
	}
}
