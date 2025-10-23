<?php
/**
 * Downloads Class
 *
 * Handles custom column registration for Easy Digital Downloads products/downloads.
 * Since downloads are a custom post type, this class integrates with WordPress's
 * post column filters while maintaining EDD-specific functionality.
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
 * Class Downloads
 *
 * Manages custom columns for EDD downloads/products in the WordPress admin.
 *
 * @package ArrayPress\EDD\RegisterColumns
 */
class Downloads extends Columns {

	/**
	 * Object type for downloads.
	 *
	 * @var string
	 */
	protected const OBJECT_TYPE = 'download';

	/**
	 * Load the necessary hooks for custom columns.
	 *
	 * Registers WordPress hooks for adding, sorting, and displaying custom download columns.
	 *
	 * @return void
	 */
	protected function load_hooks(): void {
		add_filter( 'manage_edit-download_columns', [ $this, 'register_columns' ] );
		add_filter( 'manage_edit-download_sortable_columns', [ $this, 'register_sortable_columns' ] );
		add_action( 'manage_posts_custom_column', [ $this, 'render_column_content_wrapper' ], 10, 2 );
		add_action( 'pre_get_posts', [ $this, 'sort_items' ] );
	}

	/**
	 * Check if we are on the correct screen for custom columns.
	 *
	 * @return bool True if on the downloads screen, false otherwise.
	 */
	protected function is_screen(): bool {
		$screen = get_current_screen();

		return $screen && $screen->post_type === 'download' && $screen->base === 'edit';
	}

	/**
	 * Render the custom column content wrapper for downloads.
	 *
	 * @param string $column_name The name of the column.
	 * @param int    $download_id The download ID.
	 *
	 * @return void
	 */
	public function render_column_content_wrapper( string $column_name, int $download_id ): void {
		echo $this->render_column_content( '', $column_name, $download_id );
	}

	/**
	 * Render the custom column content.
	 *
	 * @param string $value       The current value of the column.
	 * @param string $column_name The name/key of the current column.
	 * @param mixed  $download_id The download ID.
	 *
	 * @return string The rendered column content.
	 */
	public function render_column_content( string $value, string $column_name, $download_id ): string {
		$column = $this->get_column_by_name( $column_name, $this->object_type );

		if ( ! $column ) {
			return $value;
		}

		return $this->render_custom_column_content( $download_id, $column );
	}

	/**
	 * Render the content for a custom column.
	 *
	 * @param int   $download_id The download ID.
	 * @param array $column      The configuration array of the current column.
	 *
	 * @return string The rendered content.
	 */
	private function render_custom_column_content( int $download_id, array $column ): string {
		// If there's a display callback, use it
		if ( is_callable( $column['display_callback'] ) ) {
			// For downloads, if there's a meta_key, pass the value as first parameter
			if ( ! empty( $column['meta_key'] ) ) {
				$value = get_post_meta( $download_id, $column['meta_key'], true );

				return (string) call_user_func( $column['display_callback'], $value, $download_id );
			}

			// Otherwise just pass the download ID
			return (string) call_user_func( $column['display_callback'], $download_id );
		}

		// Default behavior: show meta value
		$meta_key = $column['meta_key'] ?? '';
		$value    = get_post_meta( $download_id, $meta_key, true );

		if ( empty( $value ) ) {
			return '—';
		}

		return esc_html( $value );
	}

	/**
	 * Sort the downloads based on custom columns.
	 *
	 * @param \WP_Query $query The query instance.
	 *
	 * @return void
	 */
	public function sort_items( $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		// Ensure $orderby is a valid string before proceeding
		if ( ! is_string( $orderby ) ) {
			return;
		}

		$column = $this->get_column_by_name( $orderby, $this->object_type );

		// Ensure the column exists and is sortable
		if ( ! $column || ! $column['sortable'] ) {
			return;
		}

		$meta_key     = $column['meta_key'] ?? '';
		$sortby       = $column['sortby'] ?? '';
		$sort_numeric = $column['numeric'] ?? false;

		// Priority 1: Use sortby if explicitly set (most flexible)
		if ( ! empty( $sortby ) ) {
			$query->set( 'orderby', $sortby );
		} // Priority 2: If there's a meta_key, sort by meta value
		elseif ( ! empty( $meta_key ) ) {
			$query->set( 'meta_key', $meta_key );
			$query->set( 'orderby', $sort_numeric ? 'meta_value_num' : 'meta_value' );
		} // Priority 3: No meta_key or sortby means it's a post property
		else {
			// For numeric sorting without meta, assume ID
			if ( $sort_numeric ) {
				$query->set( 'orderby', 'ID' );
			}
		}
	}

}