<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( isset( $user_listing_package ) && ! $user_listing_package->get_submit_limit() ) :
	?>
	<button type="button" class="hp-listing-package__select-button hp-listing-package__button button" disabled>
		<span><?php echo esc_html( hivepress()->translator->get_string( 'listing_limit_exceeded' ) ); ?></span>
	</button>
<?php else : ?>
	<button type="button" class="hp-listing-package__select-button hp-listing-package__button button button--primary alt" data-component="link" data-url="<?php echo esc_url( $listing_package_url ); ?>">
		<span>
			<?php
			if ( $listing_package->get_product__id() ) :
				echo esc_html( hivepress()->translator->get_string( 'buy_listing_package' ) );
			else :
				echo esc_html( hivepress()->translator->get_string( 'select_listing_package' ) );
			endif;
			?>
		</span>
	</button>
	<?php
endif;
