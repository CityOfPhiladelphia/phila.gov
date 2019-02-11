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
  $address_select_where_when = rwmb_meta('service_where_when_address_select');
  $address_where_when = rwmb_meta('service_where_when_std_address');

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
  <h3 id="requirements" class="black bg-ghost-gray phm-mu mtl mbm">Requirements</h3>
  <div class="phm-mu"><?php echo $requirements ?></div>
</section>


<?php $heading_groups = rwmb_meta( 'phila_heading_groups' ); ?>
<?php include(locate_template('partials/content-heading-groups.php')); ?>

<?php get_template_part( 'partials/content', 'additional' ); ?>
