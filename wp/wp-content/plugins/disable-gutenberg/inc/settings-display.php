<?php // Display Settings

if (!defined('ABSPATH')) exit;

function disable_gutenberg_menu_pages() {
	
	// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function )
	add_options_page('Disable Gutenberg', 'Disable Gutenberg', 'manage_options', 'disable-gutenberg', 'disable_gutenberg_display_settings');
	
}

function disable_gutenberg_display_settings() { ?>
	
	<div class="wrap">
		<h1>
			<span class="fa fa-pad fa-gear"></span> <?php echo DISABLE_GUTENBERG_NAME; ?> 
			<span class="disable-gutenberg-version"><?php echo DISABLE_GUTENBERG_VERSION; ?></span>
		</h1>
		<form method="post" action="options.php">
			
			<?php 
				settings_fields('disable_gutenberg_options');
				do_settings_sections('disable_gutenberg_options');
				submit_button(); 
			?>
			
		</form>
	</div>
	
<?php }
