<?php
/**
 * PDF Export functions
 */

namespace ColdTrick\PagesTools;

use Dompdf\Dompdf;

/**
 * Handles PDF exports
 */
class PDFExport {
	
	/**
	 * Add widget title url
	 *
	 * @param \ElggPage $entity           entity to generate PDF for
	 * @param string    $format           format of the pages
	 * @param bool      $include_index    add a index page
	 * @param bool      $include_subpages include the subpages
	 *
	 * @return void but streams the contents of the PDF
	 */
	public static function toPDF(\ElggPage $entity, string $format = 'a4', bool $include_index = false, bool $include_subpages = false): void {
		// begin of output
		$html = '';
		
		// make index
		if ($include_index) {
			$html .= elgg_format_element('h3', [], elgg_echo('pages_tools:export:index'));
			
			$html .= '<ul>';
			$html .= elgg_format_element('li', [], elgg_view('output/url', [
				'text' => $entity->getDisplayName(),
				'href' => '#page_' . $entity->guid,
				'title' => $entity->getDisplayName(),
			]));
			
			// include subpages
			if ($include_subpages) {
				$html .= self::renderIndex($entity);
			}
			
			$html .= '</ul>';
			$html .= elgg_format_element('p', ['style' => 'page-break-after:always;'], '');
		}
		
		// print page
		$html .= elgg_format_element('h3', [], elgg_view('output/url', [
			'text' => $entity->getDisplayName(),
			'href' => false,
			'name' => 'page_' . $entity->guid
		]));
		$html .= elgg_view('output/longtext', ['value' => $entity->description]);
		$html .= elgg_format_element('p', ['style' => 'page-break-after:always;'], '');
		
		// print subpages
		if ($include_subpages) {
			$html .= self::renderChildPages($entity);
		}
		
		// load library
		$dompdf = new Dompdf([
			'enable_remote' => true,
		]);
		// set correct page format
		$dompdf->setPaper($format);
		// set contents
		$dompdf->loadHtml($html);
		$dompdf->render();
		// output as download
		$dompdf->stream(elgg_get_friendly_title($entity->getDisplayName()) . '.pdf');
	}
	
	/**
	 * Get the ordered list sub-pages
	 *
	 * @param \ElggPage $page the page to get subpages for
	 *
	 * @return null|\ElggPage[]
	 */
	protected static function getOrderedChildren(\ElggPage $page): ?array {
		$children = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'page',
			'limit' => false,
			'metadata_name_value_pairs' => [
				'parent_guid' => $page->guid,
			],
		]);
		if (empty($children)) {
			return null;
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
	 * @param \ElggPage $page the page to render for
	 *
	 * @return string
	 */
	protected static function renderIndex(\ElggPage $page): string {
		$children = self::getOrderedChildren($page);
		if (empty($children)) {
			return '';
		}
		
		$result = '';
		
		foreach ($children as $child) {
			$content = elgg_view('output/url', [
				'text' => $child->getDisplayName(),
				'href' => '#page_' . $child->guid,
				'title' => $child->getDisplayName(),
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
	 * @param \ElggPage $page the page to begin with
	 *
	 * @return string
	 */
	protected static function renderChildPages(\ElggPage $page): string {
		$children = self::getOrderedChildren($page);
		if (empty($children)) {
			return '';
		}
		
		$result = '';
		
		foreach ($children as $child) {
			// title
			$result .= elgg_format_element('h3', [], elgg_view('output/url', [
				'text' => $child->getDisplayName(),
				'href' => false,
				'name' => 'page_' . $child->guid,
			]));
			// content
			$result .= elgg_view('output/longtext', ['value' => $child->description]);
			$result .= elgg_format_element('p', ['style' => 'page-break-after:always;'], '');
			
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
