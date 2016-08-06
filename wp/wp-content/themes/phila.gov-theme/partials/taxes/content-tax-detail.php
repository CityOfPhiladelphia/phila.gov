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
  $content = phila_additional_content( $additional_content );

?>
<div class="row equal-height">
  <div class="medium-12 columns">
    <div class="panel info center heading equal">
      <div class="title pvxs">
        <i class="fa fa-calendar" aria-hidden="true"></i>
 Due Date</div>
       <div class="pam">
         <div class="numbers"><span class="large-text"><?php echo $tax['due']['date'] ?></span><span class="symbol">th</span></div>
         <div class="mtm"><?php echo $tax['due']['summary_brief'] ?></div>
      </div>
    </div>
  </div>
  <div class="medium-12 columns">
    <div class="panel info center heading equal">
      <div class="title pvxs">
        <i class="fa fa-usd" aria-hidden="true"></i>
  Cost</div>
      <div class="pam">
        <div class="numbers"><span class="large-text"><?php echo $tax['cost']['number'] ?></span><span class="symbol"><?php echo $tax['cost']['unit'] ?></span></div>
        <div class="mtm"><?php echo $tax['cost']['summary_brief'] ?></div>
      </div>
    </div>
  </div>
</div>

<?php if ( !empty($payments['who_pays'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm mtl mbm">Who Pays the Tax</h3>
      <div><?php echo $payments['who_pays'] ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $tax['due']['summary_detailed'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm mtl mbm">Important Dates</h3>
      <div><?php echo $tax['due']['summary_detailed'] ?></div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $tax['cost']['summary_detailed'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm mtl mbm">Costs, Discounts, etc</h3>
      <div>
        <h4>How much is it?</h4>
        <p><?php echo $tax['cost']['summary_detailed'] ?></p>
        <hr>
        <h4>What happens if you dont pay on time?</h4>
          <?php echo $payments['late_fees'] ?>
        <hr>
        <h4>Can you be excused from paying the tax?</h4>
        <?php echo $payments['exemptions']?>
          <div class="vr">
          <p><strong>Religious institutions</strong>
          Encompasses churches, synagogues, chapels, convents, and certain religious orders.</p>
        </div>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( isset($intro) || ( isset($steps) && count($steps) > 1 ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm mtl mbm">How you pay the tax</h3>

      <?php foreach ( $intro as $item ):  ?>
        <h4 class="mbn"><?php echo $item['phila_wywiwyg_heading']; ?></h4>
        <div class="plm">
          <?php echo $item['phila_wywiyyg_content']; ?>
        </div>
      <?php endforeach; ?>

    <?php
    //display if there is more than one step
    if ( isset($steps) && count($steps) > 1 ) : ?>

    <div class="step-group">
      <?php $counter = 0; ?>
      <?php foreach ( $steps as $step ): ?>

        <?php $is_address = isset($step['phila_address_step']) ? $step['phila_address_step'] : '';
        $counter++; ?>
        <div class="step-label"><?php echo $counter; ?></div>

        <div class="step">
          <div class="step-title"><?php echo $step['phila_step_wywiwyg_heading'] ?></div>
          <div class="step-content">
            <?php if ( $is_address == 1 ) : ?>
              <?php
              $address_1 = isset( $step['phila_std_address']['address_group']['phila_std_address_st_1'] ) ? $step['phila_std_address']['address_group']['phila_std_address_st_1'] : '';

              $address_2 = isset( $step['phila_std_address']['address_group']['phila_std_address_st_2'] ) ? $step['phila_std_address']['address_group']['phila_std_address_st_2'] : '';

              $city = isset( $step['phila_std_address']['address_group']['phila_std_address_city'] ) ? $step['phila_std_address']['address_group']['phila_std_address_city'] : '';

              $state = isset( $step['phila_std_address']['address_group']['phila_std_address_state'] ) ? $step['phila_std_address']['address_group']['phila_std_address_state'] : '';

              $zip = isset( $step['phila_std_address']['address_group']['phila_std_address_zip'] ) ? $step['phila_std_address']['address_group']['phila_std_address_zip'] : '';
              ?>

              <div class="vcard">

              <span class="street-address"><?php echo $address_1; ?></span><br>
              <span class="street-address"><?php echo $address_2; ?></span></br>
              <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?>
              <span class="postal-code"><?php echo $zip; ?></span>
            </div>
            <?php else :
              echo $step['phila_step_wywiyyg_content'] ?>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
    <?php endif; ?>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( isset( $tax['code'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm mtl mbm">Tax Code</h3>
      <?php echo $tax['code'] ?>
    </section>
  </div>
</div>
<?php endif; ?>

<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm mtl mbm">Forms & Instructions</h3>
      <!--need to revisit this -->
    </section>
  </div>
</div>

<?php if ( isset( $content['related'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray h2 phm mtl mbm">Related Content</h3>
        <?php echo $content['related'] ?>
    </section>
  </div>
</div>
<?php endif; ?>

<div class="row equal-height">
  <?php if ( isset($content['did_you_know'] ) ) :
    //TODO: logic for a did_you_know or questions to take full width

     ?>
   <div class="medium-12 columns">
      <div class="panel info equal">
        <aside>
          <h3><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Did you know?</h3>
          <?php echo $content['did_you_know'] ?>
        </aside>
      </div>
  </div>
<?php endif; ?>
<?php if ( isset($content['questions'] ) ) : ?>
  <div class="medium-12 columns">
    <div class="panel info equal">
      <aside>
        <h3><i class="fa fa-comments" aria-hidden="true"></i> Questions?</h3>
        <?php echo $content['questions'] ?>
      </aside>
    </div>
  </div>
<?php endif; ?>
</div>
