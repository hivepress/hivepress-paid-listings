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
				'post_type'   => 'hp_listing_package',
				'post_status' => 'publish',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			]
		);

		if ( $query->have_posts() ) {
			$output  = '<div class="hp-grid hp-block">';
			$output .= '<div class="hp-row">';

			while ( $query->have_posts() ) {
				$query->the_post();

				// Get package.
				$listing_package = Models\Listing_Package::get( get_the_ID() );

				if ( ! is_null( $listing_package ) ) {
					$output .= '<div class="hp-grid__item hp-col-sm-4 hp-col-xs-12">';

					// Render package.
					$output .= ( new Template(
						[
							'template' => 'listing_package_view_block',

							'context'  => [
								'listing_package' => $listing_package,
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
