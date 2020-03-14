<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing->get_status() === 'publish' ) :
	if ( ! $listing->is_featured() ) :
		?>
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_feature_page', [ 'listing_id' => $listing->get_id() ] ) ); ?>" title="<?php esc_attr_e( 'Feature', 'hivepress-paid-listings' ); ?>" class="hp-listing__action hp-listing__action--feature hp-link"><i class="hp-icon fas fa-star"></i></a>
	<?php else : ?>
		<span title="<?php echo esc_attr_x( 'Featured', 'listing', 'hivepress-paid-listings' ); ?>" class="hp-listing__action hp-listing__action--feature hp-link" data-state="active"><i class="hp-icon fas fa-star"></i></span>
		<?php
	endif;
endif;
