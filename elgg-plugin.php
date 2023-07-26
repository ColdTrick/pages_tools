<?php

return [
	'plugin' => [
		'version' => '7.0.1',
		'dependencies' => [
			'pages' => [
				'position' => 'after',
			],
		],
	],
	'settings' => [
		'enable_export' => 0,
	],
	'actions' => [
		'pages/export' => ['access' => 'public'],
		'pages/reorder' => [],
	],
	'events' => [
		'entity:url' => [
			'object' => [
				'\ColdTrick\PagesTools\Widgets::widgetURL' => [],
			],
		],
		'register' => [
			'menu:entity' => [
				'\ColdTrick\PagesTools\Menus\Entity::registerExportPage' => [],
			],
			'menu:pages_nav' => [
				'\ColdTrick\PagesTools\Menus\PagesNav::orderPagesNav' => ['priority' => 999],
			],
		],
	],
	'view_options' => [
		'forms/pages/export' => ['ajax' => true],
	],
	'widgets' => [
		'index_pages' => [
			'context' => ['index'],
			'multiple' => true,
		],
		// overrule default pages widget, to add group support
		'pages' => [
			'context' => ['profile', 'dashboard', 'groups'],
		],
	],
];
