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

		// Update package.
		add_action( 'save_post', [ $this, 'update_package' ], 99, 2 );

		if ( class_exists( 'WooCommerce' ) ) {

			// Update order status.
			add_action( 'woocommerce_order_status_changed', [ $this, 'update_order_status' ], 10, 4 );

			// Redirect order page.
			add_action( 'template_redirect', [ $this, 'redirect_order_page' ] );
		}

		if ( is_admin() ) {

			// Hide package todos.
			add_filter( 'comments_clauses', [ $this, 'hide_package_todos' ] );

			// Filter meta fields.
			add_filter( 'hivepress/v1/meta_boxes/listing_package_settings', [ $this, 'filter_meta_fields' ] );
		} else {

			// Add menu items.
			add_filter( 'hivepress/v1/menus/listing_submit', [ $this, 'add_menu_items' ] );
		}
	}

	/**
	 * Updates package.
	 *
	 * @param int     $package_id Package ID.
	 * @param WP_Post $package Package object.
	 */
	public function update_package( $package_id, $package ) {
		if ( 'hp_listing_package' === $package->post_type ) {

			// Remove action.
			remove_action( 'save_post', [ $this, 'update_package' ], 99 );

			// Set product ID.
			$product_id = absint( get_post_meta( $package_id, 'hp_product', true ) );

			if ( 0 !== $product_id ) {
				if ( $package->post_parent !== $product_id ) {
					wp_update_post(
						[
							'ID'          => $package_id,
							'post_parent' => $product_id,
						]
					);
				}

				// Delete meta value.
				delete_post_meta( $package_id, 'hp_product' );
			}
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

		// Get product IDs.
		$product_ids = $this->get_package_product_ids();

		if ( ! empty( $product_ids ) && count( array_intersect( $product_ids, $this->get_order_product_ids( $order ) ) ) > 0 ) {
			if ( in_array( $new_status, [ 'processing', 'completed' ], true ) ) {
				// todo add package.
			} elseif ( in_array( $new_status, [ 'failed', 'cancelled', 'refunded' ], true ) ) {
				// todo remove package.
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
	 * Hides package todos.
	 *
	 * @param array $query Query arguments.
	 * @return array
	 */
	public function hide_package_todos( $query ) {
		global $pagenow;

		if ( in_array( $pagenow, [ 'index.php', 'edit-comments.php' ], true ) ) {
			$query['where'] .= ' AND comment_type != "hp_package_todo"';
		}

		return $query;
	}

	/**
	 * Filters meta fields.
	 *
	 * @param array $meta_box Meta box arguments.
	 * @return array
	 */
	public function filter_meta_fields( $meta_box ) {
		return hp\merge_arrays(
			$meta_box,
			[
				'fields' => [
					'product' => [
						'value' => $this->get_package_product_id( get_the_ID() ),
					],
				],
			]
		);
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
	 * Gets package product ID.
	 *
	 * @param int $package_id Package ID.
	 * @return int
	 */
	protected function get_package_product_id( $package_id ) {
		return hp\get_post_id(
			[
				'post_type'   => 'product',
				'post_status' => 'publish',
				'post__in'    => [ absint( wp_get_post_parent_id( $package_id ) ) ],
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
