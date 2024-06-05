<?php

namespace ColdTrick\PagesTools\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the pages_nav menu
 */
class PagesNav {
	
	/**
	 * Orders the pages navigation menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:pages_nav'
	 *
	 * @return void
	 */
	public static function orderPagesNav(\Elgg\Event $event): void {
		$load_js = false;
		
		/* @var $return_value MenuItems */
		$return_value = $event->getValue();
		
		/* @var $item \ElggMenuItem */
		foreach ($return_value as $item) {
			$guid = $item->getName();
			if (!is_numeric($guid)) {
				continue;
			}
			
			$entity = get_entity((int) $guid);
			if (!$entity instanceof \ElggEntity) {
				continue;
			}
			
			if (!$load_js && $entity->canEdit()) {
				$load_js = true;
			}
			
			$item->setPriority($entity->order ?: $entity->time_created);
		}
		
		if ($load_js) {
			elgg_import_esm('pages_tools/navigation');
		}
	}
}
