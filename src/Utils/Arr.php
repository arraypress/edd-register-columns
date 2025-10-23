<?php
/**
 * Helpers Class
 *
 * Provides minimal utility methods for array manipulation and request parameter handling.
 * Display helpers are intentionally NOT included - use EDD's built-in HTML classes instead.
 *
 * @package     ArrayPress\EDD\RegisterColumns
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\EDD\RegisterColumns\Utils;

/**
 * Class Helpers
 *
 * Utility class for array operations and request parameter handling.
 *
 * @package ArrayPress\EDD\RegisterColumns
 */
class Arr {

	/**
	 * Insert an element after a specific key in an array.
	 *
	 * @param array  $array The original array.
	 * @param string $key   The key to insert after.
	 * @param array  $new   The new element to insert.
	 *
	 * @return array The updated array.
	 */
	public static function insert_after( array $array, string $key, array $new ): array {
		$position = array_search( $key, array_keys( $array ) );

		if ( $position === false ) {
			$position = count( $array );
		} else {
			$position += 1;
		}

		return array_slice( $array, 0, $position, true ) +
		       $new +
		       array_slice( $array, $position, null, true );
	}

	/**
	 * Insert an element before a specific key in an array.
	 *
	 * @param array  $array The original array.
	 * @param string $key   The key to insert before.
	 * @param array  $new   The new element to insert.
	 *
	 * @return array The updated array.
	 */
	public static function insert_before( array $array, string $key, array $new ): array {
		$position = array_search( $key, array_keys( $array ) );

		if ( $position === false ) {
			$position = 0;
		}

		return array_slice( $array, 0, $position, true ) +
		       $new +
		       array_slice( $array, $position, null, true );
	}

}