<?php


namespace TechSpokes\LoginRedirects\Tools;

/**
 * Trait IsEmptyStatic
 *
 * @package TechSpokes\LoginRedirects\Tools
 */
trait IsEmptyStatic {

	/**
	 * @param mixed|null $data
	 *
	 * @return bool
	 */
	public static function isEmpty( $data = null ): bool {
		return empty( $data );
	}

}
