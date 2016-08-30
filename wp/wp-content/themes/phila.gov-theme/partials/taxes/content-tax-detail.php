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

  $additional_content = rwmb_meta('phila_additional_content');
  $more = phila_additional_content( $additional_content );

?>
<div class="row equal-height">
  <div class="medium-12 columns">
    <div class="panel info center heading equal">
      <div class="title pvxs">
        <i class="fa fa-calendar" aria-hidden="true"></i>
 Due Date</div>
       <div class="pam">
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
  <div class="medium-12 columns">
    <div class="panel info center heading equal">
      <div class="title pvxs">
        <i class="fa fa-usd" aria-hidden="true"></i>
  Tax Rate</div>
      <div class="pam">
          <?php if ( !empty( $tax['cost']['number'] ) ) : ?>
            <div class="numbers mbm">
              <?php if( $tax['due']['type'] == 'yearly') : ?>
                <div class="h4"><br></div>
              <?php endif; ?>
              <span class="symbol">
                <?php echo ($tax['cost']['unit'] == 'dollar') ? '$' : ''; ?></span><span class="large-text"><?php echo $tax['cost']['number']; ?></span><span class="symbol"><?php echo ($tax['cost']['unit'] == 'percent') ? '%' : ''; ?></span><span class="symbol small"><?php echo ($tax['cost']['unit'] == 'mills') ? 'mills' : '';
                  ?></span>
              </span>
            </div>
          <?php endif; ?>
        <div><?php echo apply_filters( 'the_content', $tax['cost']['summary_brief'] ); ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="columns">
    <?php the_content(); ?>
  </div>
</div>

<?php if ( !empty($payments['who_pays'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm">Who Pays the Tax</h3>
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
      <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm">Important Dates</h3>
      <div class="phm-mu"><?php echo apply_filters( 'the_content', $tax['due']['summary_detailed']); ?></div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $tax['cost']['summary_detailed'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm">Tax Rates, Penalties, & Fees</h3>
      <div class="phm-mu">
        <h4>How much is it?</h4>
        <?php echo apply_filters( 'the_content', $tax['cost']['summary_detailed'] ); ?>
      </div>
      <?php if ( !empty( $payments['late_fees'] ) ) : ?>
      <hr class="mhm-mu">
      <div class="phm-mu">
        <h4>What happens if you don't pay on time?</h4>
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
      <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm">Discounts & Exemptions</h3>
      <div class="phm-mu">
        <h4>Are you eligible for a discount?</h4>
        <?php echo apply_filters( 'the_content',  $payments['discounts'] );?>
      </div>
      <?php if ( !empty( $payments['exemptions'] ) ) : ?>
      <hr class="mhm-mu">
      <div class="phm-mu">
        <h4>Can you be excused from paying the tax?</h4>
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
      <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm">How to Pay</h3>
      <div class="phm-mu">
        <?php foreach ( $intro as $item ): ?>
          <div class="mbm">
            <?php if ( isset( $item['phila_wysiwyg_heading'] ) ): ?>
              <h4 class="mbn"><?php echo $item['phila_wysiwyg_heading']; ?></h4>
            <?php endif; ?>
            <?php $wysiwyg_content = isset( $item['phila_wysiwyg_content'] ) ? $item['phila_wysiwyg_content'] : ''; ?>
            <?php $is_address = isset( $item['phila_address_select'] ) ? $item['phila_address_select'] : ''; ?>
            <?php if ( (!empty($wysiwyg_content) || (!empty($is_address) ) ) ) : ?>
            <div class="plm">
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
                    <span class="street-address"><?php echo $address_2; ?></span></br>
                  <?php endif; ?>
                  <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?>
                  <span class="postal-code"><?php echo $zip; ?></span>
                </div>
                <?php endif;?>
            </div>
            <?php endif;?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php
      //display if there is more than one step
      if ( isset($steps) && count($steps) > 1 ) : ?>

      <div class="step-group">
        <?php $counter = 0; ?>
        <?php foreach ( $steps as $step ): ?>
          <?php $is_address = isset( $step['phila_address_step'] ) ? $step['phila_address_step'] : '';
          $counter++; ?>
          <div class="step-label"><?php echo $counter; ?></div>

          <div class="step">
            <div class="step-title"><?php echo isset( $step['phila_step_wysiwyg_heading'] ) ? $step['phila_step_wysiwyg_heading'] : ''; ?></div>
            <div class="step-content">
              <?php $step_wysiwyg_content = isset( $step['phila_step_wysiwyg_content'] ) ? $step['phila_step_wysiwyg_content'] : ''; ?>
              <?php echo apply_filters( 'the_content', $step_wysiwyg_content ); ?>
                <?php
                $address_1 = isset( $step['phila_std_address']['address_group']['phila_std_address_st_1'] ) ? $step['phila_std_address']['address_group']['phila_std_address_st_1'] : '';

                $address_2 = isset( $step['phila_std_address']['address_group']['phila_std_address_st_2'] ) ? $step['phila_std_address']['address_group']['phila_std_address_st_2'] : '';

                $city = isset( $step['phila_std_address']['address_group']['phila_std_address_city'] ) ? $step['phila_std_address']['address_group']['phila_std_address_city'] : '';

                $state = isset( $step['phila_std_address']['address_group']['phila_std_address_state'] ) ? $step['phila_std_address']['address_group']['phila_std_address_state'] : '';

                $zip = isset( $step['phila_std_address']['address_group']['phila_std_address_zip'] ) ? $step['phila_std_address']['address_group']['phila_std_address_zip'] : '';
                ?>

                <?php if ( !empty( $address_1 ) ) : ?>
                <div class="vcard">
                  <span class="street-address"><?php echo $address_1; ?></span><br>
                  <?php if ( !empty($address_2) ) : ?>
                    <span class="street-address"><?php echo $address_2; ?></span></br>
                  <?php endif; ?>
                  <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?>
                  <span class="postal-code"><?php echo $zip; ?></span>
                </div>
                <?php endif; ?>

            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    </section>
  </div>
</div>

<?php if ( !empty( $tax['code'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm">Tax Code</h3>
        <div class="phm-mu">
          <span class="border-black-thin pas inline-block"><?php echo $tax['code'] ?></span>
        </div>
    </section>
  </div>
</div>
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
