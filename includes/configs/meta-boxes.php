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
			'product'       => [
				'label'       => hivepress()->translator->get_string( 'ecommerce_product' ),
				'description' => esc_html__( 'Choose a product that must be purchased in order to get this package.', 'hivepress-paid-listings' ),
				'type'        => 'select',
				'options'     => 'posts',
				'option_args' => [ 'post_type' => 'product' ],
				'_alias'      => 'post_parent',
				'_order'      => 10,
			],

			'submit_limit'  => [
				'label'       => hivepress()->translator->get_string( 'listing_limit' ),
				'description' => hivepress()->translator->get_string( 'set_maximum_number_of_listing_submissions' ),
				'type'        => 'number',
				'min_value'   => 1,
				'required'    => true,
				'_order'      => 20,
			],

			'expire_period' => [
				'label'       => hivepress()->translator->get_string( 'listing_expiration' ),
				'description' => hivepress()->translator->get_string( 'set_number_of_days_until_listing_expires' ),
				'type'        => 'number',
				'min_value'   => 1,
				'_order'      => 30,
			],

			'featured'      => [
				'label'   => hivepress()->translator->get_string( 'featuring_of_listings' ),
				'caption' => hivepress()->translator->get_string( 'make_listings_featured' ),
				'type'    => 'checkbox',
				'_order'  => 40,
			],

			'primary'       => [
				'label'   => esc_html_x( 'Recommended', 'package', 'hivepress-paid-listings' ),
				'caption' => esc_html__( 'Recommend this package', 'hivepress-paid-listings' ),
				'type'    => 'checkbox',
				'_order'  => 50,
			],
		],
	],
];
