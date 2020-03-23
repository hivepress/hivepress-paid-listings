<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( isset( $user_listing_package ) ) :
	?>
	<button type="button" class="hp-listing-package__select-button hp-listing-package__button button" disabled><?php echo esc_html( hivepress()->translator->get_string( 'listing_limit_exceeded' ) ); ?></button>
<?php else : ?>
	<button type="button" class="hp-listing-package__select-button hp-listing-package__button button button--primary alt" data-component="link" data-url="<?php echo esc_url( $listing_package_url ); ?>">
		<?php
		if ( $listing_package->get_product__id() ) :
			esc_html_e( 'Buy Package', 'hivepress-paid-listings' );
		else :
			esc_html_e( 'Select Package', 'hivepress-paid-listings' );
		endif;
		?>
	</button>
	<?php
endif;
