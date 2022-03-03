<?php
/**
*
* Shortcode for easy display of covid stages
* [display_category]
*
* @package phila-gov_customization
*/
function covid_response_level( $atts ) {
  $a = shortcode_atts( array(
    'stage' => 0,
  ), $atts );
  ?>

  <?php 
    if ($a['stage'] == '1') {
      $stage_1 = 'current';
      $stage_2 = 'inactive';
      $stage_3 = 'inactive';
      $stage_4 = 'inactive';
    } else if ($a['stage'] == '2') {
      $stage_1 = 'inactive';
      $stage_2 = 'current';
      $stage_3 = 'inactive';
      $stage_4 = 'inactive';
    } else if ($a['stage'] == '3') {
      $stage_1 = 'inactive';
      $stage_2 = 'inactive';
      $stage_3 = 'current';
      $stage_4 = 'inactive';
    } else if ($a['stage'] == '4') {
      $stage_1 = 'inactive';
      $stage_2 = 'inactive';
      $stage_3 = 'inactive';
      $stage_4 = 'current';
    }
  
  ?>

  <section class="stage-tracker phl">
    <h2 class="stages-header mbm">Philadelphia COVID-19 response levels</h2>
    <div class="process">
      <div class="row collapse process-bar">
        <div class="small-24 medium-12 large-6 columns">
          <section class="chevron <?php echo $stage_1; ?>">
            <div class="row collapse current-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray center stage-container">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white">Extreme Caution</h4>
                </div>
              </div>
            </header>
            <div class="description">
              <ul>
                <li>Present your vaccine card or exemption to dine indoors</li>
                <li>Wear a mask when indoors in public places</li>
              </ul>
            </div>
          </section>
        </div>
        <div class="small-24 medium-12 large-6 columns small-text">
          <section class="chevron <?php echo $stage_2; ?>">
            <div class="row collapse current-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray center stage-container">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white">Caution </h4>
                </div>
              </div>
            </header>
            <div class="description">
              <ul>
                <li>Present your vaccine card or exemption or a negative test within 24 hours to dine indoors</li>
                <li>Wear a mask when indoors in public places</li>
              </ul>
            </div>
          </section>
        </div>
        <div class="small-24 medium-12 large-6 columns small-text">
          <section class="chevron <?php echo $stage_3; ?>">
            <div class="row collapse current-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray center stage-container">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white">Mask Precautions</h4>
                </div>
              </div>
            </header>
            <div class="description">
              <ul>
                <li>Wear a mask when indoors in public places</li>
              </ul>
            </div>
          </section>
        </div>
        <div class="small-24 medium-12 large-6 columns end">
          <section class="chevron <?php echo $stage_4; ?>">
            <div class="row collapse current-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray center stage-container">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white">All Clear</h4>
                </div>
              </div>
            </header>
          </section>
        </div>
      </div>
    </div>
  </section>
  <?php 
}

add_action( 'init', 'register_covid_response_level' );

function register_covid_response_level(){
  add_shortcode( 'covid_response_level', 'covid_response_level' );
}