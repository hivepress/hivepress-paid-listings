<?php
/**
 * Listing package component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;

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

		if ( is_admin() ) {

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
						'value' => $this->get_product_id( get_the_ID() ),
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
	 * Gets product ID.
	 *
	 * @param int $package_id Package ID.
	 * @return int
	 */
	protected function get_product_id( $package_id ) {
		return hp\get_post_id(
			[
				'post_type'   => 'product',
				'post_status' => 'publish',
				'post__in'    => [ absint( wp_get_post_parent_id( $package_id ) ) ],
			]
		);
	}
}
