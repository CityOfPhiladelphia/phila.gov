<?php

use FileBird\Classes\Tree;

defined( 'ABSPATH' ) || exit;

class acf_field_filebird extends acf_field {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
        $this->name     = 'filebird_folder';
		$this->label    = __( 'FileBird Folder', 'filebird' );
		$this->category = 'Relational';
		$this->defaults = array(
			'field_type' => 'checkbox',
		);
		parent::__construct();
	}

	public function create_field( $field ) {
		if ( $field['field_type'] == 'checkbox' ) {

			$field['name'] .= '[]';
		}

		if ( ! is_array( $field['value'] ) ) {
			$field['value'] = array( $field['value'] );
		}
		$folders = Tree::getFolders( null );
		?>
		<div class="acf-taxonomy-field">
			<div class="categorychecklist-holder">
				<ul class="acf-checkbox-list acf-bl acf-filebird">
					<?php $this->render_folder_list( $folders, $field ); ?>
				</ul>
			</div>
		</div>
		<?php
	}

	public function render_folder_list( $folders, $field ) {
		foreach ( $folders as $folder ) {
			$selected = is_array( $field['value'] ) ? in_array( $folder['id'], $field['value'] ) : false;
			?>
			<li data-id="<?php echo esc_attr( $folder['id'] ); ?>">
				<label <?php echo ( $selected ? ' class="selected"' : '' ); ?>>
					<input type="checkbox" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $folder['id'] ); ?>" <?php echo ( $selected ? 'checked="checked"' : '' ); ?>>
					<span><?php echo esc_html( $folder['text'] ); ?></span>
				</label>
				<?php if ( count( $folder['children'] ) > 0 ) : ?>
					<ul class="children acf-bl">
						<?php $this->render_folder_list( $folder['children'], $field ); ?>
					</ul>
				<?php endif; ?>
			</li>
		<?php
		}
	}
}

acf_field_filebird::getInstance();