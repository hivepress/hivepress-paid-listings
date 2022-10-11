<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_submit_details_page', [ 'redirect' => false ] ) ); ?>" class="hp-form__action hp-form__action--listing-details-change hp-link"><i class="hp-icon fas fa-arrow-left"></i><span><?php esc_html_e( 'Change Details', 'hivepress-paid-listings' ); ?></span></a>
