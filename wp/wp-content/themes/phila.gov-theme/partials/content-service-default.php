<?php
/*
 *
 * Default Services Template
 *
 */
 ?>
<?php

  $heading_groups = rwmb_meta( 'phila_heading_groups' );
  $heading_content = phila_extract_clonable_wysiwyg( $heading_groups );

  $additional_content = rwmb_meta('phila_additional_content');
  $more = phila_additional_content( $additional_content );

?>

<div class="row">
  <div class="columns">
    <?php the_content(); ?>
  </div>
</div>

<?php if ( !empty($heading_content) ) : ?>
  <?php foreach ( $heading_content as $content ): ?>

  <div class="row">
    <div class="columns">
      <section>
      <?php if ( isset( $content['phila_wysiwyg_heading'] ) ): ?>
        <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm"><?php echo $content['phila_wysiwyg_heading']; ?></h3>
      <?php endif; ?>

      <?php $wysiwyg_content = isset( $content['phila_unique_wysiwyg_content'] ) ? $content['phila_unique_wysiwyg_content'] : ''; ?>
      <?php $is_address = isset( $content['phila_address_select'] ) ? $content['phila_address_select'] : ''; ?>

      <?php if ( (!empty($wysiwyg_content) || (!empty($is_address) ) ) ) : ?>
        <?php echo apply_filters( 'the_content', $wysiwyg_content ) ;?>
          <?php
          $address_1 = isset( $content['phila_std_address']['address_group']['phila_std_address_st_1'] ) ? $content['phila_std_address']['address_group']['phila_std_address_st_1'] : '';

          $address_2 = isset( $content['phila_std_address']['address_group']['phila_std_address_st_2'] ) ? $content['phila_std_address']['address_group']['phila_std_address_st_2'] : '';

          $city = isset( $content['phila_std_address']['address_group']['phila_std_address_city'] ) ? $content['phila_std_address']['address_group']['phila_std_address_city'] : '';

          $state = isset( $content['phila_std_address']['address_group']['phila_std_address_state'] ) ? $content['phila_std_address']['address_group']['phila_std_address_state'] : '';

          $zip = isset( $content['phila_std_address']['address_group']['phila_std_address_zip'] ) ? $content['phila_std_address']['address_group']['phila_std_address_zip'] : '';
          ?>

          <?php if ( $is_address == 1 ) : ?>
          <div class="vcard">
            <span class="street-address"><?php echo $address_1; ?></span><br>
            <?php if ( !empty($address_2) ) : ?>
              <span class="street-address"><?php echo $address_2; ?></span><br>
            <?php endif; ?>
            <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?></span>
            <span class="postal-code"><?php echo $zip; ?></span>
          </div>
          <?php endif;?>
        <?php endif;?>
        </section>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>


<?php if ( !empty($more['forms']) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm">Forms & Instructions</h3>
      <div class="phm-mu">
        <?php foreach ( $more['forms'] as $form ): ?>
          <div class="pvs">
            <a href="<?php echo get_the_permalink($form);?>"><i class="fa fa-file-text" aria-hidden="true"></i> <?php echo get_the_title($form); ?></a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $more['related'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm">Related Content</h3>
      <div class="phm-mu">
        <?php echo apply_filters( 'the_content', $more['related']); ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<div class="row equal-height mtl">
  <?php if ( !empty($more['aside']['did_you_know'] ) ) : ?>
   <div class="medium-<?php echo (!empty( $more['aside']['questions'] ) ) ? '12' : '24'; ?> columns">
      <div class="panel info equal">
        <aside>
          <h3><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Did you know?</h3>
          <?php echo apply_filters( 'the_content', $more['aside']['did_you_know'] ); ?>
        </aside>
      </div>
  </div>
<?php endif; ?>
<?php if ( !empty( $more['aside']['questions'] ) ) : ?>
 <div class="medium-<?php echo (!empty( $more['aside']['did_you_know'] ) ) ? '12' : '24'; ?> columns">
    <div class="panel info equal">
      <aside>
        <h3><i class="fa fa-comments" aria-hidden="true"></i> Questions?</h3>
        <?php echo apply_filters( 'the_content', $more['aside']['questions'] );?>
      </aside>
    </div>
  </div>
<?php endif; ?>
</div>
