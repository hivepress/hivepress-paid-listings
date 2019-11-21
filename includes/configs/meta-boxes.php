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
		'title'  => hivepress()->translator->get_string( 'settings' ),
		'screen' => 'listing_package',

		'fields' => [
			'product'           => [
				'label'       => hivepress()->translator->get_string( 'ecommerce_product' ),
				'description' => esc_html__( 'Choose a product that must be purchased in order to get this package.', 'hivepress-paid-listings' ),
				'alias'       => 'post_parent',
				'type'        => 'select',
				'options'     => 'posts',
				'post_type'   => 'product',
				'order'       => 10,
			],

			'submission_limit'  => [
				'label'       => hivepress()->translator->get_string( 'listing_limit' ),
				'description' => hivepress()->translator->get_string( 'set_maximum_number_of_listing_submissions' ),
				'type'        => 'number',
				'min_value'   => 1,
				'order'       => 20,
			],

			'expiration_period' => [
				'label'       => hivepress()->translator->get_string( 'listing_expiration' ),
				'description' => hivepress()->translator->get_string( 'set_number_of_days_until_listing_expires' ),
				'type'        => 'number',
				'min_value'   => 1,
				'order'       => 30,
			],

			'featured'          => [
				'label'   => hivepress()->translator->get_string( 'featured_listings' ),
				'caption' => hivepress()->translator->get_string( 'make_listings_featured' ),
				'type'    => 'checkbox',
				'order'   => 40,
			],
		],
	],
];
