<?php
/*
 *
 * Tax Detail Template
 *
 */
 ?>
<?php
  $tax_highlights = rwmb_meta( 'phila_tax_highlights' );
  $tax = phila_tax_highlight( $tax_highlights );

  $tax_payments = rwmb_meta( 'phila_tax_payment_info' );
  $payments = phila_tax_payment_info( $tax_payments );

  $payment_group = rwmb_meta( 'phila_payment_group' );

  $intro = phila_extract_clonable_wysiwyg( $payment_group );

  $steps = phila_extract_stepped_content( $payment_group );

?>
<?php if ($tax['callout'] != '') :?>
<div class="row">
  <div class="columns mbl">
    <?php echo do_shortcode('[callout type="important" inline="false"]' . $tax['callout'] . '[/callout]'); ?>
  </div>
</div>
<?php endif; ?>
<div class="row equal-height">
  <div class="medium-12 columns">
    <div class="panel info center heading">
      <div class="title pvxs">
        <i class="fa fa-calendar" aria-hidden="true"></i>
 Due date</div>
       <div class="valign equal">
         <div class="pam valign-cell">
           <?php if ($tax['due']['type'] != 'misc') : ?>
             <?php if( $tax['due']['type'] == 'yearly') : ?>
               <span class="h4"><?php echo $tax['due']['month'] ?></span>
             <?php endif; ?>
              <div class="numbers"><span class="large-text"><?php echo $tax['due']['date'] ?></span><span class="symbol"><?php echo phila_return_ordinal( $tax['due']['date'] ); ?></span></div>
              <div class="mtm"><?php echo $tax['due']['summary_brief'] ?></div>
           <?php else : ?>
             <?php echo apply_filters( 'the_content', $tax['due']['misc']); ?>
           <?php endif; ?>
       </div>
      </div>
    </div>
  </div>
  <div class="medium-12 columns">
    <div class="panel info center heading">
      <div class="title pvxs">
        <i class="fa fa-usd" aria-hidden="true"></i>
  Tax rate</div>
      <div class="valign equal">
        <div class="pam valign-cell">
          <?php if ( !empty( $tax['cost']['number'] ) ) : ?>
            <div class="numbers mbm">
              <?php if( $tax['due']['type'] == 'yearly') : ?>
                <div class="h4"><br></div>
              <?php endif; ?>
              <span class="symbol">
                <?php echo ($tax['cost']['unit'] == 'dollar') ? '$' : ''; ?><span class="large-text"><?php echo $tax['cost']['number']; ?></span><span class="symbol"><?php echo ($tax['cost']['unit'] == 'percent') ? '%' : ''; ?></span><span class="symbol small"><?php echo ($tax['cost']['unit'] == 'mills') ? 'mills' : '';
                  ?></span>
              </span>
            </div>
            <?php endif; ?>
          <div><?php echo apply_filters( 'the_content', $tax['cost']['summary_brief'] ); ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
  <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
<div class="row">
  <div class="columns">
    <?php the_content(); ?>
  </div>
</div>
  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
<?php if ( !empty($payments['who_pays'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 id="who-pays" class="black bg-ghost-gray phm-mu mtl mbm">Who pays the tax</h3>
      <div class="phm-mu">
        <?php echo apply_filters( 'the_content', $payments['who_pays'] ); ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $tax['due']['summary_detailed'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 id="important-dates" class="black bg-ghost-gray phm-mu mtl mbm">Important dates</h3>
      <div class="phm-mu"><?php echo apply_filters( 'the_content', $tax['due']['summary_detailed']); ?></div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $tax['cost']['summary_detailed'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 id="tax-rates-penalties-fees" class="black bg-ghost-gray phm-mu mtl mbm">Tax rates, penalties, & fees</h3>
      <div class="phm-mu">
        <h4>How much is it?</h4>
        <?php echo apply_filters( 'the_content', $tax['cost']['summary_detailed'] ); ?>
      </div>
      <?php if ( !empty( $payments['late_fees'] ) ) : ?>
      <hr class="mhm-mu">
      <div class="phm-mu">
        <h4 id="what-happens">What happens if you don't pay on time?</h4>
        <?php echo apply_filters( 'the_content', $payments['late_fees'] ); ?>
      </div>
      <?php endif; ?>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $payments['discounts'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 id="discounts-exemptions" class="black bg-ghost-gray phm-mu mtl mbm">Discounts & exemptions</h3>
      <div class="phm-mu">
        <h4>Are you eligible for a discount?</h4>
        <?php echo apply_filters( 'the_content',  $payments['discounts'] );?>
      </div>
      <?php if ( !empty( $payments['exemptions'] ) ) : ?>
      <hr class="mhm-mu">
      <div class="phm-mu">
        <h4 id="excused">Can you be excused from paying the tax?</h4>
        <?php echo apply_filters( 'the_content', $payments['exemptions'] );?>
      </div>
      <?php endif; ?>
    </section>
  </div>
</div>
<?php endif; ?>
<?php if ( !empty($intro) || !empty($steps) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 id="how-to-pay" class="black bg-ghost-gray phm-mu mtl mbm">How to pay</h3>
      <div class="phm-mu">
        <?php foreach ( $intro as $item ): ?>
          <div class="mbm">
            <?php if ( isset( $item['phila_wysiwyg_heading'] ) ): ?>
              <h4 class="mbn"><?php echo $item['phila_wysiwyg_heading']; ?></h4>
            <?php endif; ?>
            <?php $wysiwyg_content = isset( $item['phila_wysiwyg_content'] ) ? $item['phila_wysiwyg_content'] : ''; ?>
            <?php $is_address = isset( $item['phila_address_select'] ) ? $item['phila_address_select'] : ''; ?>
            <?php if ( (!empty($wysiwyg_content) || (!empty($is_address) ) ) ) : ?>
            <?php echo apply_filters( 'the_content', $wysiwyg_content ) ;?>
              <?php
              $address_1 = isset( $item['phila_std_address']['address_group']['phila_std_address_st_1'] ) ? $item['phila_std_address']['address_group']['phila_std_address_st_1'] : '';

              $address_2 = isset( $item['phila_std_address']['address_group']['phila_std_address_st_2'] ) ? $item['phila_std_address']['address_group']['phila_std_address_st_2'] : '';

              $city = isset( $item['phila_std_address']['address_group']['phila_std_address_city'] ) ? $item['phila_std_address']['address_group']['phila_std_address_city'] : '';

              $state = isset( $item['phila_std_address']['address_group']['phila_std_address_state'] ) ? $item['phila_std_address']['address_group']['phila_std_address_state'] : '';

              $zip = isset( $item['phila_std_address']['address_group']['phila_std_address_zip'] ) ? $item['phila_std_address']['address_group']['phila_std_address_zip'] : '';
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
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php
      //display if there is more than one step
      if ( isset($steps) && count($steps) > 1 ) :

        include( locate_template( 'partials/stepped-content.php' ) );

      endif; ?>
    </section>
  </div>
</div>

<?php if ( !empty( $tax['code'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 id="tax-code" class="black bg-ghost-gray phm-mu mtl mbm">Tax code</h3>
        <div class="phm-mu">
          <span class="bdr-all bdr-black pas inline-block"><?php echo $tax['code'] ?></span>
        </div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php get_template_part( 'partials/content', 'additional' ); ?>
