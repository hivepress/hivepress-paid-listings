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
			'product'          => [
				'label'       => esc_html__( 'WooCommerce Product', 'hivepress-paid-listings' ),
				'description' => esc_html__( 'Choose a WooCommerce product that must be purchased in order to get this package.', 'hivepress-paid-listings' ),
				'alias'       => 'post_parent',
				'type'        => 'select',
				'options'     => 'posts',
				'post_type'   => 'product',
				'order'       => 10,
			],

			'submission_limit' => [
				'label'     => esc_html__( 'Listing Limit', 'hivepress-paid-listings' ),
				'type'      => 'number',
				'min_value' => 1,
				'required'  => true,
				'order'     => 20,
			],
		],
	],
];
