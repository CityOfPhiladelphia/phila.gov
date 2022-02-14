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

  <section class="stage-tracker mvl phl">
    <h3 class="stages-header mbm">Philadelphia COVID Response Levels</h3>
    <div class="process">
      <div class="row collapse process-bar">
        <div class="small-6 medium-6 columns">
          <section class="chevron <?php echo $stage_1; ?>">
            <div class="row collapse current-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray center">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white"><span class="stage">Extreme Caution</span></h4>
                </div>
              </div>
            </header>
            <div class="description">
              <ul>
                <li>Patient-facing healthcare workers</li>
                <li>Long-term care facility residents and staff</li>
              </ul>
            </div>
          </section>
        </div>
        <div class="small-6 medium-6 columns small-text">
          <section class="chevron <?php echo $stage_2; ?>">
            <div class="row collapse current-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray center">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white"><span class="stage">Caution </span></h4>
                </div>
              </div>
            </header>
            <div class="description">
              <ul>
                <li>Frontline essential workers at high risk for exposure</li>
                <li>Individuals 65+</li>
                <li>Those with the highest risk medical conditions</li>
                <li>Those working or residing in congregate settings</li>
              </ul>
            </div>
          </section>
        </div>
        <div class="small-6 medium-6 columns small-text">
          <section class="chevron <?php echo $stage_3; ?>">
            <div class="row collapse current-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray center">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white"><span class="stage">Mask Precautions</span></h4>
                </div>
              </div>
            </header>
            <div class="description">
              <ul>
                <li>Essential workers at lower risk of exposure</li>
              </ul>
            </div>
          </section>
        </div>
        <div class="small-6 medium-6 columns end">
          <section class="chevron <?php echo $stage_4; ?>">
            <div class="row collapse current-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray center">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white"><span class="stage">All Clear</span></h4>
                </div>
              </div>
            </header>
            <div class="description">
              <ul>
                <li>Anyone 16+ not yet immunized</li>
              </ul>
            </div>
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