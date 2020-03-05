<?php
/**
 * User listing package view block template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * User listing package view block template class.
 *
 * @class User_Listing_Package_View_Block
 */
class User_Listing_Package_View_Block extends Template {

	/**
	 * Class constructor.
	 *
	 * @param array $args Template arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'user_listing_package_container' => [
						'type'       => 'container',
						'tag'        => 'tr',
						'_order'     => 10,

						'attributes' => [
							'class' => [ 'hp-user-listing-package', 'hp-user-listing-package--view-block' ],
						],

						'blocks'     => [
							// todo block subfolder required?
							'user_listing_package_name' => [
								'type'   => 'part',
								'path'   => 'user-listing-package/view/block/user-listing-package-name',
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
