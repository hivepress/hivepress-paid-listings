<?php
/**
 * User listing packages view page template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * User listing packages view page template class.
 *
 * @class User_Listing_Packages_View_Page
 */
class User_Listing_Packages_View_Page extends User_Account_Page {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Class meta values.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label' => hivepress()->translator->get_string( 'listing_packages' ) . ' (' . hivepress()->translator->get_string( 'user' ) . ')',
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
							'user_listing_packages' => [
								'type'   => 'user_listing_packages',
								'_label' => hivepress()->translator->get_string( 'listing_packages' ) . ' (' . hivepress()->translator->get_string( 'user' ) . ')',
								'_order' => 10,
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
