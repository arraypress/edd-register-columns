<?php
/**
 * Registration Functions
 *
 * Provides convenient helper functions for registering custom columns in Easy Digital Downloads.
 * These functions are in the global namespace for easy use throughout your codebase.
 *
 * @package     ArrayPress\EDD\RegisterColumns
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

use ArrayPress\EDD\RegisterColumns\Tables\Downloads;
use ArrayPress\EDD\RegisterColumns\Tables\Orders;
use ArrayPress\EDD\RegisterColumns\Tables\Customers;
use ArrayPress\EDD\RegisterColumns\Tables\Discounts;
use ArrayPress\EDD\RegisterColumns\Tables\Commissions;
use ArrayPress\EDD\RegisterColumns\Tables\Licenses;
use ArrayPress\EDD\RegisterColumns\Tables\Subscriptions;

if ( ! function_exists( 'edd_register_download_columns' ) ) {
	/**
	 * Register custom columns for EDD downloads/products.
	 *
	 * @param array $columns        Array of custom columns configuration.
	 * @param array $keys_to_remove Optional. Array of column keys to remove. Default empty array.
	 *
	 * @return Downloads|null The Downloads instance or null on error.
	 */
	function edd_register_download_columns( array $columns, array $keys_to_remove = [] ): ?Downloads {
		try {
			return new Downloads( $columns, $keys_to_remove );
		} catch ( Exception $e ) {
			edd_debug_log( 'EDD Register Columns Error: ' . $e->getMessage(), true );

			return null;
		}
	}
}

if ( ! function_exists( 'edd_register_order_columns' ) ) {
	/**
	 * Register custom columns for EDD orders.
	 *
	 * @param array $columns        Array of custom columns configuration.
	 * @param array $keys_to_remove Optional. Array of column keys to remove. Default empty array.
	 *
	 * @return Orders|null The Orders instance or null on error.
	 */
	function edd_register_order_columns( array $columns, array $keys_to_remove = [] ): ?Orders {
		try {
			return new Orders( $columns, $keys_to_remove );
		} catch ( Exception $e ) {
			edd_debug_log( 'EDD Register Columns Error: ' . $e->getMessage(), true );

			return null;
		}
	}
}

if ( ! function_exists( 'edd_register_customer_columns' ) ) {
	/**
	 * Register custom columns for EDD customers.
	 *
	 * @param array $columns        Array of custom columns configuration.
	 * @param array $keys_to_remove Optional. Array of column keys to remove. Default empty array.
	 *
	 * @return Customers|null The Customers instance or null on error.
	 */
	function edd_register_customer_columns( array $columns, array $keys_to_remove = [] ): ?Customers {
		try {
			return new Customers( $columns, $keys_to_remove );
		} catch ( Exception $e ) {
			edd_debug_log( 'EDD Register Columns Error: ' . $e->getMessage(), true );

			return null;
		}
	}
}

if ( ! function_exists( 'edd_register_discount_columns' ) ) {
	/**
	 * Register custom columns for EDD discounts.
	 *
	 * @param array $columns        Array of custom columns configuration.
	 * @param array $keys_to_remove Optional. Array of column keys to remove. Default empty array.
	 *
	 * @return Discounts|null The Discounts instance or null on error.
	 */
	function edd_register_discount_columns( array $columns, array $keys_to_remove = [] ): ?Discounts {
		try {
			return new Discounts( $columns, $keys_to_remove );
		} catch ( Exception $e ) {
			edd_debug_log( 'EDD Register Columns Error: ' . $e->getMessage(), true );

			return null;
		}
	}
}

if ( ! function_exists( 'edd_register_commission_columns' ) ) {
	/**
	 * Register custom columns for EDD commissions.
	 *
	 * Requires Easy Digital Downloads - Commissions extension to be installed and active.
	 *
	 * @param array $columns        Array of custom columns configuration.
	 * @param array $keys_to_remove Optional. Array of column keys to remove. Default empty array.
	 *
	 * @return Commissions|null The Commissions instance or null on error.
	 */
	function edd_register_commission_columns( array $columns, array $keys_to_remove = [] ): ?Commissions {
		try {
			return new Commissions( $columns, $keys_to_remove );
		} catch ( Exception $e ) {
			edd_debug_log( 'EDD Register Columns Error: ' . $e->getMessage(), true );

			return null;
		}
	}
}

if ( ! function_exists( 'edd_register_license_columns' ) ) {
	/**
	 * Register custom columns for EDD licenses.
	 *
	 * Requires Easy Digital Downloads - Software Licensing extension to be installed and active.
	 *
	 * @param array $columns        Array of custom columns configuration.
	 * @param array $keys_to_remove Optional. Array of column keys to remove. Default empty array.
	 *
	 * @return Licenses|null The Licenses instance or null on error.
	 */
	function edd_register_license_columns( array $columns, array $keys_to_remove = [] ): ?Licenses {
		try {
			return new Licenses( $columns, $keys_to_remove );
		} catch ( Exception $e ) {
			edd_debug_log( 'EDD Register Columns Error: ' . $e->getMessage(), true );

			return null;
		}
	}
}

if ( ! function_exists( 'edd_register_subscription_columns' ) ) {
	/**
	 * Register custom columns for EDD subscriptions.
	 *
	 * Requires Easy Digital Downloads - Recurring extension to be installed and active.
	 * Note: Sortable columns are not supported by EDD Recurring.
	 *
	 * @param array $columns        Array of custom columns configuration.
	 * @param array $keys_to_remove Optional. Array of column keys to remove. Default empty array.
	 *
	 * @return Subscriptions|null The Subscriptions instance or null on error.
	 */
	function edd_register_subscription_columns( array $columns, array $keys_to_remove = [] ): ?Subscriptions {
		try {
			return new Subscriptions( $columns, $keys_to_remove );
		} catch ( Exception $e ) {
			edd_debug_log( 'EDD Register Columns Error: ' . $e->getMessage(), true );

			return null;
		}
	}
}