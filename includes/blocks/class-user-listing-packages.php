<?php
/**
 * User listing packages block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * User listing packages block class.
 *
 * @class User_Listing_Packages
 */
class User_Listing_Packages extends Block {

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Get packages.
		$user_listing_packages = Models\User_Listing_Package::query()->filter(
			[
				'user' => get_current_user_id(),
			]
		)->order( [ 'todo' => 'desc' ] )
		->get()
		->serialize();

		if ( $user_listing_packages ) {
			$output .= '<table class="hp-table">';

			foreach ( $user_listing_packages as $user_listing_package ) {
				if ( hp\is_class_instance( $user_listing_package, '\HivePress\Models\User_Listing_Package' ) ) {

					// Render package.
					$output .= ( new Template(
						[
							'template' => 'user_listing_package_view_block',

							'context'  => [
								'user_listing_package' => $user_listing_package,
							],
						]
					) )->render();
				}
			}

			$output .= '</table>';
		}

		return $output;
	}
}
