<?php
/**
 * Settings configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'listings' => [
		'sections' => [
			'featuring' => [
				'title'  => esc_html__( 'Featuring', 'hivepress-paid-listings' ),
				'_order' => 40,

				'fields' => [
					'product_listing_feature'  => [
						'label'       => hivepress()->translator->get_string( 'ecommerce_product' ),
						'description' => hivepress()->translator->get_string( 'choose_product_purchased_to_feature_listing' ),
						'type'        => 'select',
						'options'     => 'posts',
						'option_args' => [ 'post_type' => 'product' ],
						'_order'      => 10,
					],

					'listing_featuring_period' => [
						'label'       => esc_html__( 'Featuring Period', 'hivepress-paid-listings' ),
						'description' => hivepress()->translator->get_string( 'set_number_of_days_until_listing_not_featured' ),
						'type'        => 'number',
						'min_value'   => 1,
						'_order'      => 20,
					],
				],
			],
		],
	],

	'listing_packages' => [
		'title'    => esc_html__('Packages', 'hivepress-paid-listings'),
		'_order'   => 40,

		'sections' => [
			'features' => [
				'title'  => esc_html__( 'Features', 'hivepress-paid-listings' ),
				'_order' => 5,

				'fields' => [
					'listing_package_allow_free'  => [
						'label'   => esc_html__('Allow free package', 'hivepress-paid-listings'),
						'caption' => esc_html__( 'Check this option to allow re-selecting the free package', 'hivepress-paid-listings' ),
						'type'    => 'checkbox',
						'_order'  => 10,
					],
				],
			],
		],
	],
];
