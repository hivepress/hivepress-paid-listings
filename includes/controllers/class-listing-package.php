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
					'listing_submit_package_page'     => [
						'title'    => esc_html_x( 'Select Package', 'imperative', 'hivepress-paid-listings' ),
						'base'     => 'listing_submit_page',
						'path'     => '/package/?(?P<listing_package_id>\d+)?',
						'redirect' => [ $this, 'redirect_listing_submit_package_page' ],
						'action'   => [ $this, 'render_listing_submit_package_page' ],
					],

					'user_listing_packages_view_page' => [
						'title'    => esc_html__( 'Packages', 'hivepress-paid-listings' ),
						'base'     => 'user_account_page',
						'path'     => '/listing-packages',
						'redirect' => [ $this, 'redirect_user_listing_packages_view_page' ],
						'action'   => [ $this, 'render_user_listing_packages_view_page' ],
					],

					'listing_feature_page'            => [
						'base'     => 'listing_edit_page',
						'path'     => '/feature',
						'redirect' => [ $this, 'redirect_listing_feature_page' ],
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

		// Get listing.
		$listing = hivepress()->request->get_context( 'listing' );

		// Set package query.
		$package_query = Models\Listing_Package::query()->filter(
			[
				'status' => 'publish',
			]
		)->order( [ 'sort_order' => 'asc' ] );

		$package_query_args = array_merge(
			$package_query->get_args(),
			[
				'fields'     => 'ids',
				'categories' => $listing->get_categories__id(),
			]
		);

		// Get package IDs.
		$package_ids = hivepress()->cache->get_cache( $package_query_args, 'models/listing_package' );

		if ( ! is_array( $package_ids ) ) {
			$package_ids = [];

			// Add IDs.
			foreach ( $package_query->get() as $package ) {
				if ( ! $package->get_categories__id() || array_intersect( (array) $listing->get_categories__id(), (array) $package->get_categories__id() ) ) {
					$package_ids[] = $package->get_id();
				}
			}

			// Cache IDs.
			hivepress()->cache->set_cache( $package_query_args, 'models/listing_package', $package_ids );
		}

		// Check packages.
		if ( empty( $package_ids ) ) {
			return true;
		}

		// Set request context.
		hivepress()->request->set_context( 'listing_package_ids', $package_ids );

		// Get user packages.
		$user_packages = Models\User_Listing_Package::query()->filter(
			[
				'user' => get_current_user_id(),
			]
		)->get()->serialize();

		// Filter user packages.
		$user_packages = array_filter(
			$user_packages,
			function( $user_package ) use ( $listing ) {
				return ! $user_package->get_categories__id() || array_intersect( (array) $listing->get_categories__id(), (array) $user_package->get_categories__id() );
			}
		);

		// Check submission limit.
		if ( array_sum(
			array_map(
				function( $user_package ) {
					return $user_package->get_submit_limit();
				},
				$user_packages
			)
		) > 0 ) {
			return true;
		}

		if ( hivepress()->request->get_param( 'listing_package_id' ) ) {

			// Get package.
			$package = Models\Listing_Package::query()->get_by_id( hivepress()->request->get_param( 'listing_package_id' ) );

			if ( $package && $package->get_status() === 'publish' ) {
				if ( hp\is_plugin_active( 'woocommerce' ) && $package->get_product__id() ) {

					// Add product to cart.
					WC()->cart->empty_cart();
					WC()->cart->add_to_cart( $package->get_product__id(), 1, 0, [], [ 'hp_listing' => $listing->get_id() ] );

					return wc_get_page_permalink( 'checkout' );
				}

				if ( empty( $user_packages ) ) {

					// Add user package.
					$user_package = ( new Models\User_Listing_Package() )->fill(
						array_merge(
							$package->serialize(),
							[
								'user'    => get_current_user_id(),
								'package' => $package->get_id(),
								'default' => true,
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

		// Get package IDs.
		$package_ids = hivepress()->request->get_context( 'listing_package_ids' );

		// Query packages.
		query_posts(
			Models\Listing_Package::query()->filter(
				[
					'status' => 'publish',
					'id__in' => $package_ids,
				]
			)->order( 'id__in' )
			->limit( count( $package_ids ) )
			->get_args()
		);

		// Render template.
		return ( new Blocks\Template(
			[
				'template' => 'listing_submit_package_page',

				'context'  => [
					'listing_packages' => [],
				],
			]
		) )->render();
	}

	/**
	 * Redirects user listing packages view page.
	 *
	 * @return mixed
	 */
	public function redirect_user_listing_packages_view_page() {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hivepress()->router->get_url(
				'user_login_page',
				[
					'redirect' => hivepress()->router->get_current_url(),
				]
			);
		}

		// Check listings.
		if ( ! Models\User_Listing_Package::query()->filter(
			[
				'user' => get_current_user_id(),
			]
		)->get_first_id() ) {
			return hivepress()->router->get_url( 'user_account_page' );
		}

		return false;
	}

	/**
	 * Renders user listing packages view page.
	 *
	 * @return string
	 */
	public function render_user_listing_packages_view_page() {
		return ( new Blocks\Template(
			[
				'template' => 'user_listing_packages_view_page',
			]
		) )->render();
	}

	/**
	 * Redirects listing feature page.
	 *
	 * @return mixed
	 */
	public function redirect_listing_feature_page() {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hivepress()->router->get_url(
				'user_login_page',
				[
					'redirect' => hivepress()->router->get_current_url(),
				]
			);
		}

		// Get listing.
		$listing = Models\Listing::query()->get_by_id( hivepress()->request->get_param( 'listing_id' ) );

		if ( empty( $listing ) || get_current_user_id() !== $listing->get_user__id() || $listing->get_status() !== 'publish' || $listing->is_featured() ) {
			return hivepress()->router->get_url( 'listings_edit_page' );
		}

		// Get product ID.
		$product_id = absint( get_option( 'hp_product_listing_feature' ) );

		if ( hp\is_plugin_active( 'woocommerce' ) && $product_id ) {

			// Add product to cart.
			WC()->cart->empty_cart();
			WC()->cart->add_to_cart( $product_id, 1, 0, [], [ 'hp_listing' => $listing->get_id() ] );

			return wc_get_page_permalink( 'checkout' );
		}

		return true;
	}
}
