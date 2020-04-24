<?php 
    $modal_content = rwmb_meta( 'cto_modal_text' ); 
    $modal_button_text = rwmb_meta( 'cto_modal_button_text' );
?>

<div class="reveal center cto-modal" id="cto-modal" data-reveal data-deep-link="true">
    <div class="content">
        <?php echo $modal_content; ?>
    </div>
    <div class="button-text">
    <?php echo $modal_button_text; ?>
    </div>
    
    <button class="close-button bg-white" data-close aria-label="Close modal" type="button">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
