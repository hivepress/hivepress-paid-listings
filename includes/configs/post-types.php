<?php
/**
 * Post types configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'listing_package' => [
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'edit.php?post_type=hp_listing',
		'supports'     => [ 'title', 'editor', 'page-attributes' ],

		'labels'       => [
			'name'               => esc_html__( 'Packages', 'hivepress-paid-listings' ),
			'singular_name'      => esc_html__( 'Package', 'hivepress-paid-listings' ),
			'add_new_item'       => esc_html__( 'Add Package', 'hivepress-paid-listings' ),
			'add_new'            => esc_html_x( 'Add New', 'package', 'hivepress-paid-listings' ),
			'edit_item'          => esc_html__( 'Edit Package', 'hivepress-paid-listings' ),
			'new_item'           => esc_html__( 'Add Package', 'hivepress-paid-listings' ),
			'all_items'          => esc_html__( 'Packages', 'hivepress-paid-listings' ),
			'search_items'       => esc_html__( 'Search Packages', 'hivepress-paid-listings' ),
			'not_found'          => esc_html__( 'No packages found.', 'hivepress-paid-listings' ),
			'not_found_in_trash' => esc_html__( 'No packages found.', 'hivepress-paid-listings' ),
		],
	],
];
