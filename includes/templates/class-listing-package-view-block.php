<?php
/**
 * Listing package view block template.
 *
 * @template listing_package_view_block
 * @description Listing package block in view context.
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing package view block template class.
 *
 * @class Listing_Package_View_Block
 */
class Listing_Package_View_Block extends Template {

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
					'listing_package_container' => [
						'type'       => 'container',
						'order'      => 10,

						'attributes' => [
							'class' => [ 'hp-listing-package', 'hp-listing-package--view-block' ],
						],

						'blocks'     => [
							'listing_package_header'  => [
								'type'       => 'container',
								'tag'        => 'header',
								'order'      => 10,

								'attributes' => [
									'class' => [ 'hp-listing-package__header' ],
								],

								'blocks'     => [
									'listing_package_name' => [
										'type'     => 'element',
										'filepath' => 'listing-package/view/block/listing-package-name',
										'order'    => 10,
									],

									'listing_package_price' => [
										'type'     => 'element',
										'filepath' => 'listing-package/view/listing-package-price',
										'order'    => 20,
									],
								],
							],

							'listing_package_content' => [
								'type'       => 'container',
								'order'      => 20,

								'attributes' => [
									'class' => [ 'hp-listing-package__content' ],
								],

								'blocks'     => [
									'listing_package_description' => [
										'type'     => 'element',
										'filepath' => 'listing-package/view/listing-package-description',
										'order'    => 10,
									],
								],
							],

							'listing_package_footer'  => [
								'type'       => 'container',
								'tag'        => 'footer',
								'order'      => 30,

								'attributes' => [
									'class' => [ 'hp-listing-package__footer' ],
								],

								'blocks'     => [
									'listing_package_select_button' => [
										'type'     => 'element',
										'filepath' => 'listing-package/view/listing-package-select-button',
										'order'    => 10,
									],
								],
							],
						],
					],
				],
			],
			$args,
			'blocks'
		);

		parent::init( $args );
	}
}
