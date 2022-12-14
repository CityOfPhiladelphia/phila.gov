<?php
/**
*
* Shortcode for easy display of covid phases
* [display_category]
*
* @package phila-gov_customization
*/
function covid_phases( $atts ) {
  $a = shortcode_atts( array(
    'phase' => 0,
  ), $atts );
  ?>

  <?php 
    if ($a['phase'] == '1a') {
      $phase_1a = 'active';
      $phase_1b = 'upcoming';
      $phase_1c = 'upcoming';
      $phase_2 = 'upcoming';
    } else if ($a['phase'] == '1b') {
      $phase_1a = 'completed';
      $phase_1b = 'active';
      $phase_1c = 'upcoming';
      $phase_2 = 'upcoming';
    } else if ($a['phase'] == '1c') {
      $phase_1a = 'completed';
      $phase_1b = 'completed';
      $phase_1c = 'active';
      $phase_2 = 'upcoming';
    } else if ($a['phase'] == '2') {
      $phase_1a = 'completed';
      $phase_1b = 'completed';
      $phase_1c = 'completed';
      $phase_2 = 'active';
    }
  
  ?>

  <section class="phila-redesign covid-phases mvl phl">
    <h3 class="phases-header mbm">Phases</h3>
    <div class="process">
      <div class="row collapse process-bar">
        <div class="small-24 medium-6 columns">
          <section class="chevron <?php echo $phase_1a; ?>">
            <div class="row collapse active-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-3x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white"><span class="phase">Phase </span>1a</h4>
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
        <div class="small-24 medium-6 columns small-text">
          <section class="chevron <?php echo $phase_1b; ?>">
            <div class="row collapse active-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-3x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white"><span class="phase">Phase </span>1b</h4>
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
        <div class="small-24 medium-6 columns small-text">
          <section class="chevron <?php echo $phase_1c; ?>">
            <div class="row collapse active-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-3x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white"><span class="phase">Phase </span>1c</h4>
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
        <div class="small-24 medium-6 columns end">
          <section class="chevron <?php echo $phase_2; ?>">
            <div class="row collapse active-marker">
              <div class="columns center small-centered marker">
                <i class="fas fa-map-marker-alt fa-3x" aria-hidden="true"></i>
              </div>
            </div>
            <header class="bg-dark-gray">
              <div class="valign process-label left-arrow-indent right-arrow">
                <div class="valign-cell">
                  <h4 class="mbn h5 white"><span class="phase">Phase </span>2</h4>
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

add_action( 'init', 'register_covid_phases' );

function register_covid_phases(){
  add_shortcode( 'covid_phases', 'covid_phases' );
}
