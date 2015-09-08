<?php
/**
 * Parent picker
 *
 * @uses $vars['value']          The current value, if any
 * @uses $vars['options_values']
 * @uses $vars['name']           The name of the input field
 * @uses $vars['entity']         Optional. The child entity (uses container_guid)
 */

$entity = elgg_extract('entity', $vars);
if (!pages_tools_is_valid_page($entity)) {
	return;
}

$tree = pages_tools_get_parent_selector_options($entity);
if (empty($tree)) {
	return;
}

$defaults = array(
	'class' => 'elgg-pages-input-parent-picker',
	'options_values' => $tree,
);

$vars = array_merge($defaults, $vars);

echo elgg_view('input/dropdown', $vars);
