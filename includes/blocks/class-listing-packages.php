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
	 * Class initializer.
	 *
	 * @param array $meta Block meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label' => hivepress()->translator->get_string( 'listing_packages' ),
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		global $wp_query;

		$output = '';

		// Query packages.
		$query = $wp_query;

		if ( ! isset( $this->context['listing_packages'] ) ) {
			$query = new \WP_Query(
				Models\Listing_Package::query()->filter(
					[
						'status' => 'publish',
					]
				)->order( [ 'sort_order' => 'asc' ] )
				->get_args()
			);
		}

		if ( $query->have_posts() ) {

			// Get user packages.
			$user_packages = [];

			if ( is_user_logged_in() ) {
				$user_packages = Models\User_Listing_Package::query()->filter(
					[
						'user' => get_current_user_id(),
					]
				)->get()->serialize();

				$user_packages = array_combine(
					array_map(
						function( $user_package ) {
							return $user_package->get_package__id();
						},
						$user_packages
					),
					$user_packages
				);
			}

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
				$package = Models\Listing_Package::query()->get_by_id( get_post() );

				if ( $package ) {
					$output .= '<div class="hp-grid__item hp-col-sm-' . esc_attr( $column_width ) . ' hp-col-xs-12">';

					// Render package.
					$output .= ( new Template(
						[
							'template' => 'listing_package_view_block',

							'context'  => [
								'listing_package'      => $package,
								'user_listing_package' => hp\get_array_value( $user_packages, $package->get_id() ),
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
