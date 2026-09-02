<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="notice notice-info is-dismissible" id="filebird-empty-folder-notice">
    <p>
        <?php esc_html_e( 'Create your first folder for media library now.', 'filebird' ); ?>
        <a href="<?php echo esc_url( admin_url( '/upload.php' ) ); ?>">
            <strong><?php esc_html_e( 'Get Started', 'filebird' ); ?></strong>
        </a>
    </p>
</div>