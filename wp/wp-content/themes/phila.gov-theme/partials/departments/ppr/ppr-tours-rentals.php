<?php
    $custom_wysiwyg = rwmb_meta('phila_custom_wysiwyg');
    if($custom_wysiwyg):
?>

<section class="row mvl ppr-tours-rentals">
    <div class="columns">
        <h2 class="contrast"><?= $custom_wysiwyg['phila_wysiwyg_title']; ?></h2>
        <?= $custom_wysiwyg['phila_wysiwyg_content']; ?>
    </div>
</section>

<?php endif; ?>
