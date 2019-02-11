<?php
/*
 *
 * Default Page or Service Template
 *
 */
?>
  
<?php 
  $who = rwmb_meta( 'service_who' ); 
  $requirements = rwmb_meta( 'service_requirements' ); 
  $where_when = rwmb_meta( 'service_where_when' ); 

  $is_address = rwmb_meta('service_where_when_address_select');
  $contact_content = rwmb_meta('service_where_when_std_address');

  $cost = rwmb_meta('service_cost');
  $how = rwmb_meta('service_how');
  $how_stepped_select = rwmb_meta('service_how_stepped_select');
  $how_steped = rwmb_meta('service_how_stepped_content');

  $renewal = rwmb_meta('service_renewal_requirements');
?>

<?php get_template_part('partials/services/content', 'start-process'); ?>

<?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
<div class="row">
  <div class="columns">
    <?php the_content(); ?>
  </div>
</div>
<?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

<section>
  <h3 id="who" class="black bg-ghost-gray phm-mu mtl mbm">Who</h3>
  <div class="phm-mu"><?php echo $who ?></div>
</section>

<section>
  <h3 id="requirements" class="black bg-ghost-gray phm-mu mtl mbm">Requirements</h3>
  <div class="phm-mu"><?php echo $requirements ?></div>
</section>

<section>
  <h3 id="where-when" class="black bg-ghost-gray phm-mu mtl mbm">Where and when</h3>
  <div class="phm-mu"><?php echo $where_when ?></div>
  <?php include( locate_template( 'partials/global/contact-information.php' ) );?>
</section>


<section>
  <h3 id="cost" class="black bg-ghost-gray phm-mu mtl mbm">Cost</h3>
  <div class="phm-mu"><?php echo $cost ?></div>
</section>


<section>
  <h3 id="how" class="black bg-ghost-gray phm-mu mtl mbm">How</h3>
  <div class="phm-mu"><?php echo $how ?></div>
</section>

<section>
  <h3 id="renewal" class="black bg-ghost-gray phm-mu mtl mbm">Renewal requirements</h3>
  <div class="phm-mu"><?php echo $renewal ?></div>
</section>


<?php $heading_groups = rwmb_meta( 'phila_heading_groups' ); ?>
<?php include(locate_template('partials/content-heading-groups.php')); ?>

<?php get_template_part( 'partials/content', 'additional' ); ?>
