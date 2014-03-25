<?php
/**
 * jQuery action to update the timestamp to show someone is editing a page
 */

$guid = (int) get_input("guid");

if (!empty($guid)) {
	$entity = get_entity($guid);
	
	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", "page_top") || elgg_instanceof($entity, "object", "page")) {
			$entity->setPrivateSetting("edit_notice", time());
		} else {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:page"))));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward(REFERER);
