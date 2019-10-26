<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-listing-package__button button button--primary alt" data-component="link" data-url="<?php echo esc_url( hivepress()->router->get_url( 'listing_package/submit_package', [ 'listing_package_id' => get_the_ID() ] ) ); ?>"><?php echo $listing_package->get_price() ? esc_html__( 'Buy Package', 'hivepress-paid-listings' ) : esc_html__( 'Select Package', 'hivepress-paid-listings' ); ?></button>
