<?php


namespace TechSpokes\LoginRedirects\Tools;

use WP_User;

/**
 * Trait GetWPRoles
 *
 * @package TechSpokes\LoginRedirects\Tools
 */
trait GetWPRoles {

	/**
	 * @return string[]|array
	 */
	protected static function get_editable_roles(): array {
		return wp_list_pluck( get_editable_roles(), 'name' );
	}

	/**
	 * @return string[]|array
	 */
	protected static function get_roles(): array {
		$roles = wp_roles()->roles;
		array_walk( $roles, function ( array &$value, string $role ) {
			$value = $role;
		} );

		return array_filter( $roles );
	}

	/**
	 * @param int $user_id
	 *
	 * @return string[]|array
	 */
	protected static function get_user_roles( int $user_id ): array {
		$user = get_userdata( $user_id );
		if ( $user instanceof WP_User ) {
			return array_filter( (array) $user->roles );
		}

		return array();
	}

}
