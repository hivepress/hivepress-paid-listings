<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing_package->is_primary() ) :
	?>
	<i class="hp-listing-package__primary-badge hp-icon fas fa-check-circle" title="<?php echo esc_attr_x( 'Recommended', 'package', 'hivepress-paid-listings' ); ?>"></i>
	<?php
endif;
