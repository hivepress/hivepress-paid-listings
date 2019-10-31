<?php
/**
 * Listing package controller.
 *
 * @package HivePress\Controllers
 */

namespace HivePress\Controllers;

use HivePress\Helpers as hp;
use HivePress\Blocks;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing package controller class.
 *
 * @class Listing_Package
 */
class Listing_Package extends Controller {

	/**
	 * Controller name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Controller routes.
	 *
	 * @var array
	 */
	protected static $routes = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Controller arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'routes' => [
					'submit_package' => [
						'title'    => esc_html__( 'Select Package', 'hivepress-paid-listings' ),
						'path'     => '/submit-listing/package/?(?P<listing_package_id>\d+)?',
						'redirect' => 'redirect_listing_submit_package_page',
						'action'   => 'render_listing_submit_package_page',
					],
				],
			],
			$args
		);

		parent::init( $args );
	}

	/**
	 * Redirects listing submit package page.
	 *
	 * @return mixed
	 */
	public function redirect_listing_submit_package_page() {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return add_query_arg( 'redirect', rawurlencode( hp\get_current_url() ), User::get_url( 'login_user' ) );
		}

		// Get package ID.
		$package_id = absint( get_query_var( 'hp_listing_package_id' ) );

		// Check packages.
		if ( hp\get_post_id(
			[
				'post_type'   => 'hp_listing_package',
				'post_status' => 'publish',
				'post__in'    => 0 === $package_id ? [] : [ $package_id ],
			]
		) === 0 ) {
			return true;
		}

		// Get product ID.
		$product_id = absint( wp_get_post_parent_id( $package_id ) );

		if ( class_exists( 'WooCommerce' ) && 0 !== $product_id ) {

			// Add product to cart.
			WC()->cart->empty_cart();
			WC()->cart->add_to_cart( $product_id );

			return wc_get_page_permalink( 'checkout' );
		} elseif ( count(
			get_comments(
				[
					'type'    => 'hp_listing_package',
					'post_id' => $package_id,
					'user_id' => get_current_user_id(),
					'number'  => 1,
					'fields'  => 'ids',
				]
			)
		) === 0 ) {
			wp_insert_comment(
				[
					'comment_type'    => 'hp_listing_package',
					'comment_post_ID' => $package_id,
					'user_id'         => $order->get_user_id(),
					'comment_karma'   => absint( get_post_meta( $package_id, 'hp_listing_limit', true ) ),
					'comment_content' => get_the_title( $package_id ),
				]
			);

			return true;
		}

		return false;
	}

	/**
	 * Renders listing submit package page.
	 *
	 * @return string
	 */
	public function render_listing_submit_package_page() {
		return ( new Blocks\Template(
			[
				'template' => 'listing_submit_package_page',
			]
		) )->render();
	}
}
