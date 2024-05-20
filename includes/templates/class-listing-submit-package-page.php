<?php
/**
 * Listing submit package page template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing submit package page template class.
 *
 * @class Listing_Submit_Package_Page
 */
class Listing_Submit_Package_Page extends Listing_Submit_Page {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Class meta values.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label' => hivepress()->translator->get_string( 'listing_packages' ) . ' (' . hivepress()->translator->get_string( 'submit_listing' ) . ')',
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Template arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'page_content' => [
						'blocks' => [
							'listing_packages'            => [
								'type'   => 'listing_packages',
								'mode'   => 'submit',
								'_order' => 10,
							],

							'listing_details_change_link' => [
								'type'   => 'part',
								'path'   => 'listing/submit/listing-details-change-link',
								'_label' => esc_html__( 'Return Link', 'hivepress-paid-listings' ),
								'_order' => 20,
							],
						],
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
