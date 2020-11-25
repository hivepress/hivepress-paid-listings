<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $listing_package->get_description() ) :
	?>
	<div class="hp-listing-package__description"><?php echo $listing_package->display_description(); ?></div>
	<?php
endif;
