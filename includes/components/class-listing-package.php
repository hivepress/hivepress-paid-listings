<?php
/**
 * Listing package component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Listing package component class.
 *
 * @class Listing_Package
 */
final class Listing_Package extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Update user packages.
		add_action( 'hivepress/v1/models/listing/update_status', [ $this, 'update_user_packages' ], 10, 3 );

		// Delete user packages.
		add_action( 'hivepress/v1/models/user/delete', [ $this, 'delete_user_packages' ] );

		if ( hp\is_plugin_active( 'woocommerce' ) ) {

			// Set order item meta.
			add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'set_order_item_meta' ], 10, 3 );

			// Hide order item meta.
			add_filter( 'woocommerce_hidden_order_itemmeta', [ $this, 'hide_order_item_meta' ] );

			// Update order status.
			add_action( 'woocommerce_order_status_changed', [ $this, 'update_order_status' ], 10, 4 );

			// Redirect order page.
			add_action( 'template_redirect', [ $this, 'redirect_order_page' ] );
		}

		if ( ! is_admin() ) {

			// Alter submission menu.
			add_filter( 'hivepress/v1/menus/listing_submit', [ $this, 'alter_submission_menu' ] );

			// Alter account menu.
			add_filter( 'hivepress/v1/menus/user_account', [ $this, 'alter_account_menu' ] );

			// Alter templates.
			add_filter( 'hivepress/v1/templates/listing_edit_block', [ $this, 'alter_listing_edit_block' ] );
			add_filter( 'hivepress/v1/templates/listing_edit_page', [ $this, 'alter_listing_edit_page' ] );
		}

		parent::__construct( $args );
	}

	/**
	 * Updates user packages.
	 *
	 * @param int    $listing_id Listing ID.
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 */
	public function update_user_packages( $listing_id, $new_status, $old_status ) {

		// Check listing status.
		if ( 'auto-draft' !== $old_status ) {
			return;
		}

		// Get listing.
		$listing = Models\Listing::query()->get_by_id( $listing_id );

		// Get user packages.
		$user_packages = Models\User_Listing_Package::query()->filter(
			[
				'user' => $listing->get_user__id(),
			]
		)->order( [ 'submit_limit' => 'desc' ] )
		->get()->serialize();

		// Filter user packages.
		$user_packages = array_filter(
			$user_packages,
			function( $user_package ) use ( $listing ) {
				return ! $user_package->get_categories__id() || array_intersect( (array) $listing->get_categories__id(), (array) $user_package->get_categories__id() );
			}
		);

		if ( empty( $user_packages ) ) {
			return;
		}

		// Get the first package.
		$user_package = reset( $user_packages );

		if ( $user_package->get_expire_period() || $user_package->is_featured() ) {

			// Set expiration time.
			if ( $user_package->get_expire_period() ) {
				$listing->set_expired_time( time() + $user_package->get_expire_period() * DAY_IN_SECONDS );
			}

			// Set featured status.
			if ( $user_package->is_featured() ) {
				$listing->set_featured( true );

				if ( $user_package->get_expire_period() ) {
					$listing->set_featured_time( time() + $user_package->get_expire_period() * DAY_IN_SECONDS );
				}
			}

			// Update listing.
			$listing->save();
		}

		// Update user package.
		if ( $user_package->get_submit_limit() > 0 ) {
			$user_package->set_submit_limit( $user_package->get_submit_limit() - 1 )->save();
		}

		// Delete user package.
		if ( ! $user_package->is_default() && ! $user_package->get_submit_limit() ) {
			$user_package->delete();
		}
	}

	/**
	 * Deletes user packages.
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_user_packages( $user_id ) {
		Models\User_Listing_Package::query()->filter(
			[
				'user' => $user_id,
			]
		)->delete();
	}

	/**
	 * Gets package product IDs.
	 *
	 * @return array
	 */
	protected function get_package_product_ids() {
		return array_filter(
			array_map(
				function( $package ) {
					return $package->get_product__id();
				},
				Models\Listing_Package::query()->filter(
					[
						'status' => 'publish',
					]
				)->get()->serialize()
			)
		);
	}

	/**
	 * Sets order item meta.
	 *
	 * @param WC_Order_Item_Product $item Order item.
	 * @param string                $cart_item_key Cart item key.
	 * @param array                 $meta Meta values.
	 * @deprecated Since core version 1.3.2
	 */
	public function set_order_item_meta( $item, $cart_item_key, $meta ) {
		if ( isset( $meta['_hp_listing'] ) ) {
			$item->update_meta_data( '_hp_listing', $meta['_hp_listing'] );
		}
	}

	/**
	 * Hides order item meta.
	 *
	 * @param array $meta Meta values.
	 * @return array
	 * @deprecated Since core version 1.3.2
	 */
	public function hide_order_item_meta( $meta ) {
		return array_merge( $meta, [ '_hp_listing' ] );
	}

	/**
	 * Updates order status.
	 *
	 * @param int      $order_id Order ID.
	 * @param string   $old_status Old status.
	 * @param string   $new_status New status.
	 * @param WC_Order $order Order object.
	 */
	public function update_order_status( $order_id, $old_status, $new_status, $order ) {

		// Check user.
		if ( ! $order->get_user_id() ) {
			return;
		}

		// Get order product IDs.
		$order_product_ids = hivepress()->woocommerce->get_order_product_ids( $order );

		// Get package product IDs.
		$package_product_ids = array_intersect( $this->get_package_product_ids(), $order_product_ids );

		if ( $package_product_ids ) {

			// Get packages.
			$packages = Models\Listing_Package::query()->filter(
				[
					'status'      => 'publish',
					'product__in' => $package_product_ids,
				]
			)->get()->serialize();

			if ( empty( $packages ) ) {
				return;
			}

			// Get user packages.
			$user_packages = Models\User_Listing_Package::query()->filter(
				[
					'user'        => $order->get_user_id(),
					'package__in' => array_map(
						function( $package ) {
							return $package->get_id();
						},
						$packages
					),
				]
			);

			if ( in_array( $new_status, [ 'processing', 'completed' ], true ) ) {

				// Get package IDs.
				$package_ids = array_map(
					function( $user_package ) {
						return $user_package->get_package__id();
					},
					$user_packages->get()->serialize()
				);

				// Add user packages.
				foreach ( $packages as $package ) {
					if ( ! in_array( $package->get_id(), $package_ids, true ) ) {
						( new Models\User_Listing_Package() )->fill(
							array_merge(
								$package->serialize(),
								[
									'user'    => $order->get_user_id(),
									'package' => $package->get_id(),
								]
							)
						)->save();
					}
				}
			} elseif ( in_array( $new_status, [ 'failed', 'cancelled', 'refunded' ], true ) ) {

				// Delete user packages.
				$user_packages->delete();
			}
		}

		// Get feature product ID.
		$feature_product_id = absint( get_option( 'hp_product_listing_feature' ) );

		if ( $feature_product_id && in_array( $feature_product_id, $order_product_ids, true ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( $item->get_product_id() === $feature_product_id ) {

					// Get listing.
					$listing = Models\Listing::query()->get_by_id( $item->get_meta( '_hp_listing', true, 'edit' ) );

					if ( $listing ) {
						if ( ! $listing->is_featured() && in_array( $new_status, [ 'processing', 'completed' ], true ) ) {

							// Set featured status.
							$listing->set_featured( true );

							// Set featured time.
							$featuring_period = absint( get_option( 'hp_listing_featuring_period' ) );

							if ( $featuring_period ) {
								$listing->set_featured_time( time() + $featuring_period * DAY_IN_SECONDS );
							}

							$listing->save();
						} elseif ( $listing->is_featured() && in_array( $new_status, [ 'failed', 'cancelled', 'refunded' ], true ) ) {

							// Remove featured status.
							$listing->fill(
								[
									'featured'      => false,
									'featured_time' => null,
								]
							)->save();
						}
					}

					break;
				}
			}
		}
	}

	/**
	 * Redirects order page.
	 */
	public function redirect_order_page() {

		// Check authentication.
		if ( ! is_user_logged_in() || ! is_wc_endpoint_url( 'order-received' ) ) {
			return;
		}

		// Get product IDs.
		$product_ids = $this->get_package_product_ids();

		if ( empty( $product_ids ) ) {
			return;
		}

		// Get order.
		$order = wc_get_order( get_query_var( 'order-received' ) );

		if ( empty( $order ) || ! in_array( $order->get_status(), [ 'processing', 'completed' ], true ) || ! array_intersect( $product_ids, hivepress()->woocommerce->get_order_product_ids( $order ) ) ) {
			return;
		}

		// Get listing ID.
		$listing_id = Models\Listing::query()->filter(
			[
				'status'  => 'auto-draft',
				'drafted' => true,
				'user'    => get_current_user_id(),
			]
		)->get_first_id();

		if ( empty( $listing_id ) ) {
			return;
		}

		// Redirect page.
		wp_safe_redirect( hivepress()->router->get_url( 'listing_submit_package_page' ) );

		exit;
	}

	/**
	 * Alters submission menu.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function alter_submission_menu( $menu ) {
		$menu['items']['listing_submit_package'] = [
			'route'  => 'listing_submit_package_page',
			'_order' => 30,
		];

		return $menu;
	}

	/**
	 * Alters account menu.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function alter_account_menu( $menu ) {
		if ( Models\User_Listing_Package::query()->filter(
			[
				'user' => get_current_user_id(),
			]
		)->get_first_id() ) {
			$menu['items']['user_listing_packages_view'] = [
				'route'  => 'user_listing_packages_view_page',
				'_order' => 15,
			];
		}

		return $menu;
	}

	/**
	 * Alters listing edit block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_edit_block( $template ) {
		if ( hp\is_plugin_active( 'woocommerce' ) && get_option( 'hp_product_listing_feature' ) ) {
			$template = hp\merge_trees(
				$template,
				[
					'blocks' => [
						'listing_actions_primary' => [
							'blocks' => [
								'listing_feature_link' => [
									'type'   => 'part',
									'path'   => 'listing/edit/block/listing-feature-link',
									'_order' => 5,
								],
							],
						],
					],
				]
			);
		}

		return $template;
	}

	/**
	 * Alters listing edit page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_edit_page( $template ) {
		if ( hp\is_plugin_active( 'woocommerce' ) && get_option( 'hp_product_listing_feature' ) ) {
			$template = hp\merge_trees(
				$template,
				[
					'blocks' => [
						'listing_actions_secondary' => [
							'blocks' => [
								'listing_feature_link' => [
									'type'   => 'part',
									'path'   => 'listing/edit/page/listing-feature-link',
									'_order' => 10,
								],
							],
						],
					],
				]
			);
		}

		return $template;
	}
}
