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
	'listing_limit'                             => esc_html__( 'Listing Limit', 'hivepress-paid-listings' ),
	'listing_limit_exceeded'                    => esc_html__( 'Limit Exceeded', 'hivepress-paid-listings' ),
	'set_maximum_number_of_listing_submissions' => esc_html__( 'Set the maximum number of listing submissions.', 'hivepress-paid-listings' ),
];
