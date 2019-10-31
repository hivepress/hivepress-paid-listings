<?php
/**
 * Listing packages block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing packages block class.
 *
 * @class Listing_Packages
 */
class Listing_Packages extends Block {

	/**
	 * Block type.
	 *
	 * @var string
	 */
	protected static $type;

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Query packages.
		$query = new \WP_Query(
			[
				'post_type'      => 'hp_listing_package',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			]
		);

		if ( $query->have_posts() ) {

			// Get column width.
			$columns      = absint( $query->found_posts );
			$column_width = 3;

			if ( $columns < 4 ) {
				$column_width = round( 12 / $columns );
			}

			// Render packages.
			$output  = '<div class="hp-grid hp-block">';
			$output .= '<div class="hp-row">';

			while ( $query->have_posts() ) {
				$query->the_post();

				// Get package.
				$package = Models\Listing_Package::get( get_the_ID() );

				if ( ! is_null( $package ) ) {
					$output .= '<div class="hp-grid__item hp-col-sm-' . esc_attr( $column_width ) . ' hp-col-xs-12">';

					// Get product.
					$product = null;

					if ( class_exists( 'WooCommerce' ) && $package->get_product_id() !== 0 ) {
						$product = wc_get_product( $package->get_product_id() );
					}

					// Render package.
					$output .= ( new Template(
						[
							'template' => 'listing_package_view_block',

							'context'  => [
								'listing_package' => $package,
								'product'         => $product,
							],
						]
					) )->render();

					$output .= '</div>';
				}
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		// Reset query.
		wp_reset_postdata();

		return $output;
	}
}
