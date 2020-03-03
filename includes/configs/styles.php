<?php
/**
 * Styles configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'paid_listings_frontend' => [
		'handle'  => 'hivepress-paid-listings-frontend',
		'src'     => hivepress()->get_url( 'paid_listings' ) . '/assets/css/frontend.min.css',
		'version' => hivepress()->get_version( 'paid_listings' ),
		'scope'   => [ 'frontend', 'editor' ],
	],
];
