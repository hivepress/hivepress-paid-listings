<?php
/**
 * User listing package model.
 *
 * @package HivePress\Models
 */

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
	 * Class initializer.
	 *
	 * @param array $meta Model meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'alias' => 'hp_listing_package',
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Model arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'fields' => [
					'name'          => [
						'type'       => 'text',
						'max_length' => 256,
						'required'   => true,
						'_alias'     => 'comment_content',
					],

					'submit_limit'  => [
						'type'      => 'number',
						'min_value' => 0,
						'required'  => true,
						'_alias'    => 'comment_karma',
					],

					'expire_period' => [
						'type'      => 'number',
						'min_value' => 1,
						'_external' => true,
					],

					'featured'      => [
						'type'      => 'checkbox',
						'_external' => true,
					],

					'default'       => [
						'type'      => 'checkbox',
						'_external' => true,
					],

					'user'          => [
						'type'      => 'number',
						'min_value' => 1,
						'required'  => true,
						'_alias'    => 'user_id',
						'_model'    => 'user',
					],

					'package'       => [
						'type'      => 'number',
						'min_value' => 1,
						'required'  => true,
						'_alias'    => 'comment_post_ID',
						'_model'    => 'listing_package',
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
