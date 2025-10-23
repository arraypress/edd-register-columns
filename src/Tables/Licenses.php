<?php
/**
 * Licenses Class
 *
 * Handles custom column registration for Easy Digital Downloads Software Licensing.
 * Integrates with EDD Software Licensing table filters and provides license-specific functionality.
 *
 * @package     ArrayPress\EDD\RegisterColumns
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\EDD\RegisterColumns\Tables;

use ArrayPress\EDD\RegisterColumns\Abstracts\Columns;

/**
 * Class Licenses
 *
 * Manages custom columns for EDD licenses in the WordPress admin.
 *
 * @package ArrayPress\EDD\RegisterColumns
 */
class Licenses extends Columns {

	/**
	 * Object type for licenses.
	 *
	 * @var string
	 */
	protected const OBJECT_TYPE = 'edd_license';

	/**
	 * Load the necessary hooks for custom columns.
	 *
	 * Registers EDD Software Licensing hooks for adding, sorting, and displaying custom license columns.
	 *
	 * @return void
	 */
	protected function load_hooks(): void {
		add_filter( 'eddsl_get_admin_columns', [ $this, 'register_columns' ] );
		add_filter( 'eddsl_get_sortable_admin_columns', [ $this, 'register_sortable_columns' ] );
		$this->register_dynamic_column_filters();
	}

	/**
	 * Register dynamic filters for each custom column.
	 *
	 * EDD Software Licensing uses individual action hooks per column
	 * (edd_sl_column_{column_name}) similar to how customers work.
	 *
	 * @return void
	 */
	protected function register_dynamic_column_filters(): void {
		$custom_columns = self::get_columns( $this->object_type );

		foreach ( $custom_columns as $column_name => $column_config ) {
			add_action( "edd_sl_column_{$column_name}", function ( $item ) use ( $column_name ) {
				echo $this->render_license_column( $column_name, $item );
			} );
		}
	}

	/**
	 * Check if we are on the correct screen for custom columns.
	 *
	 * @return bool True if on the licenses screen, false otherwise.
	 */
	protected function is_screen(): bool {
		$screen = get_current_screen();

		return $screen && $screen->id === 'download_page_edd-licenses';
	}

	/**
	 * Render the license column content (implementation for the action hook).
	 *
	 * @param string $column_name The name/key of the current column.
	 * @param array  $item        The license data array.
	 *
	 * @return string The rendered column content.
	 */
	protected function render_license_column( string $column_name, array $item ): string {
		$column = $this->get_column_by_name( $column_name, $this->object_type );

		if ( ! $column ) {
			return '';
		}

		$license_id = $item['ID'] ?? 0;
		$license    = edd_software_licensing()->get_license( $license_id );

		if ( ! $license ) {
			return '';
		}

		return $this->render_custom_column_content( $license, $item, $column );
	}

	/**
	 * Render the custom column content (required by parent abstract class).
	 *
	 * @param string $value       The current value of the column.
	 * @param string $column_name The name/key of the current column.
	 * @param mixed  $object      The license object or data.
	 *
	 * @return string The rendered column content.
	 */
	public function render_column_content( string $value, string $column_name, $object ): string {
		return '';
	}

	/**
	 * Render the content for a custom column.
	 *
	 * @param object $license The license object.
	 * @param array  $item    The license data array.
	 * @param array  $column  The configuration array of the current column.
	 *
	 * @return string The rendered content.
	 */
	private function render_custom_column_content( $license, array $item, array $column ): string {
		// If there's a display callback, use it
		if ( is_callable( $column['display_callback'] ) ) {
			// If there's a meta_key, pass the value as first parameter
			if ( ! empty( $column['meta_key'] ) ) {
				$value = edd_software_licensing()->license_meta_db->get_meta( $license->ID, $column['meta_key'], true );

				return (string) call_user_func( $column['display_callback'], $value, $license, $item );
			}

			// Otherwise just pass the license object and item array
			return (string) call_user_func( $column['display_callback'], $license, $item );
		}

		// Default behavior: show meta value
		$meta_key = $column['meta_key'] ?? '';
		$value    = edd_software_licensing()->license_meta_db->get_meta( $license->ID, $meta_key, true );

		if ( empty( $value ) ) {
			return '—';
		}

		return esc_html( $value );
	}

}