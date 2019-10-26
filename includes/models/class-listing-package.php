<?php
/**
 * Listing package model.
 *
 * @package HivePress\Models
 */

namespace HivePress\Models;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing package model class.
 *
 * @class Listing_Package
 */
class Listing_Package extends Post {

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Model fields.
	 *
	 * @var array
	 */
	protected static $fields = [];

	/**
	 * Model aliases.
	 *
	 * @var array
	 */
	protected static $aliases = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Model arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'fields'  => [
					'name'        => [
						'type'       => 'text',
						'max_length' => 128,
						'required'   => true,
					],

					'price'       => [
						'type'       => 'text',
						'max_length' => 128,
					],

					'description' => [
						'type'       => 'textarea',
						'max_length' => 2048,
					],
				],

				'aliases' => [
					'post_title'   => 'name',
					'post_content' => 'description',
				],
			],
			$args
		);

		parent::init( $args );
	}

	/**
	 * Gets price.
	 *
	 * @return mixed
	 */
	final public function get_price() {
		$price = null;

		if ( class_exists( 'WooCommerce' ) ) {

			// Get product ID.
			$product_id = absint( wp_get_post_parent_id( $this->id ) );

			if ( 0 !== $product_id ) {

				// Get product.
				$product = wc_get_product( $product_id );

				if ( ! empty( $product ) ) {

					// Set price.
					$price = wp_strip_all_tags( wc_price( $product->get_price() ) );
				}
			}
		}

		return $price;
	}
}
