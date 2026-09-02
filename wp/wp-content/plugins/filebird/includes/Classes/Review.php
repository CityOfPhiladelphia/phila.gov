<?php
namespace FileBird\Classes;

defined( 'ABSPATH' ) || exit;

class Review {
	public function __construct() {
		add_action( 'wp_ajax_fbv_save_review', array( $this, 'fbv_save_review' ) );

		$option = get_option( 'fbv_review', false );
		if ( time() >= intval( $option ) && '0' !== $option ) {
			add_action( 'admin_notices', array( $this, 'give_review' ) );
		}
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'fbv-review', NJFB_PLUGIN_URL . 'assets/js/review.js', array( 'jquery' ), NJFB_VERSION, false );
	}

	public function checkNonce( $nonce ) {
		if ( ! wp_verify_nonce( $nonce, 'fbv_nonce' ) ) {
			wp_send_json_error( array( 'status' => 'Wrong nonce validate!' ) );
			exit();
		}
	}

	public function hasField( $field, $request ) {
		return isset( $request[ $field ] ) ? sanitize_text_field( $request[ $field ] ) : null;
	}

	public function fbv_save_review() {
		if ( count( $_REQUEST ) ) {
			$nonce = $this->hasField( 'nonce', $_REQUEST );
			$field = $this->hasField( 'field', $_REQUEST );

			$this->checkNonce( $nonce );

			if ( 'later' == $field ) {
				update_option( 'fbv_review', time() + 5 * 60 * 60 * 24 ); //After 3 days show
			} elseif ( 'alreadyDid' == $field || 'rateNow' == $field ) {
				update_option( 'fbv_review', 0 );
			}
			wp_send_json_success();
		}
		wp_send_json_error( array( 'message' => 'Update fail!' ) );
	}

	public static function update_time_display() {
		$option = get_option( 'fbv_review', false );
		if ( '0' !== $option ) {
			update_option( 'fbv_review', time() + 3 * 60 * 60 * 24 ); //After 3 days show
		}
	}

	public function give_review() {
		if ( function_exists( 'get_current_screen' ) ) {
			if ( get_current_screen()->id == 'upload' || get_current_screen()->id == 'plugins' ) {
				$this->enqueue_scripts();
				?>
<div class="notice notice-success is-dismissible" id="njt-FileBird-review">
	<h3><?php esc_html_e( 'Give FileBird a review', 'filebird' ); ?></h3>
	<p>
				<?php esc_html_e( 'Thank you for choosing FileBird. We hope you love it. Could you take a couple of seconds posting a nice review to share your happy experience?', 'filebird' ); ?>
	</p>
	<p>
				<?php esc_html_e( 'We will be forever grateful. Thank you in advance ;)', 'filebird' ); ?>
	</p>
	<p>
		<a href="javascript:;" data="rateNow"
			class="button button-primary"><?php esc_html_e( 'Rate now', 'filebird' ); ?></a>
		<a href="javascript:;" data="later" class="button"><?php esc_html_e( 'Later', 'filebird' ); ?></a>
		<a href="javascript:;" data="alreadyDid" class="button"><?php esc_html_e( 'No, thanks', 'filebird' ); ?></a>
	</p>
</div>
				<?php
			}
		}
	}
}
