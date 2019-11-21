<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( get_comments(
	[
		'type'    => 'hp_user_listing_package',
		'post_id' => get_the_ID(),
		'user_id' => get_current_user_id(),
		'number'  => 1,
		'fields'  => 'ids',
	]
) ) :
	?>
	<button type="button" class="hp-listing-package__button button" disabled><?php echo esc_html( hivepress()->translator->get_string( 'listing_limit_exceeded' ) ); ?></button>
<?php else : ?>
	<button type="button" class="hp-listing-package__button button button--primary alt" data-component="link" data-url="<?php echo esc_url( hivepress()->router->get_url( 'listing_package/submit_package', [ 'listing_package_id' => get_the_ID() ] ) ); ?>"><?php echo ! empty( $product ) ? esc_html__( 'Buy Package', 'hivepress-paid-listings' ) : esc_html__( 'Select Package', 'hivepress-paid-listings' ); ?></button>
	<?php
endif;
