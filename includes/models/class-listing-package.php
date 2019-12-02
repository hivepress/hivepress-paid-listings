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
	 * Model fields.
	 *
	 * @var array
	 */
	protected static $fields = [];

	/**
	 * Model aliases.
	 *
	 * @var array
	 */
	protected static $aliases = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Model arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'fields'  => [
					'name'        => [
						'type'       => 'text',
						'max_length' => 128,
						'required'   => true,
					],

					'description' => [
						'type'       => 'textarea',
						'max_length' => 2048,
					],

					'product_id'  => [
						'type'      => 'number',
						'min_value' => 1,
					],
				],

				'aliases' => [
					'post_title'   => 'name',
					'post_content' => 'description',
					'post_parent'  => 'product_id',
				],
			],
			$args
		);

		parent::init( $args );
	}
}
