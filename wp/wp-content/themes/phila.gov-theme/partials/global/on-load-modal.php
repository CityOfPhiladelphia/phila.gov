<?php 
    list($modal_exists, $modal_content, $modal_button_text) = phila_apply_modal_to_children_pages();
?>

<?php if( $modal_exists ) { ?>
    <div class="reveal center phila-modal" id="disclaimer-modal" data-reveal data-deep-link="true" data-options="closeOnClick:false; closeOnEsc:false;">
        <div class="content">
            <?php echo $modal_content; ?>
            <button class="button-text" aria-label="Close modal" type="button" data-close>
                <?php echo $modal_button_text; ?>
            </button>
        </div>
    </div>
<?php } ?>