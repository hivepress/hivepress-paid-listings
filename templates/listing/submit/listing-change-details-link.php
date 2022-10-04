<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing_status && 'auto-draft' === $listing_status ) :
	?>
	<a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_submit_package_page', [ 'details' => 'change' ] ) ); ?>" class="hp-form__action hp-form__action--listing-details-change hp-link"><i class="hp-icon fas fa-arrow-left"></i><span><?php esc_html_e( 'Change Details', 'hivepress-paid-listings' ); ?></span></a>
	<?php
endif;
