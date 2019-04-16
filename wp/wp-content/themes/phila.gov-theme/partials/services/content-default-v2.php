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

  $is_cost_callout = rwmb_meta( 'service_cost_callout_select' );
  $cost_callout = rwmb_meta( 'service_cost_callout' );

  $cost = trim( rwmb_meta( 'service_cost' ) );
  $cost = isset( $cost ) ? phila_remove_empty_p_tags( $cost ) : false;

  $is_payment_info = rwmb_meta('service_payment_info_select');
  $payment_info = rwmb_meta('service_payment_info');

  $how = trim( rwmb_meta( 'service_how' ) );
  $how = isset( $how ) ? phila_remove_empty_p_tags( $how ) : false;

  $how_stepped_select = rwmb_meta('service_how_stepped_select');
  $how_steped = rwmb_meta('service_how_stepped_content');

  $renewal = trim( rwmb_meta( 'service_renewal_requirements' ) );
  $renewal = isset( $renewal ) ? phila_remove_empty_p_tags( $renewal ) : false;
?>


<?php 
  $process = rwmb_meta( 'service_before_you_begin' );
?>

<?php get_template_part('partials/services/content', 'before-begin'); ?>

<?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
<?php if( !empty (get_the_content() ) )  : ?>
<section>
  <div class="row">
    <div class="columns">
      <h3 id="service-overview" class="black bg-ghost-gray phm-mu mtl mbm">Service overview</h3>
      <div class="phm-mu"><?php the_content(); ?></div>
    </div>
  </div>
</section>
<?php endif; ?>

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

<?php if ( !empty( $cost ) || !empty( $is_cost_callout ) ): ?>
<div class="cost">
  <section>
    <h3 id="cost" class="black bg-ghost-gray phm-mu mtl mbm">Cost</h3>
    <?php if ( !empty( $is_cost_callout ) ): ?>
      <div class="grid-x grid-margin-x">
        <?php $count = count($cost_callout['cost_callout']) ?>
        <?php foreach ( $cost_callout['cost_callout'] as $callout ): ?>
          <div class="medium-<?php echo phila_grid_column_counter($count)?> cell align-self-stretch panel info">
            <div class="center heading">
              <div class="title pvxs"> <?php echo $callout['heading'] ?></div>
              <span class="symbol">
                $<span class="large-text"><?php echo $callout['amount']; ?></span>
              </span>  
                <?php if ( isset($callout['description'] ) ) : ?>
                  <div class="pam">
                    <?php echo apply_filters( 'the_content', $callout['description']) ?>
                  </div>
                <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <div class="phm-mu <?php echo !empty( $is_cost_callout) ? 'ptl' : '' ?>"><?php echo $cost ?></div>
    <?php if (!empty($is_payment_info)) : ?>
      <div class="reveal reveal--announcement" id="payment-information" data-reveal aria-labelledby="payment-information">
        <h2 id="payment-information">Payment information</h2>
        <button class="close-button" data-close aria-label="Close modal" type="button">
          <span aria-hidden="true">&times;</span>
        </button>
        <?php echo do_shortcode($payment_info) ?>
      </div>
      <div class="phm-mu"><a data-open="payment-information"><i class="fas fa-info-circle"></i> Payment information</a></div>
    <?php endif ?>
  </section>
</div>
<?php endif ?>

<?php if ( !empty( $how || !empty( $how_stepped_select ) ) ): ?>
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

<?php include(locate_template('partials/content-additional.php')); ?>
