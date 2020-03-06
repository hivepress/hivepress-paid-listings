<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing->get_status() === 'publish' && ! $listing->is_featured() ) :
	?>
	<a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_feature_page', [ 'listing_id' => $listing->get_id() ] ) ); ?>" class="hp-form__action hp-form__action--listing-feature hp-link"><i class="hp-icon fas fa-star"></i><span><?php echo esc_html( hivepress()->translator->get_string( 'feature_listing' ) ); ?></span></a>
	<?php
endif;
