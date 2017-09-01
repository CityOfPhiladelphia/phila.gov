<?php

/* Form */

function wpcmsb_form_meta_box( $post ) {
?>
<div class="half-left"><textarea id="wpcmsb-form" name="wpcmsb-form" cols="100" rows="24"><?php echo esc_textarea( $post->prop( 'form' ) ); ?></textarea></div>

<div class="half-right"><div id="taggenerator"></div></div>

<br class="clear" />
<?php
}

function wpcmsb_messages_meta_box( $post ) {
	$updated = isset( $_REQUEST['message'] )
		&& in_array( $_REQUEST['message'], array( 'saved', 'created' ) );
	$count = 0;
	$messages = wpcmsb_messages();

	foreach ( $messages as $key => $arr ) {
		$count += 1;
		$field_name = 'wpcmsb-message-' . strtr( $key, '_', '-' );

?>
<div class="message-field">
<p class="description"><label for="<?php echo $field_name; ?>"><?php echo esc_html( $arr['description'] ); ?></label></p>
<input type="text" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" class="wide" size="70" value="<?php echo esc_attr( $post->message( $key, false ) ); ?>" />
</div>
<?php

		if ( ! $updated && 10 <= count( $messages ) ) {
			if ( 6 == $count ) {
				echo '<p><a href="#" id="show-all-messages">' . esc_html( __( 'Show all messages', 'cms-block' ) ) . '</a></p>' . "\n";
				echo '<div class="hide-initially">';
			}

			if ( count( $messages ) == $count ) {
				echo '</div>';
			}
		}
	}
}

?>