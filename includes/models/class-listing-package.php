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
						'_alias'     => 'post_title',
					],

					'description'   => [
						'type'       => 'textarea',
						'max_length' => 10240,
						'_alias'     => 'post_content',
					],

					'submit_limit'  => [
						'type'      => 'number',
						'min_value' => 1,
						'required'  => true,
						'_external' => true,
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

					'active'        => [
						'type'      => 'number',
						'min_value' => 0,
						'max_value' => 1,
						'default'   => 1,
						'_alias'    => 'comment_approved',
					],

					'product'       => [
						'type'      => 'number',
						'min_value' => 1,
						'_alias'    => 'post_parent',
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
