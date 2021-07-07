<?php

return [
	'plugin' => [
		'version' => '4.0.3',
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
	'hooks' => [
		'entity:url' => [
			'object' => [
				'\ColdTrick\PagesTools\Widgets::widgetURL' => [],
			],
		],
		'register' => [
			'menu:entity' => [
				'\ColdTrick\PagesTools\Menus::registerExportPage' => [],
			],
			'menu:pages_nav' => [
				'\ColdTrick\PagesTools\Menus::orderPagesNav' => ['priority' => 999],
			],
		],
	],
];
