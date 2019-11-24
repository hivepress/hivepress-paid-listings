<?php
/**
 * Meta boxes configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'listing_package_settings' => [
		'title'  => esc_html__( 'Settings', 'hivepress-paid-listings' ),
		'screen' => 'listing_package',

		'fields' => [
			'product'           => [
				'label'       => esc_html__( 'WooCommerce Product', 'hivepress-paid-listings' ),
				'description' => esc_html__( 'Choose a WooCommerce product that must be purchased in order to get this package.', 'hivepress-paid-listings' ),
				'type'        => 'select',
				'options'     => 'posts',
				'post_type'   => 'product',
				'order'       => 10,
			],

			'submission_limit'  => [
				'label'     => esc_html__( 'Listing Limit', 'hivepress-paid-listings' ),
				'type'      => 'number',
				'min_value' => 1,
				'required'  => true,
				'order'     => 20,
			],

			'expiration_period' => [
				'label'       => esc_html__( 'Listing Expiration', 'hivepress-paid-listings' ),
				'description' => esc_html__( 'Set the number of days after which a listing expires.', 'hivepress-paid-listings' ),
				'type'        => 'number',
				'min_value'   => 1,
				'order'       => 30,
			],

			'featured'          => [
				'label'   => esc_html__( 'Featured Listings', 'hivepress-paid-listings' ),
				'caption' => esc_html__( 'Make listings featured', 'hivepress-paid-listings' ),
				'type'    => 'checkbox',
				'order'   => 40,
			],
		],
	],
];
