<?php

namespace ColdTrick\PagesTools;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		elgg_extend_view('css/elgg', 'css/pages_tools/site.css');
		elgg_register_ajax_view('forms/pages/export');
	}
}
