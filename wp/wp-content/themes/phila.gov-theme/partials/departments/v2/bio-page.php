<?php $content = rwmb_meta( 'phila_v2_homepage_hero', ['size' => 'full'], $parent ); ?>

<?php $contact_content = isset($content['address_group']) ? $content['address_group'] : ''; ?>
<?php include(locate_template('/partials/global/contact-information.php')); ?>