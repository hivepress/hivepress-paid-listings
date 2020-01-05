<?php
/**
 * Listing submit package page template.
 *
 * @template listing_submit_package_page
 * @description Listing submission page (package).
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
	 * Template blocks.
	 *
	 * @var array
	 */
	protected static $blocks = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Template arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'page_content' => [
						'blocks' => [
							'listing_packages' => [
								'type'   => 'listing_packages',
								'_order' => 10,
							],
						],
					],
				],
			],
			$args
		);

		parent::init( $args );
	}
}
