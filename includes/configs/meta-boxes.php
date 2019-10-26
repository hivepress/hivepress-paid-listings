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
			'product'       => [
				'label'     => esc_html__( 'WooCommerce Product', 'hivepress-paid-listings' ),
				'type'      => 'select',
				'options'   => 'posts',
				'post_type' => 'product',
				'order'     => 10,
			],

			'listing_limit' => [
				'label'     => esc_html__( 'Listing Limit', 'hivepress-paid-listings' ),
				'type'      => 'number',
				'min_value' => 1,
				'order'     => 20,
			],
		],
	],
];
