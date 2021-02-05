<?php


namespace TechSpokes\LoginRedirects\Tools;

/**
 * Trait GetWPRoles
 *
 * @package TechSpokes\LoginRedirects\Tools
 */
trait GetWPRoles {

	/**
	 * @return array
	 */
	protected static function get_roles(): array {
		return wp_list_pluck( get_editable_roles(), 'name' );
	}

}
