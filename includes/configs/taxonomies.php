<?php
/**
 * Taxonomies configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'listing_category' => [
		'post_type' => [ 'listing_package' ],
	],
];
