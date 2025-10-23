<?php
/**
 * Commissions Class
 *
 * Handles custom column registration for Easy Digital Downloads Commissions.
 * Integrates with EDD Commissions table filters and provides commission-specific functionality.
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
 * Class Commissions
 *
 * Manages custom columns for EDD commissions in the WordPress admin.
 *
 * @package ArrayPress\EDD\RegisterColumns
 */
class Commissions extends Columns {

	/**
	 * Object type for commissions.
	 *
	 * @var string
	 */
	protected const OBJECT_TYPE = 'edd_commission';

	/**
	 * Load the necessary hooks for custom columns.
	 *
	 * Registers EDD Commissions hooks for adding, sorting, and displaying custom commission columns.
	 *
	 * @return void
	 */
	protected function load_hooks(): void {
		add_filter( 'manage_edd_commissions_columns', [ $this, 'register_columns' ] );
		$this->register_dynamic_column_filters();
	}

	/**
	 * Register dynamic filters for each custom column.
	 *
	 * EDD Commissions uses a single action hook for custom columns
	 * (manage_edd_commissions_custom_column) rather than individual filters.
	 *
	 * @return void
	 */
	protected function register_dynamic_column_filters(): void {
		add_action( 'manage_edd_commissions_custom_column', [ $this, 'render_commission_column' ], 10, 2 );
	}

	/**
	 * Wrapper for rendering commission column content.
	 *
	 * @param string $column_name   The name/key of the current column.
	 * @param int    $commission_id The commission ID.
	 *
	 * @return void
	 */
	public function render_commission_column( string $column_name, int $commission_id ): void {
		$content = $this->render_commission_column_content( $column_name, $commission_id );

		if ( ! empty( $content ) ) {
			echo $content;
		}
	}

	/**
	 * Check if we are on the correct screen for custom columns.
	 *
	 * @return bool True if on the commissions screen, false otherwise.
	 */
	protected function is_screen(): bool {
		$screen = get_current_screen();

		return $screen && $screen->id === 'download_page_edd-commissions';
	}

	/**
	 * Render the commission column content (implementation for the action hook).
	 *
	 * @param string $column_name   The name/key of the current column.
	 * @param int    $commission_id The commission ID.
	 *
	 * @return string The rendered column content.
	 */
	protected function render_commission_column_content( string $column_name, int $commission_id ): string {
		$column = $this->get_column_by_name( $column_name, $this->object_type );

		if ( ! $column ) {
			return '';
		}

		$commission = eddc_get_commission( $commission_id );

		if ( ! $commission ) {
			return '';
		}

		return $this->render_custom_column_content( $commission, $column );
	}

	/**
	 * Render the custom column content (required by parent abstract class).
	 *
	 * @param string $value       The current value of the column.
	 * @param string $column_name The name/key of the current column.
	 * @param mixed  $object      The commission object or data.
	 *
	 * @return string The rendered column content.
	 */
	public function render_column_content( string $value, string $column_name, $object ): string {
		// For commissions, this method isn't directly called by EDD
		// The render_commission_column method handles the actual rendering
		return '';
	}

	/**
	 * Render the content for a custom column.
	 *
	 * @param object $commission The commission object.
	 * @param array  $column     The configuration array of the current column.
	 *
	 * @return string The rendered content.
	 */
	private function render_custom_column_content( $commission, array $column ): string {
		// If there's a display callback, use it
		if ( is_callable( $column['display_callback'] ) ) {
			// If there's a meta_key, pass the value as first parameter
			if ( ! empty( $column['meta_key'] ) ) {
				$value = eddc_get_commission_meta( $commission->id, $column['meta_key'], true );

				return (string) call_user_func( $column['display_callback'], $value, $commission );
			}

			// Otherwise just pass the commission object
			return (string) call_user_func( $column['display_callback'], $commission );
		}

		// Default behavior: show meta value
		$meta_key = $column['meta_key'] ?? '';
		$value    = eddc_get_commission_meta( $commission->id, $meta_key, true );

		if ( empty( $value ) ) {
			return '—';
		}

		return esc_html( $value );
	}

}