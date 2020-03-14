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

					'created_date'  => [
						'type'   => 'date',
						'format' => 'Y-m-d H:i:s',
						'_alias' => 'comment_date',
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

					'categories'    => [
						'type'        => 'select',
						'options'     => 'terms',
						'option_args' => [ 'taxonomy' => 'hp_listing_category' ],
						'multiple'    => true,
						'_model'      => 'listing_category',
						'_relation'   => 'many_to_many',
						'_external'   => true,
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

	/**
	 * Sets categories.
	 *
	 * @param array $category_ids Category IDs.
	 * @deprecated Since core version 1.3.2
	 */
	public function set_categories( $category_ids ) {
		$this->fields['categories']->set_value( maybe_unserialize( $category_ids ) );
	}
}
