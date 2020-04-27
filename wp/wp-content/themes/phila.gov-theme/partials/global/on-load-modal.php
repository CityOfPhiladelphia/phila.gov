<?php 
    $classes = get_body_class();
    $modal_exists = true;
    if (    (  in_array('department_page-template-default',$classes) && in_array('department-landing',$classes )) ||
            ( in_array('programs-template-default',$classes) && wp_get_post_parent_id( get_the_ID()) == 0) ) {
        $modal_content = rwmb_meta( 'cto_modal_text' ); 
        $modal_button_text = rwmb_meta( 'cto_modal_button_text' );
    }
    else if (   ( in_array('department_page-template-default',$classes) && !in_array('department-landing',$classes )) ||
                ( in_array('programs-template-default',$classes) && wp_get_post_parent_id( get_the_ID()) != 0) ) {
        $post_parent = wp_get_post_parent_id( get_the_ID() );
        $modal_content = get_post_meta( $post_parent, 'cto_modal_text', TRUE );
        $modal_button_text = get_post_meta( $post_parent, 'cto_modal_button_text', TRUE );
    }
    if( $modal_content == null || 
        $modal_content == '' || 
        $modal_button_text == null || 
        $modal_button_text == ''
    ) {
        $modal_exists = false;
    }
?>

<?php if( $modal_exists ) { ?>
    <div class="reveal center cto-modal" id="cto-modal" data-reveal data-deep-link="true">
        <div class="content">
            <?php echo $modal_content; ?>
        </div>
        <button class="button-text" aria-label="Close modal" type="button" data-close>
            <?php echo $modal_button_text; ?>
        </button>
    </div>
<?php } ?>