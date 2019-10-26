<?php
/**
 * Listing package select form.
 *
 * @package HivePress\Forms
 */

namespace HivePress\Forms;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing package select form class.
 *
 * @class Listing_Package_Select
 */
class Listing_Package_Select extends Model_Form {

	/**
	 * Form name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected static $model;

	/**
	 * Form action.
	 *
	 * @var string
	 */
	protected static $action;

	/**
	 * Form method.
	 *
	 * @var string
	 */
	protected static $method = 'POST';

	/**
	 * Form fields.
	 *
	 * @var array
	 */
	protected static $fields = [];

	/**
	 * Form button.
	 *
	 * @var object
	 */
	protected static $button;

	/**
	 * Class initializer.
	 *
	 * @param array $args Form arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'model'  => 'listing_package',
				'action' => hp\get_rest_url( '/listing-packages/%id%/select' ),

				'button' => [
					'label' => esc_html__( 'Select Package', 'hivepress-paid-listings' ),
				],
			],
			$args
		);

		parent::init( $args );
	}
}
