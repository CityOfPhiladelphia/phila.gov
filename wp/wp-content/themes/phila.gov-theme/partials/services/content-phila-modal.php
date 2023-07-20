<?php
    $phila_modal_link_text = $phila_modal['phila_modal_link_text']; 
    $phila_modal_content = $phila_modal['phila_modal_wysiwyg'];
?>

<?php if ( !empty($phila_modal) && !empty( $phila_modal_link_text ) ) : ?>
    <div class="reveal reveal--announcement" id="<?php echo sanitize_title_with_dashes($phila_modal_link_text)?>" data-reveal aria-labelledby="<?php echo sanitize_title_with_dashes($phila_modal_link_text)?>">
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="mtl"><?php echo apply_filters( 'the_content', $phila_modal_content); ?></div>
    </div>
    <div class="phm-mu"><button class="link" data-open="<?php echo sanitize_title_with_dashes($phila_modal_link_text)?>"><i class="fas fa-info-circle"></i> <?php echo $phila_modal_link_text ?></button></div>
<?php endif ?>