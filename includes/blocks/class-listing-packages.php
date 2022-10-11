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
	 * Columns number.
	 *
	 * @var int
	 */
	protected $columns = 4;

	/**
	 * Packages number.
	 *
	 * @var int
	 */
	protected $number;

	/**
	 * Template mode.
	 *
	 * @var string
	 */
	protected $mode = 'view';

	/**
	 * Class initializer.
	 *
	 * @param array $meta Block meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'    => hivepress()->translator->get_string( 'listing_packages' ),

				'settings' => [
					'columns' => [
						'label'    => hivepress()->translator->get_string( 'columns_number' ),
						'type'     => 'select',
						'default'  => 4,
						'required' => true,
						'_order'   => 10,

						'options'  => [
							2 => '2',
							3 => '3',
							4 => '4',
						],
					],

					'number'  => [
						'label'     => hivepress()->translator->get_string( 'items_number' ),
						'type'      => 'number',
						'min_value' => 1,
						'default'   => 3,
						'required'  => true,
						'_order'    => 20,
					],
				],
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
			$column_width = hp\get_column_width( $this->columns );

			if ( isset( $this->context['listing_packages'] ) ) {
				$columns = absint( $query->found_posts );

				if ( $columns < 4 ) {
					$column_width = round( 12 / $columns );
				}
			}

			// Render packages.
			$output  = '<div class="hp-listing-packages hp-grid hp-block">';
			$output .= '<div class="hp-row">';

			while ( $query->have_posts() ) {
				$query->the_post();

				// Get package.
				$package = Models\Listing_Package::query()->get_by_id( get_post() );

				if ( $package ) {

					// Get package URL.
					$package_url = null;

					if ( 'submit' === $this->mode ) {
						$package_url = hivepress()->router->get_url( 'listing_submit_package_page', [ 'listing_package_id' => $package->get_id() ] );
					} elseif ( 'renew' === $this->mode ) {
						$package_url = hivepress()->router->get_url(
							'listing_renew_package_page',
							[
								'listing_id'         => hivepress()->request->get_context( 'listing_id' ),
								'listing_package_id' => $package->get_id(),
							]
						);
					} else {
						$package_url = hivepress()->router->get_url( 'listing_package_select_page', [ 'listing_package_id' => $package->get_id() ] );
					}

					// Render package.
					$output .= '<div class="hp-grid__item hp-col-sm-' . esc_attr( $column_width ) . ' hp-col-xs-12">';

					$output .= ( new Template(
						[
							'template' => 'listing_package_view_block',

							'context'  => [
								'listing_package'      => $package,
								'listing_package_url'  => $package_url,
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
