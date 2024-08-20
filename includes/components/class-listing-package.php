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

			// Alter menus.
			add_filter( 'hivepress/v1/menus/listing_submit', [ $this, 'alter_listing_submit_menu' ] );
			add_filter( 'hivepress/v1/menus/listing_renew', [ $this, 'alter_listing_renew_menu' ] );
			add_filter( 'hivepress/v1/menus/user_account', [ $this, 'alter_user_account_menu' ] );

			// Alter templates.
			add_filter( 'hivepress/v1/templates/listing_package_view_block/blocks', [ $this, 'alter_listing_package_view_blocks' ], 10, 2 );

			add_filter( 'hivepress/v1/templates/listing_edit_block', [ $this, 'alter_listing_edit_block' ] );
			add_filter( 'hivepress/v1/templates/listing_edit_page', [ $this, 'alter_listing_edit_page' ] );
		}

		parent::__construct( $args );
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
	 * Updates user packages.
	 *
	 * @param int    $listing_id Listing ID.
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 */
	public function update_user_packages( $listing_id, $new_status, $old_status ) {

		// Check listing status.
		if ( ! in_array( $old_status, [ 'auto-draft', 'draft' ], true ) || ! in_array( $new_status, [ 'pending', 'publish' ], true ) ) {
			return;
		}

		// Get listing.
		$listing = Models\Listing::query()->get_by_id( $listing_id );

		// Update listing status.
		if ( 'draft' === $old_status ) {
			if ( 'pending' === $new_status && $listing->get_expired_time() && $listing->get_expired_time() < time() ) {
				update_post_meta( $listing_id, 'hp_moderated', 1 );

				$listing->set_status( 'draft' )->save_status();

				return;
			} elseif ( 'publish' === $new_status && get_post_meta( $listing_id, 'hp_moderated', true ) ) {
				delete_post_meta( $listing_id, 'hp_moderated' );

				$listing->set_status( 'pending' )->save_status();
			}
		}

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
				return ! $user_package->get_categories__id() || array_intersect( (array) $listing->get_categories__id(), $user_package->get_categories__id() );
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
			$listing->save(
				[
					'featured',
					'featured_time',
					'expired_time',
				]
			);
		}

		// Update user package.
		if ( $user_package->get_submit_limit() > 0 ) {
			if ( $user_package->get_categories__id() ) {
				$user_package->set_categories( array_intersect( $user_package->get_categories__id(), Models\Listing_Category::query()->get_ids() ) );
			}

			$user_package->set_submit_limit( $user_package->get_submit_limit() - 1 )->save();
		}

		// Delete user package.
		if ( ( ! $user_package->is_default() || get_option( 'hp_listing_package_allow_free' ) ) && ! $user_package->get_submit_limit() ) {
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
			)->get();

			// Get user packages.
			$user_packages = Models\User_Listing_Package::query()->filter(
				[
					'user'        => $order->get_user_id(),
					'package__in' => $packages->get_ids(),
				]
			);

			if ( in_array( $new_status, [ 'processing', 'completed' ], true ) && 'processing' !== $old_status ) {

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

				// Update submited listings.
				foreach ( $order->get_items() as $item ) {
					if ( in_array( $item->get_product_id(), $package_product_ids, true ) ) {

						// Get listing.
						$listing = Models\Listing::query()->get_by_id( $item->get_meta( 'hp_listing' ) );

						if ( $listing ) {
							if ( $listing->is_drafted() ) {

								// Get status.
								$status = get_option( 'hp_listing_enable_moderation' ) ? 'pending' : 'publish';

								// Add listing.
								$listing->fill(
									[
										'status'  => $status,
										'drafted' => null,
									]
								)->save( [ 'status', 'drafted' ] );
							} elseif ( $listing->get_status() === 'draft' && $listing->get_expired_time() && $listing->get_expired_time() < time() ) {

								// Get date.
								$date = current_time( 'mysql' );

								// Renew listing.
								$listing->fill(
									[
										'status'           => 'publish',
										'created_date'     => $date,
										'created_date_gmt' => get_gmt_from_date( $date ),
										'expired_time'     => null,
									]
								)->save(
									[
										'status',
										'created_date',
										'created_date_gmt',
										'expired_time',
									]
								);
							}
						}

						break;
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
					$listing = Models\Listing::query()->get_by_id( $item->get_meta( 'hp_listing' ) );

					if ( $listing ) {
						if ( ! $listing->is_featured() && in_array( $new_status, [ 'processing', 'completed' ], true ) ) {

							// Set featured status.
							$listing->set_featured( true );

							// Set featured time.
							$featuring_period = absint( get_option( 'hp_listing_featuring_period' ) );

							if ( $featuring_period ) {
								$listing->set_featured_time( time() + $featuring_period * DAY_IN_SECONDS );
							}

							$listing->save( [ 'featured', 'featured_time' ] );
						} elseif ( $listing->is_featured() && in_array( $new_status, [ 'failed', 'cancelled', 'refunded' ], true ) ) {

							// Remove featured status.
							$listing->fill(
								[
									'featured'      => false,
									'featured_time' => null,
								]
							)->save( [ 'featured', 'featured_time' ] );
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

		// Get order.
		$order = wc_get_order( get_query_var( 'order-received' ) );

		if ( empty( $order ) || ! in_array( $order->get_status(), [ 'processing', 'completed' ], true ) ) {
			return;
		}

		// Get order product IDs.
		$order_product_ids = hivepress()->woocommerce->get_order_product_ids( $order );

		// Get package product IDs.
		$package_product_ids = array_intersect( $this->get_package_product_ids(), $order_product_ids );

		if ( $package_product_ids ) {
			foreach ( $order->get_items() as $item ) {
				if ( in_array( $item->get_product_id(), $package_product_ids, true ) ) {

					// Get listing.
					$listing = Models\Listing::query()->get_by_id( $item->get_meta( 'hp_listing' ) );

					if ( $listing ) {

						// Get redirect URL.
						$redirect_url = null;

						if ( $listing->get_status() === 'pending' ) {
							$redirect_url = hivepress()->router->get_url( 'listings_edit_page' );
						} elseif ( $listing->get_status() === 'publish' ) {
							$redirect_url = hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $listing->get_id() ] );
						}

						// Redirect page.
						if ( $redirect_url ) {
							wp_safe_redirect( $redirect_url );

							exit;
						}
					}

					break;
				}
			}
		}

		// Get feature product ID.
		$feature_product_id = absint( get_option( 'hp_product_listing_feature' ) );

		if ( $feature_product_id && in_array( $feature_product_id, $order_product_ids, true ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( $item->get_product_id() === $feature_product_id ) {

					// Get listing.
					$listing = Models\Listing::query()->get_by_id( $item->get_meta( 'hp_listing' ) );

					if ( $listing && $listing->is_featured() ) {

						// Redirect page.
						wp_safe_redirect( hivepress()->router->get_url( 'listing_feature_complete_page', [ 'listing_id' => $listing->get_id() ] ) );

						exit;
					}

					break;
				}
			}
		}
	}

	/**
	 * Alters listing submission menu.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function alter_listing_submit_menu( $menu ) {
		$menu['items']['listing_submit_package'] = [
			'route'  => 'listing_submit_package_page',
			'_order' => 100,
		];

		return $menu;
	}

	/**
	 * Alters listing renewal menu.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function alter_listing_renew_menu( $menu ) {
		$menu['items']['listing_renew_package'] = [
			'route'  => 'listing_renew_package_page',
			'_order' => 100,
		];

		return $menu;
	}

	/**
	 * Alters user account menu.
	 *
	 * @param array $menu Menu arguments.
	 * @return array
	 */
	public function alter_user_account_menu( $menu ) {
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
	 * Alters listing package view blocks.
	 *
	 * @param array  $blocks Block arguments.
	 * @param object $template Template object.
	 * @return array
	 */
	public function alter_listing_package_view_blocks( $blocks, $template ) {

		// Get package.
		$package = $template->get_context( 'listing_package' );

		if ( $package && $package->is_primary() ) {

			// Add class.
			$blocks = hp\merge_trees(
				[ 'blocks' => $blocks ],
				[
					'blocks' => [
						'listing_package_container' => [
							'attributes' => [
								'class' => [ 'hp-listing-package--primary' ],
							],
						],
					],
				]
			)['blocks'];
		}

		return $blocks;
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
