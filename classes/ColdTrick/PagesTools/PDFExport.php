<?php
/**
 * PDF Export functions
 */

namespace ColdTrick\PagesTools;

class PDFExport {
	
	/**
	 * Add widget title url
	 *
	 * @param \ElggPage $entity           entity to generate PDF for
	 * @param string    $format           format of the pages
	 * @param bool      $include_index    add a index page
	 * @param bool      $include_subpages include the subpages
	 *
	 * @return string contents for PDF
	 */
	public static function toPDF(\ElggPage $entity, string $format = 'a4', bool $include_index = false, bool $include_subpages = false) {
		if (!defined('DOMPDF_ENABLE_AUTOLOAD')) {
			define('DOMPDF_ENABLE_AUTOLOAD', false);
		}
		
		// begin of output
		$html = "";
		
		// make index
		if ($include_index) {
			$html .= "<h3>" . elgg_echo("pages_tools:export:index") . "</h3>";
			
			$html .= "<ul>";
			$html .= "<li>" . elgg_view("output/url", [
				"text" => $entity->getDisplayName(),
				"href" => "#page_" . $entity->guid,
				"title" => $entity->getDisplayName(),
			]) . "</li>";
			
			// include subpages
			if ($include_subpages && ($sub_index = self::renderIndex($entity))) {
				$html .= $sub_index;
			}
			
			$html .= "</ul>";
			$html .= "<p style='page-break-after:always;'></p>";
		}
		
		// print page
		$html .= "<h3>" . elgg_view("output/url", [
			"text" => $entity->getDisplayName(),
			"href" => false,
			"name" => "page_" . $entity->guid
		]) . "</h3>";
		$html .= elgg_view("output/longtext", ["value" => $entity->description]);
		$html .= "<p style='page-break-after:always;'></p>";
		
		// print subpages
		if ($include_subpages && ($child_pages = self::renderChildPages($entity))) {
			$html .= $child_pages;
		}
		
		// load library
		require_once(elgg_get_plugins_path() . 'pages_tools/vendor/dompdf/dompdf/dompdf_config.inc.php');
		
		$dompdf = new \DOMPDF();
		// set correct page format
		$dompdf->set_paper($format);
		// set contents
		$dompdf->load_html($html);
		$dompdf->render();
		// output as download
		$dompdf->stream(elgg_get_friendly_title($entity->getDisplayName()) . ".pdf");
	}
	
	/**
	 * Get the ordered list sub pages
	 *
	 * @param ElggObject $page the page to get subpages for
	 *
	 * @return false|ElggObject[]
	 */
	protected static function getOrderedChildren(\ElggPage $page) {
		
		if (!$page instanceof \ElggPage) {
			return false;
		}
			
		$children = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'page',
			'limit' => false,
			'metadata_name_value_pairs' => ['parent_guid' => $page->guid],
		]);
		if (empty($children)) {
			return false;
		}
		 
		$result = [];
		
		foreach ($children as $child) {
			$order = $child->order;
			if ($order === null) {
				$order = $child->time_created;
			}
			
			while (array_key_exists($order, $result)) {
				$order++;
			}
			
			$result[$order] = $child;
		}
		
		ksort($result);
		
		return $result;
	}
	
	/**
	 * Render the index in the export for every page below the provided page
	 *
	 * @param ElggObject $page the page to render for
	 *
	 * @return false|string
	 */
	protected static function renderIndex(\ElggPage $page) {
		
		$children = self::getOrderedChildren($page);
		if (empty($children)) {
			return false;
		}
		
		$result = "";
		
		foreach ($children as $child) {
			$content = elgg_view("output/url", [
				"text" => $child->getDisplayName(),
				"href" => "#page_" . $child->guid,
				"title" => $child->getDisplayName(),
			]);
			
			$child_index = self::renderIndex($child);
			if (!empty($child_index)) {
				$content .= elgg_format_element('ul', [], $child_index);
			}
			
			$result .= elgg_format_element('li', [], $content);
		}
		
		return $result;
	}
	
	/**
	 * Render the subpages content for export of the child pages
	 *
	 * @param ElggObject $page the page to begin with
	 *
	 * @return false|string
	 */
	protected static function renderChildPages(\ElggPage $page) {
		
		$children = self::getOrderedChildren($page);
		if (empty($children)) {
			return false;
		}
		
		$result = "";
		
		foreach ($children as $child) {
			// title
			$result .= "<h3>" . elgg_view("output/url", [
				"text" => $child->getDisplayName(),
				"href" => false,
				"name" => "page_" . $child->guid,
			]) . "</h3>";
			// content
			$result .= elgg_view("output/longtext", ["value" => $child->description]);
			$result .= "<p style='page-break-after:always;'></p>";
			
			// sub pages
			$child_pages = self::renderChildPages($child);
			if (empty($child_pages)) {
				continue;
			}
			
			$result .= $child_pages;
		}
		
		return $result;
	}
}
