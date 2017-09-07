<?php

class wpcmsb_Editor {

	private $cms_block;
	private $panels = array();

	public function __construct( wpcmsb_cmsblock $cms_block ) {
		$this->cms_block = $cms_block;
	}

	public function add_panel( $id, $title, $callback ) {
		if ( wpcmsb_is_name( $id ) ) {
			$this->panels[$id] = array(
				'title' => $title,
				'callback' => $callback );
		}
	}

	public function display() {
		if ( empty( $this->panels ) ) {
			return;
		}

		echo '<ul id="cms-block-editor-tabs">';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<li id="%1$s-tab"><a href="#%1$s">%2$s</a></li>',
				esc_attr( $id ), esc_html( $panel['title'] ) );
		}

		echo '</ul>';
//wp-editor-expand
//cms-block-editor-panel
		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<div class="cms-block-editor-panel" id="%1$s">',
				esc_attr( $id ) );
			call_user_func( $panel['callback'], $this->cms_block );
			echo '</div>';
		}
	}
}

function wpcmsb_editor_panel_form( $post ) {
?>
<textarea id="wpcmsb-form" name="wpcmsb-form" cols="100" rows="24" class="large-text code"><?php echo esc_textarea( $post->prop( 'form' ) ); ?></textarea>
<?php
}
