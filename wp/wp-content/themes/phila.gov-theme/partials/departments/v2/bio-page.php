<?php $headshots = rwmb_meta( 'phila_person_headshot', $parent ); ?>
<?php $headshot = reset( $headshots ) ?>

<?php $title = rwmb_meta('phila_person_title', $parent); ?>
<?php $address = rwmb_meta('phila_std_address', $parent); ?>
<?php $contact_content = isset($address) ? $address : ''; ?>
<?php $is_address = true; ?>

<div class="grid-container mayor-page">
  <div class="grid-x grid-padding-x bio-page custom-text">
    <div class="cell medium-8">
      <?php include(locate_template( 'partials/global/contact-information.php') ) ; ?>

      <?php if (!empty($headshots) ): ?>
        <a class="button content-type-featured mtl" href="<?php echo $headshot['url']; ?>">Download high resolution headshot <i class="fa-solid fa-download" aria-hidden="true"></i></a>
        <?php endif; ?>
    </div>
    <div class="cell medium-16">
      <?php if ( !empty($title) ) : ?>
        <h2 class="mtn"><?php echo $title; ?></h2>
        <?php endif; ?>

      <?php if( get_the_content() != '' ) : ?>
        <!-- WYSIWYG content -->
        <section class="wysiwyg-content">
              <?php echo the_content();?>
        </section>
        <!-- End WYSIWYG content -->
      <?php endif; ?>
    </div>
  </div>
</div>
