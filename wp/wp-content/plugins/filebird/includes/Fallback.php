<?php

defined( 'ABSPATH' ) || exit;
add_action(
	'admin_notices',
	function() {
		if ( current_user_can( 'activate_plugins' ) ) {
			?>
				<div class="notice notice-error is-dismissible">
				<p>
					<strong><?php esc_html_e( 'It looks like you have another FileBird version installed, please delete it before activating this new version. All of the settings and data are still preserved.', 'filebird' ); ?>
					<a href="https://ninjateam.gitbook.io/filebird/how-to-update-filebird"><?php esc_html_e( 'Read more details.', 'filebird' ); ?></a>
					</strong>
				</p>
				</div>
			<?php
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
);
