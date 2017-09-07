<?php

function wpcmsb_current_action() {
	if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
		return $_REQUEST['action'];

	if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
		return $_REQUEST['action2'];

	return false;
}

function wpcmsb_admin_has_edit_cap() {
	return current_user_can( 'wpcmsb_edit_cms_blocks' );
}

function wpcmsb_save_cms_block( $post_id = -1 ) {
	if ( -1 != $post_id ) {
		$cms_block = wpcmsb_cms_block( $post_id );
	}

	if ( empty( $cms_block ) ) {
		$cms_block = wpcmsb_cmsblock::get_template();
	}

	if ( isset( $_POST['post_title'] ) ) {
		$cms_block->set_title( $_POST['post_title'] );
	}

	
	if ( isset( $_POST['wpcmsb-locale'] ) ) {
		$locale = trim( $_POST['wpcmsb-locale'] );

		if ( wpcmsb_is_valid_locale( $locale ) ) {
			$cms_block->locale = $locale;
		}
	}

	$properties = $cms_block->get_properties();
	//htmlspecialchars
	//var_dump($_POST);
	//exit(0);
	//fcp: 04/11/2015 --Graba las variables envoltura, tipo y clase

	if ( isset( $_POST['wpcmsb-wsbenvolver'] ) ) {
		$properties['wsbenvolver'] = trim($_POST['wpcmsb-wsbenvolver']);
		//var_dump(htmlspecialchars($_POST['wpcmsb-form']));		
	}else{
		$properties['wsbenvolver'] = 0;
	}

	if ( isset( $_POST['wpcmsb-wsbautop'] ) ) {
		$properties['wsbautop'] = trim($_POST['wpcmsb-wsbautop']);
		//var_dump(htmlspecialchars($_POST['wpcmsb-form']));		
	}else{
		$properties['wsbautop'] = 0;
	}


	// Paul
	if ( isset( $_POST['wpcmsb-wsbtipoenvol'] ) ) {
		$properties['wsbtipoenvol'] = trim($_POST['wpcmsb-wsbtipoenvol']);
		//var_dump(htmlspecialchars($_POST['wpcmsb-form']));		
	}else{
		$properties['wsbtipoenvol'] =1;
	}

	if ( isset( $_POST['wpcmsb-wsbclaseenvol'] ) ) {
		$properties['wsbclaseenvol'] = trim($_POST['wpcmsb-wsbclaseenvol']);
		//var_dump(htmlspecialchars($_POST['wpcmsb-form']));		
	}else{
		$properties['wsbclaseenvol'] = '';
	}

	//  Paul
	
	if ( isset( $_POST['wpcmsb-form'] ) ) {
		$properties['form'] = trim($_POST['wpcmsb-form']);
		//var_dump(htmlspecialchars($_POST['wpcmsb-form']));
		//exit(0);
	}


	foreach ( wpcmsb_messages() as $key => $arr ) {
		$field_name = 'wpcmsb-message-' . strtr( $key, '_', '-' );

		if ( isset( $_POST[$field_name] ) ) {
			$properties['messages'][$key] = trim( $_POST[$field_name] );
		}
	}

	$cms_block->set_properties( $properties );

	do_action( 'wpcmsb_save_cms_block', $cms_block );

	return $cms_block->save();
}
