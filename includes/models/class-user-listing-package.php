<?php
/**
 * User listing package model.
 *
 * @package HivePress\Models
 */
// todo.
namespace HivePress\Models;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * User listing package model class.
 *
 * @class User_Listing_Package
 */
class User_Listing_Package extends Comment {

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
					'user_id'    => [
						'type'      => 'number',
						'min_value' => 1,
						'required'  => true,
					],

					'package_id' => [
						'type'      => 'number',
						'min_value' => 1,
						'required'  => true,
					],
				],

				'aliases' => [
					'user_id'         => 'user_id',
					'comment_post_ID' => 'package_id',
				],
			],
			$args
		);

		parent::init( $args );
	}
}
