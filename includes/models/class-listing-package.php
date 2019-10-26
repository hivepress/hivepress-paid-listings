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
	 * Model name.
	 *
	 * @var string
	 */
	protected static $name;

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
					'title' => [
						'type'       => 'text',
						'max_length' => 128,
						'required'   => true,
					],
				],

				'aliases' => [
					'post_title' => 'title',
				],
			],
			$args
		);

		parent::init( $args );
	}
}
