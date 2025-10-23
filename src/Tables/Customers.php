<?php
/**
 * Customers Class
 *
 * Handles custom column registration for Easy Digital Downloads customers.
 * Integrates with EDD's customer table filters and provides customer-specific functionality.
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
 * Class Customers
 *
 * Manages custom columns for EDD customers in the WordPress admin.
 *
 * @package ArrayPress\EDD\RegisterColumns
 */
class Customers extends Columns {

	/**
	 * Object type for customers.
	 *
	 * @var string
	 */
	protected const OBJECT_TYPE = 'edd_customer';

	/**
	 * Load the necessary hooks for custom columns.
	 *
	 * Registers EDD hooks for adding, sorting, and displaying custom customer columns.
	 *
	 * @return void
	 */
	protected function load_hooks(): void {
		add_filter( 'edd_report_customer_columns', [ $this, 'register_columns' ] );
		$this->register_dynamic_column_filters();
	}

	/**
	 * Register dynamic filters for each custom column.
	 *
	 * EDD uses individual filters per column (edd_customers_column_{column_name})
	 * rather than a unified rendering hook, so we need to register a filter for each.
	 *
	 * @return void
	 */
	protected function register_dynamic_column_filters(): void {
		$custom_columns = self::get_columns( $this->object_type );

		foreach ( $custom_columns as $column_name => $column_config ) {
			add_filter( "edd_customers_column_{$column_name}", function ( $value, $customer_id ) use ( $column_name ) {
				return $this->render_column_content( (string) $value, (string) $column_name, (int) $customer_id );
			}, 10, 2 );
		}
	}

	/**
	 * Check if we are on the correct screen for custom columns.
	 *
	 * @return bool True if on the customers screen, false otherwise.
	 */
	protected function is_screen(): bool {
		$screen = get_current_screen();

		return $screen && $screen->id === 'download_page_edd-customers';
	}

	/**
	 * Render the custom column content.
	 *
	 * @param string $value       The current value of the column.
	 * @param string $column_name The name/key of the current column.
	 * @param mixed  $object      The customer ID.
	 *
	 * @return string The rendered column content.
	 */
	public function render_column_content( string $value, string $column_name, $object ): string {
		$customer_id = is_numeric( $object ) ? (int) $object : $object->id;
		$column      = $this->get_column_by_name( $column_name, $this->object_type );

		if ( ! $column ) {
			return $value;
		}

		$customer = edd_get_customer( $customer_id );

		if ( ! $customer ) {
			return $value;
		}

		return $this->render_custom_column_content( $customer, $column );
	}

	/**
	 * Render the content for a custom column.
	 *
	 * @param object $customer The customer object.
	 * @param array  $column   The configuration array of the current column.
	 *
	 * @return string The rendered content.
	 */
	private function render_custom_column_content( $customer, array $column ): string {
		// If there's a display callback, use it
		if ( is_callable( $column['display_callback'] ) ) {
			// If there's a meta_key, pass the value as first parameter
			if ( ! empty( $column['meta_key'] ) ) {
				$value = edd_get_customer_meta( $customer->id, $column['meta_key'], true );

				return (string) call_user_func( $column['display_callback'], $value, $customer );
			}

			// Otherwise just pass the customer object
			return (string) call_user_func( $column['display_callback'], $customer );
		}

		// Default behavior: show meta value
		$meta_key = $column['meta_key'] ?? '';
		$value    = edd_get_customer_meta( $customer->id, $meta_key, true );

		if ( empty( $value ) ) {
			return '—';
		}

		return esc_html( $value );
	}

}