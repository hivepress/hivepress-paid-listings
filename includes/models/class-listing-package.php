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
	 * Class constructor.
	 *
	 * @param array $args Model arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'fields' => [
					'name'          => [
						'type'       => 'text',
						'max_length' => 256,
						'required'   => true,
						'_alias'     => 'post_title',
					],

					'description'   => [
						'type'       => 'textarea',
						'max_length' => 10240,
						'_alias'     => 'post_content',
					],

					'submit_limit'  => [
						'type'      => 'number',
						'min_value' => 1,
						'required'  => true,
						'_external' => true,
					],

					'expire_period' => [
						'type'      => 'number',
						'min_value' => 1,
						'_external' => true,
					],

					'sort_order'    => [
						'type'      => 'number',
						'min_value' => 0,
						'_alias'    => 'menu_order',
					],

					'featured'      => [
						'type'      => 'checkbox',
						'_external' => true,
					],

					'product'       => [
						'type'      => 'number',
						'min_value' => 1,
						'_alias'    => 'post_parent',
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Gets product price.
	 *
	 * @return string
	 */
	final public function display_product__price() {
		$price = esc_html( hivepress()->translator->get_string( 'free' ) );

		if ( hp\is_plugin_active( 'woocommerce' ) && $this->get_product__id() ) {

			// Get product.
			$product = wc_get_product( $this->get_product__id() );

			if ( $product ) {

				// Get price.
				$price = $product->get_price_html();
			}
		}

		return $price;
	}
}
