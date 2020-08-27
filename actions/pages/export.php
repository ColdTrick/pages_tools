<?php
/**
 * Export a page (and subpages) to PDF
 */

use ColdTrick\PagesTools\PDFExport;
use Elgg\EntityNotFoundException;

$guid = (int) get_input('guid');
$format = strtolower(get_input('format', 'a4'));
$include_subpages = (bool) get_input('include_children');
$include_index = (bool) get_input('include_index');

$page = get_entity($guid);
if (!$page instanceof ElggPage) {
	throw new EntityNotFoundException();
}

// this could take a while
set_time_limit(0);

try {
	PDFExport::toPDF($page, $format, $include_subpages, $include_index);
	exit();
} catch (Exception $e) {
	return elgg_error_response($e->getMessage());
}
