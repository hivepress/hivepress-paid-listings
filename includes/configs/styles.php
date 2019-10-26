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
		'handle'  => 'hp-paid-listings-frontend',
		'src'     => HP_PAID_LISTINGS_URL . '/assets/css/frontend.min.css',
		'version' => HP_PAID_LISTINGS_VERSION,
	],
];
