<?php
/**
 * Plugin Name: HivePress Paid Listings
 * Description: Charge users for adding, featuring and renewing listings.
 * Version: 1.1.9
 * Author: HivePress
 * Author URI: https://hivepress.io/
 * Text Domain: hivepress-paid-listings
 * Domain Path: /languages/
 *
 * @package HivePress
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Register extension directory.
add_filter(
	'hivepress/v1/extensions',
	function( $extensions ) {
		$extensions[] = __DIR__;

		return $extensions;
	}
);
