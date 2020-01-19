<?php
/**
 * Listing package controller.
 *
 * @package HivePress\Controllers
 */

namespace HivePress\Controllers;

use HivePress\Helpers as hp;
use HivePress\Models;
use HivePress\Blocks;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing package controller class.
 *
 * @class Listing_Package
 */
final class Listing_Package extends Controller {

	/**
	 * Class constructor.
	 *
	 * @param array $args Controller arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'routes' => [
					'listing_submit_package_page' => [
						'title'    => esc_html_x( 'Select Package', 'imperative', 'hivepress-paid-listings' ),
						'base'     => 'listing_submit_page',
						'path'     => '/package/?(?P<listing_package_id>\d+)?',
						'redirect' => [ $this, 'redirect_listing_submit_package_page' ],
						'action'   => [ $this, 'render_listing_submit_package_page' ],
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Redirects listing submit package page.
	 *
	 * @return mixed
	 */
	public function redirect_listing_submit_package_page() {

		// Check packages.
		if ( ! Models\Listing_Package::query()->filter(
			[
				'status' => 'publish',
			]
		)->get_first_id() ) {
			return;
		}

		// Get user packages.
		$user_packages = Models\User_Listing_Package::query(
			[
				'user' => get_current_user_id(),
			]
		)->get();

		// Check submission limit.
		if ( array_sum(
			array_map(
				function( $user_package ) {
					return $user_package->get_submit_limit();
				},
				$user_packages
			)
		) ) {
			true;
		}

		if ( hivepress()->request->get_param( 'listing_package_id' ) ) {

			// Get package.
			$package = Models\Listing_Package::query()->get_by_id( hivepress()->request->get_param( 'listing_package_id' ) );

			if ( $package && $package->get_status() === 'publish' ) {
				if ( hp\is_plugin_active( 'woocommerce' ) && $package->get_product__id() ) {

					// Add product to cart.
					WC()->cart->empty_cart();
					WC()->cart->add_to_cart( $package->get_product__id() );

					return wc_get_page_permalink( 'checkout' );
				}

				if ( empty( $user_packages ) ) {

					// Add user package.
					$user_package = ( new Models\User_Listing_Package() )->fill(
						array_merge(
							$package->serialize(),
							[
								'default' => 1,
								'user'    => get_current_user_id(),
								'package' => $package->get_id(),
							]
						)
					);

					if ( $user_package->save() ) {
						return true;
					}
				}
			}

			return hivepress()->router->get_url( 'listing_submit_package_page' );
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
