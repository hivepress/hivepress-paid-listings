<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="hp-listing-package__price"><?php echo $listing_package->get_price() ? esc_html( $listing_package->get_price() ) : esc_html__( 'Free', 'hivepress-paid-listings' ); ?></div>
