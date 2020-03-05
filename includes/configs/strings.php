<?php
/**
 * Strings configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'featuring'                                     => esc_html__( 'Featuring', 'hivepress-paid-listings' ),
	'feature_listing'                               => esc_html__( 'Feature Listing', 'hivepress-paid-listings' ),
	'listing_packages'                              => esc_html__( 'Listing Packages', 'hivepress-paid-listings' ),
	'listing_limit'                                 => esc_html__( 'Listing Limit', 'hivepress-paid-listings' ),
	'listing_limit_exceeded'                        => esc_html__( 'Limit Exceeded', 'hivepress-paid-listings' ),
	'set_maximum_number_of_listing_submissions'     => esc_html__( 'Set the maximum number of listing submissions.', 'hivepress-paid-listings' ),
	'set_number_of_days_until_listing_not_featured' => esc_html__( 'Set the number of days after which a listing loses featured status.', 'hivepress-paid-listings' ),
	'choose_product_purchased_to_feature_listing'   => esc_html__( 'Choose a product that must be purchased in order to feature a listing.', 'hivepress-paid-listings' ),
];
