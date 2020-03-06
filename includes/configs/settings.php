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
				'title'  => hivepress()->translator->get_string( 'featuring' ),
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
];
