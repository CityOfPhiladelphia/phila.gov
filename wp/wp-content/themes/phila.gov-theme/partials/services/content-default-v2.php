<?php
/*
 *
 * Default Page or Service Template
 *
 */
?>
  
<?php 

  $who = trim( rwmb_meta( 'service_who' ) ); 
  $who = isset( $who ) ? phila_remove_empty_p_tags( $who ) : false;

  $requirements = trim( rwmb_meta( 'service_requirements' ) ); 
  $requirements = isset( $requirements ) ? phila_remove_empty_p_tags( $requirements ) : false;

  $where_when = trim( rwmb_meta( 'service_where_when' ) ); 
  $where_when = isset( $where_when ) ? phila_remove_empty_p_tags( $where_when ) : false;

  $is_address = rwmb_meta('service_where_when_address_select');
  $contact_content = rwmb_meta('service_where_when_std_address');

  $cost = trim( rwmb_meta( 'service_cost' ) );
  $cost = isset( $cost ) ? phila_remove_empty_p_tags( $cost ) : false;

  $how = trim( rwmb_meta( 'service_how' ) );
  $how = isset( $how ) ? phila_remove_empty_p_tags( $how ) : false;

  $how_stepped_select = rwmb_meta('service_how_stepped_select');
  $how_steped = rwmb_meta('service_how_stepped_content');

  $renewal = trim( rwmb_meta( 'service_renewal_requirements' ) );
  $renewal = isset( $renewal ) ? phila_remove_empty_p_tags( $renewal ) : false;

?>

<?php get_template_part('partials/services/content', 'start-process'); ?>

<?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
<div class="row">
  <div class="columns">
    <?php the_content(); ?>
  </div>
</div>
<?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

<?php if ( !empty( $who ) ) : ?>
  <section>
    <h3 id="who" class="black bg-ghost-gray phm-mu mtl mbm">Who</h3>
    <div class="phm-mu"><?php echo $who ?></div>
  </section>
<?php endif ?>

<?php if ( !empty( $requirements ) ): ?>
<section>
  <h3 id="requirements" class="black bg-ghost-gray phm-mu mtl mbm">Requirements</h3>
  <div class="phm-mu"><?php echo $requirements ?></div>
</section>
<?php endif ?>

<?php if ( !empty($where_when)   ): ?>
<section>
  <h3 id="where-when" class="black bg-ghost-gray phm-mu mtl mbm">Where and when</h3>
  <div class="phm-mu"><?php echo $where_when ?></div>
  <div class="phm-mu"><?php include( locate_template( 'partials/global/contact-information.php' ) );?></div>
</section>
<?php endif ?>

<?php if ( !empty($cost) ): ?>
<section>
  <h3 id="cost" class="black bg-ghost-gray phm-mu mtl mbm">Cost</h3>
  <div class="phm-mu"><?php echo $cost ?></div>
</section>
<?php endif ?>

<?php if ( !empty($how) ): ?>
<section>
  <h3 id="how" class="black bg-ghost-gray phm-mu mtl mbm">How</h3>
  <div class="phm-mu"><?php echo $how ?></div>

  <?php if ( !empty( $how_stepped_select ) ) :?>
    <?php $steps = phila_extract_stepped_content($how_steped);?>
    <div class="phm-mu">
      <?php include( locate_template( 'partials/stepped-content.php' ) );?>
    </div>
  <?php endif;?>
</section>
<?php endif ?>

<?php if ( !empty($renewal) ): ?>
<section>
  <h3 id="renewal" class="black bg-ghost-gray phm-mu mtl mbm">Renewal requirements</h3>
  <div class="phm-mu"><?php echo $renewal ?></div>
</section>
<?php endif ?>


<?php $heading_groups = rwmb_meta( 'phila_heading_groups' ); ?>
<?php include(locate_template('partials/content-heading-groups.php')); ?>

<?php get_template_part( 'partials/content', 'additional' ); ?>
