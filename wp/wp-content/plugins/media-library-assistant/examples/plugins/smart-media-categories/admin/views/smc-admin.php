<?php
/**
 * Represents the view for the SMC Settings page.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Smart_Media_Categories_Admin
 * @author    David Lingren <dlingren@comcast.net>
 * @license   GPL-2.0+
 * @link      @TODO http://example.com
 * @copyright 2014 David Lingren
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<?php settings_errors(); ?>

	<!-- @TODO: Provide markup for your options page here. -->
	<?php SMC_Settings_Support::render_settings_page( $this->plugin_slug, $active_tab = 'smc_automatic_options' ); ?>

</div>
