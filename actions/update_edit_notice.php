<?php
/**
 * jQuery action to update the timestamp to show someone is editing a page
 */

$guid = (int) get_input("guid");

if (empty($guid)) {
	forward(REFERER);
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	forward(REFERER);
}

if (pages_tools_is_valid_page($entity)) {
	$entity->setPrivateSetting("edit_notice", time());
} else {
	register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:page"))));
}

forward(REFERER);
