<?php
/**
 * Listing package component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;
use HivePress\Controllers;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing package component class.
 *
 * @class Listing_Package
 */
final class Listing_Package {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Upgrade user packages.
		add_action( 'hivepress/v1/update', [ $this, 'upgrade_user_packages' ] );

		// Update user packages.
		add_action( 'hivepress/v1/models/listing/update_status', [ $this, 'update_user_packages' ], 10, 3 );

		// Delete user packages.
		add_action( 'hivepress/v1/models/user/delete', [ $this, 'delete_user_packages' ] );

		if ( class_exists( 'WooCommerce' ) ) {

			// Update order status.
			add_action( 'woocommerce_order_status_changed', [ $this, 'update_order_status' ], 10, 4 );

			// Redirect order page.
			add_action( 'template_redirect', [ $this, 'redirect_order_page' ] );
		}

		if ( ! is_admin() ) {

			// Add menu items.
			add_filter( 'hivepress/v1/menus/listing_submit', [ $this, 'add_menu_items' ] );
		}
	}

	/**
	 * Upgrades user packages.
	 *
	 * @deprecated
	 */
	public function upgrade_user_packages() {
		global $wpdb;

		// Update type.
		$wpdb->query( "UPDATE $wpdb->comments SET comment_type = 'hp_user_listing_package' WHERE comment_type = 'hp_listing_package';" );
	}

	/**
	 * Updates user packages.
	 *
	 * @param int    $listing_id Listing ID.
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 */
	public function update_user_packages( $listing_id, $new_status, $old_status ) {
		if ( 'auto-draft' === $old_status ) {

			// Get listing.
			$listing = get_post( $listing_id );

			// Get user packages.
			$user_packages = wp_list_sort(
				get_comments(
					[
						'type'    => 'hp_user_listing_package',
						'user_id' => $listing->post_author,
					]
				),
				'comment_karma',
				'DESC'
			);

			if ( ! empty( $user_packages ) ) {

				// Get user package.
				$user_package = reset( $user_packages );

				// Set expiration time.
				$expiration_period = absint( get_post_meta( $user_package->comment_post_ID, 'hp_expiration_period', true ) );

				if ( $expiration_period > 0 ) {
					update_post_meta( $listing->ID, 'hp_expiration_time', time() + $expiration_period * DAY_IN_SECONDS );
				}

				// Set featured status.
				if ( get_post_meta( $user_package->comment_post_ID, 'hp_featured', true ) ) {
					update_post_meta( $listing->ID, 'hp_featured', '1' );

					if ( $expiration_period > 0 ) {
						update_post_meta( $listing->ID, 'hp_featuring_time', time() + $expiration_period * DAY_IN_SECONDS );
					}
				}

				// Update user package.
				if ( $user_package->comment_karma > 1 ) {
					wp_update_comment(
						[
							'comment_ID'    => $user_package->comment_ID,
							'comment_karma' => $user_package->comment_karma - 1,
						]
					);
				} else {
					if ( ! $user_package->comment_approved ) {
						wp_delete_comment( $user_package->comment_ID, true );
					} elseif ( $user_package->comment_karma > 0 ) {
						wp_update_comment(
							[
								'comment_ID'    => $user_package->comment_ID,
								'comment_karma' => 0,
							]
						);
					}
				}

				// Delete user packages.
				foreach ( $user_packages as $user_package ) {
					if ( ! $user_package->comment_approved && $user_package->comment_karma <= 0 ) {
						wp_delete_comment( $user_package->comment_ID, true );
					}
				}
			}
		}
	}

	/**
	 * Deletes user packages.
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_user_packages( $user_id ) {

		// Get package IDs.
		$package_ids = get_comments(
			[
				'type'    => 'hp_user_listing_package',
				'user_id' => $user_id,
				'fields'  => 'ids',
			]
		);

		// Delete packages.
		foreach ( $package_ids as $package_id ) {
			wp_delete_comment( $package_id, true );
		}
	}

	/**
	 * Updates order status.
	 *
	 * @param int      $order_id Order ID.
	 * @param string   $old_status Old status.
	 * @param string   $new_status New status.
	 * @param WC_Order $order Order object.
	 */
	public function update_order_status( $order_id, $old_status, $new_status, $order ) {

		// Get package product IDs.
		$package_product_ids = $this->get_package_product_ids();

		if ( ! empty( $package_product_ids ) ) {

			// Get order product IDs.
			$order_product_ids = array_intersect( $package_product_ids, $this->get_order_product_ids( $order ) );

			if ( ! empty( $order_product_ids ) ) {

				// Get package IDs.
				$package_ids = get_posts(
					[
						'post_type'       => 'hp_listing_package',
						'post_status'     => 'publish',
						'post_parent__in' => $order_product_ids,
						'posts_per_page'  => -1,
						'fields'          => 'ids',
					]
				);

				if ( ! empty( $package_ids ) ) {

					// Get user packages.
					$user_packages = get_comments(
						[
							'type'     => 'hp_user_listing_package',
							'user_id'  => $order->get_user_id(),
							'post__in' => $package_ids,
						]
					);

					if ( in_array( $new_status, [ 'processing', 'completed' ], true ) ) {

						// Get user package IDs.
						$user_package_ids = array_map( 'absint', wp_list_pluck( $user_packages, 'comment_post_ID' ) );

						// Add packages.
						foreach ( $package_ids as $package_id ) {
							if ( ! in_array( $package_id, $user_package_ids, true ) ) {
								wp_insert_comment(
									[
										'comment_type'     => 'hp_user_listing_package',
										'comment_approved' => 0,
										'comment_post_ID'  => $package_id,
										'user_id'          => $order->get_user_id(),
										'comment_karma'    => absint( get_post_meta( $package_id, 'hp_submission_limit', true ) ),
										'comment_content'  => get_the_title( $package_id ),
									]
								);
							}
						}
					} elseif ( in_array( $new_status, [ 'failed', 'cancelled', 'refunded' ], true ) ) {

						// Delete packages.
						foreach ( $user_packages as $user_package ) {
							wp_delete_comment( $user_package->comment_ID, true );
						}
					}
				}
			}
		}
	}

	/**
	 * Redirects order page.
	 */
	public function redirect_order_page() {
		if ( is_wc_endpoint_url( 'order-received' ) ) {

			// Get product IDs.
			$product_ids = $this->get_package_product_ids();

			if ( ! empty( $product_ids ) ) {

				// Get order.
				$order = wc_get_order( get_query_var( 'order-received' ) );

				if ( ! empty( $order ) && count( array_intersect( $product_ids, $this->get_order_product_ids( $order ) ) ) > 0 && in_array( $order->get_status(), [ 'processing', 'completed' ], true ) ) {

					// Get listing ID.
					$listing_id = hp\get_post_id(
						[
							'post_type'   => 'hp_listing',
							'post_status' => 'auto-draft',
							'post_parent' => null,
							'author'      => get_current_user_id(),
						]
					);

					// Redirect page.
					if ( 0 !== $listing_id ) {
						wp_safe_redirect( Controllers\Listing_Package::get_url( 'submit_package' ) );

						exit();
					}
				}
			}
		}
	}

	/**
	 * Adds menu items.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function add_menu_items( $menu ) {
		return hp\merge_arrays(
			$menu,
			[
				'items' => [
					'submit_package' => [
						'route' => 'listing_package/submit_package',
						'order' => 35,
					],
				],
			]
		);
	}

	/**
	 * Gets package product IDs.
	 *
	 * @return array
	 */
	protected function get_package_product_ids() {
		return array_filter(
			array_map(
				'absint',
				wp_list_pluck(
					get_posts(
						[
							'post_type'      => 'hp_listing_package',
							'post_status'    => 'publish',
							'posts_per_page' => -1,
						]
					),
					'post_parent'
				)
			)
		);
	}

	/**
	 * Gets order product IDs.
	 *
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	protected function get_order_product_ids( $order ) {
		return array_map(
			function( $item ) {
				return $item->get_product_id();
			},
			$order->get_items()
		);
	}
}
