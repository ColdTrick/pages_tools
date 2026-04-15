<?php
/**
 * Export a page (and subpages) to PDF
 */

use ColdTrick\PagesTools\PDFExport;

$guid = (int) get_input('guid');
$format = strtolower(get_input('format', 'a4'));
$include_subpages = (bool) get_input('include_children');
$include_index = (bool) get_input('include_index');

$page = get_entity($guid);
if (!$page instanceof \ElggPage) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

try {
	$contents = PDFExport::toPDF($page, $format, $include_subpages, $include_index);
	
	return elgg_download_response($contents, elgg_get_friendly_title($page->getDisplayName()) . '.pdf', false, [
		'content-type' => 'application/pdf',
	]);
} catch (\Throwable $t) {
	return elgg_error_response($t->getMessage());
}
