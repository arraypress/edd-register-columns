# Easy Digital Downloads - Register Columns

A lightweight library for adding custom columns to Easy Digital Downloads admin tables.

## Installation

```bash
composer require arraypress/edd-register-columns
```

## Requirements

- PHP 7.4+
- WordPress 5.0+
- Easy Digital Downloads 3.0+

## Quick Start

### Downloads

```php
edd_register_download_columns( [
    'sku' => [
        'label'            => __( 'SKU', 'text-domain' ),
        'meta_key'         => 'edd_sku',
        'sortable'         => true,
        'position'         => 'after:title',
    ],
] );
```

### Orders

```php
edd_register_order_columns( [
    'tax_amount' => [
        'label'            => __( 'Tax', 'text-domain' ),
        'display_callback' => function( $order ) {
            return edd_currency_filter( edd_format_amount( $order->tax ) );
        },
        'sortable'         => true,
        'position'         => 'after:amount',
    ],
] );
```

### Customers

```php
edd_register_customer_columns( [
    'lifetime_value' => [
        'label'            => __( 'LTV', 'text-domain' ),
        'display_callback' => function( $customer ) {
            return edd_currency_filter( edd_format_amount( $customer->purchase_value ) );
        },
        'position'         => 'after:spent',
    ],
] );
```

### Discounts

```php
edd_register_discount_columns( [
    'usage_percent' => [
        'label'            => __( 'Usage %', 'text-domain' ),
        'display_callback' => function( $discount ) {
            if ( $discount->max_uses <= 0 ) return '∞';
            return sprintf( '%d%%', ( $discount->use_count / $discount->max_uses ) * 100 );
        },
        'sortable'         => true,
        'position'         => 'after:uses',
    ],
] );
```

### Commissions (EDD Commissions)

```php
edd_register_commission_columns( [
    'commission_type' => [
        'label'            => __( 'Type', 'text-domain' ),
        'display_callback' => function( $commission ) {
            return ucfirst( esc_html( $commission->type ) );
        },
        'position'         => 'after:rate',
    ],
] );
```

### Licenses (EDD Software Licensing)

```php
edd_register_license_columns( [
    'license_version' => [
        'label'            => __( 'Version', 'text-domain' ),
        'display_callback' => function( $license, $item ) {
            $version = edd_software_licensing()->license_meta_db->get_meta( $license->ID, '_edd_sl_version', true );
            return ! empty( $version ) ? esc_html( $version ) : '—';
        },
        'position'         => 'after:title',
    ],
] );
```

### Subscriptions (EDD Recurring)

```php
edd_register_subscription_columns( [
    'times_billed' => [
        'label'            => __( 'Renewals', 'text-domain' ),
        'display_callback' => function( $subscription ) {
            $times_billed = absint( $subscription->get_total_payments() ) - 1;
            return $times_billed > 0 ? number_format_i18n( $times_billed ) : '0';
        },
        'position'         => 'after:period',
    ],
] );
```

## Column Options

```php
[
    'label'               => '',       // Column header label
    'meta_key'            => '',       // Meta key (optional)
    'sortby'              => '',       // Custom sort field (optional)
    'position'            => '',       // 'after:column' or 'before:column'
    'sortable'            => false,    // Enable sorting (where supported)
    'numeric'             => false,    // Use numeric sorting
    'display_callback'    => null,     // Custom display function
    'permission_callback' => null,     // Permission check function
    'width'               => null,     // Column width (e.g., '100px')
]
```

## Position Examples

```php
'position' => 'after:title',   // Place after the title column
'position' => 'before:date',   // Place before the date column
```

## Sortable Support

| Table | Sortable |
|-------|----------|
| Downloads | ✅ Yes |
| Orders | ✅ Yes |
| Customers | ❌ No |
| Discounts | ✅ Yes |
| Commissions | ❌ No |
| Licenses | ✅ Yes |
| Subscriptions | ❌ No |

## Removing Columns

```php
edd_register_download_columns(
    [
        'new_column' => [ /* ... */ ],
    ],
    [ 'sales', 'earnings' ] // Remove these columns
);
```

## License

GPL-2.0-or-later