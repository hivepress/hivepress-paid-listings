<?php
/**
 * Listing feature page template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing feature page template class.
 *
 * @class Listing_Feature_Page
 */
abstract class Listing_Feature_Page extends Page_Wide {

	/**
	 * Class constructor.
	 *
	 * @param array $args Template arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'page_content' => [],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
