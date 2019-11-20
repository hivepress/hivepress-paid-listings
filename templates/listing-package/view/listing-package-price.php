<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="hp-listing-package__price"><?php echo ! empty( $product ) ? $product->get_price_html() : esc_html( hivepress()->translator->get_string( 'free' ) ); ?></div>
