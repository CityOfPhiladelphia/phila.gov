<?php
/**
 * The template for displaying the front page.
 *
 * @package phila-gov
 */

get_header(); ?>

<div class="site-main home">
  <main>
    <div class="hero-content">
      <img class="show-for-small-only" src="<?php echo get_bloginfo('url'); ?>/wp-content/themes/phila.gov-theme/img/beta-homepage-mobile.jpg" alt="">
      <div class="hero-wrap">
        <div class="photo-credit small-text">
        </div>
        <div class="mask" style=""></div>
        <div class="row expanded ptl pbs pvxl-mu">
          <div class="medium-14 small-centered columns welcome-message">
            <div class="small-text"><i>welcome to</i></div>
            <div class="h1">phila.gov</div>
          </div>
        </div>
        <div class="row common-requests">
          <div class="small-24 medium-15 large-13 small-centered columns overlap">
            <div class="row collapse call-to-action bg-white pvs pls equal-height">
              <div class="small-12 medium-6 columns">
                <a href="/trashday" class="mrs mbs equal">
                  <div class="phs pvm cta-block clearfix">
                    <div class="valign">
                      <div class="valign-cell">
                        <i class="fa fa-trash fa-4x"></i>
                      </div>
                    </div>
                    <div>
                      <p class="h6">Find<span class="break-before-mu"> trash day</span></p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="small-12 medium-6 columns">
                <a href="https://secure.phila.gov/PaymentCenter/AccountLookup/" class="mrs mbs equal">
                  <div class="phs pvm cta-block clearfix">
                    <div class="valign ">
                      <div class="valign-cell">
                        <i class="fa fa-credit-card fa-4x"></i>
                      </div>
                    </div>
                    <div>
                      <p class="h6">Pay<span class="break-before-mu"> a bill</span></p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="small-12 medium-6 columns">
                <a href="http://www.phila.gov/personnel/JobOpps.html" class="mrs mbs equal">
                  <div class="phs pvm cta-block clearfix">
                    <div class="valign ">
                      <div class="valign-cell">
                        <i class="fa fa-briefcase fa-4x"></i>
                      </div>
                    </div>
                    <div>
                      <p class="h6">Explore<span class="break-before-mu"> City jobs</span></p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="small-12 medium-6 columns">
                <a href="http://property.phila.gov/" class="mrs mbs equal">
                  <div class="phs pvm cta-block clearfix">
                    <div class="valign ">
                      <div class="valign-cell">
                        <i class="fa fa-home fa-4x"></i>
                      </div>
                    </div>
                    <div>
                      <p class="h6">Search for a<span class="break-before-mu"> property</span></p>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- End Common Requests -->
      </div>
    </div>
    <!-- End Hero Content -->
    <div class="row">
      <div class="columns">
        <h2>Service updates</h2>
      </div>
    </div>
    <?php $service_args = array( 'post_type' => 'service_updates', 'category_name' => 'homepage' ); ?>
    <div class="mbxl">
      <?php include( locate_template( 'partials/content-service-updates.php' ) ); ?>
    </div>

    <?php $home_filter = array(
      'key' => 'show_on_home',
      'value'   => '1' ,
      'compare' => '='
    );?>
    <?php include( locate_template( 'partials/posts/announcements-grid.php' ) ); ?>

    <section class="the-latest mvm">
      <div class="row">
        <div class="columns">
          <h2>The latest news + events</h2>
        </div>
      </div>
      <?php include( locate_template( 'partials/posts/featured-grid.php' ) ); ?>


      <?php $the_latest = array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'templates/the-latest.php',
        'field' => 'ids'
      );
      $latest_desc = get_posts( $the_latest );
      $desc = rwmb_meta('phila_meta_desc', $args = null, $latest_desc[0]->ID );
      ?>
      <div class="row mvxl">
        <div class="columns panel">
          <div class="row equal-height">
            <div class="small-24 medium-16 columns valign equal">
              <div class="valign-cell">
                <h3 class="mbn">More from the City of Philadelphia</h3>
                <p class="mts"><?php echo $desc ?></p>
              </div>
            </div>
            <div class="small-24 medium-8 columns valign equal center">
              <a href="/the-latest" class="button full mts">Go to The latest</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="neighborhood-resources">
      <div class="row expanded ptm">
        <div class="columns">
          <div class="row">
            <div class="columns">
              <h2 class="contrast">Neighborhood resources</h2>
            </div>
          </div>
        </div>
      </div>
      <div class="row expanded resource-row">
        <div class="row phm phl-mu pvm-mu equal-height">
          <div class="small-12 medium-6 columns mtxl-mu mbm-mu sidewalk bdr-right nbdr-right-mu bdr-bottom-sm">
            <a href="http://www.freelibrary.org/" class="action-panel">
              <div class="panel equal" data-equalizer-watch="" >
                <header class="">
                  <div class="icon">
                    <span><i class="fa fa-book fa-5x" aria-hidden="true"></i></span>
                  </div>
                  <span class="external">Free libraries</span>
                </header>
              </div>
            </a>
          </div>
          <!-- Free Libraries -->
          <div class="small-12 medium-6 columns mtxl-mu mbm-mu sidewalk bdr-bottom-sm">
            <a href="https://beta.phila.gov/parks-rec-finder/#/locations/parks" class="action-panel">
              <div class="panel equal" data-equalizer-watch="" >
                <header class="">
                  <div class="icon">
                    <span><i class="fa fa-tree fa-5x" aria-hidden="true"></i></span>
                  </div>
                  <span class="external">Parks</span>
                </header>
              </div>
            </a>
          </div>
          <!-- Parks -->
          <div class="small-12 medium-6 columns mtxl-mu mbm-mu sidewalk bdr-right nbdr-right-mu  bdr-bottom-sm">
            <a href="https://beta.phila.gov/parks-rec-finder/#/locations/recreation-centers" class="action-panel">
              <div class="panel equal" data-equalizer-watch="" >
                <header class="">
                  <div class="icon">
                    <span><i class="fa fa-paint-brush fa-5x" aria-hidden="true"></i></span>
                  </div>
                  <span class="external">Recreation centers</span>
                </header>
              </div>
            </a>
          </div>
          <!-- Recreation Centers -->
          <div class="small-12 medium-6 columns mtxl-mu mbm-mu sidewalk bdr-bottom-sm">
            <a href="https://www.phillykeyspots.org/keyspot-finder" class="action-panel">
              <div class="panel equal" data-equalizer-watch="" >
                <header class="">
                  <div class="icon">
                    <span><i class="fa fa-wifi fa-5x" aria-hidden="true"></i></span>
                  </div>
                  <span class="external">Free Internet access</span>
                </header>
              </div>
            </a>
          </div>
          <!-- Free Internet Access -->
          <div class="small-12 medium-6 columns mtm-mu mbxl-mu sidewalk bdr-right nbdr-right-mu  bdr-bottom-sm">
            <a href="http://phl.maps.arcgis.com/apps/View/index.html?appid=a9bc69013f76464ca21ad6bb00167c90" class="action-panel">
              <div class="panel equal" data-equalizer-watch="" >
                <header class="">
                  <div class="icon">
                    <span><i class="fa fa-soccer-ball-o fa-5x" aria-hidden="true"></i></span>
                  </div>
                  <span class="external">Playgrounds</span>
                </header>
              </div>
            </a>
          </div>
          <!-- Playgrounds -->
          <div class="small-12 medium-6 columns mtm-mu mbxl-mu sidewalk bdr-bottom-sm">
            <a href="/city-health-centers/" class="action-panel">
              <div class="panel equal" data-equalizer-watch="" >
                <header class="">
                  <div class="icon">
                    <span><i class="fa fa-heartbeat fa-5x" aria-hidden="true"></i></span>
                  </div>
                  <span class="">Health centers</span>
                </header>
              </div>
            </a>
          </div>
          <!-- Health Centers -->
          <div class="small-12 medium-6 columns mtm-mu mbxl-mu sidewalk bdr-right nbdr-right-mu">
            <a href="http://www.phila.gov/fire/fac_and_equip/facil_firehouses.html" class="action-panel">
              <div class="panel equal" data-equalizer-watch="" >
                <header class="">
                  <div class="icon">
                    <span><i class="fa fa-fire-extinguisher fa-5x" aria-hidden="true"></i></span>
                  </div>
                  <span class="external">Fire stations</span>
                </header>
              </div>
            </a>
          </div>
          <!-- Fire Stations -->
          <div class="small-12 medium-6 columns mtm-mu mbxl-mu sidewalk">
            <a href="https://www.phillypolice.com/districts/" class="action-panel">
              <div class="panel equal" data-equalizer-watch="" >
                <header class="">
                  <div class="icon">
                    <span><i class="fa fa-shield fa-5x" aria-hidden="true"></i></span>
                  </div>
                  <span class="external">Police stations</span>
                </header>
              </div>
            </a>
          </div>
          <!-- Police Stations -->
        </div>
      </div>
      <div class="feedback phm phn-mu mvs mvn-mu">
        <div class="row expanded" data-toggle="feedback">
          <div class="column call-to-action pas center">
            <a href="#" class="no-link"><i class="fa fa-lightbulb-o" aria-hidden="true"></i><span class="break-before-sm"> What should we we add to the neighborhood resources section?</span>
            <span class="break-before-sm"> Tell us</span>.</a>
          </div>
        </div>
        <div class="feedback-form" data-type="feedback-form" style="display:none;">
          <div class="row expanded">
            <div class="medium-18 large-14 column small-centered mbm clearfix" data-type="form-wrapper" >
              <div id="form-container"></div>
            </div>
          </div>
        </div>
        <div class="row expanded" data-type="feedback-indicator">
          <div class="column center">
            <div class="arrow-wrapper">
              <div class="arrow"></div>
            </div>
          </div>
        </div>
        <div class="row expanded" data-toggle="feedback" data-type="feedback-footer" style="display:none;">
          <div class="column call-to-action center">
            <div class="pas"><a href="#" class="no-link"><i class="fa fa-close" aria-hidden="true"></i> Close</a></div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Neighborhood Resources -->

    <div class="row ptm pbxl expanded phila-redesign">
      <div class="columns">
        <section>
          <div class="row">
            <div class="columns">
              <h2 class="contrast">Redesigning phila.gov</h2>
            </div>
          </div>
          <div class="row equal-height">
            <div class="medium-10 columns bdr-right-mu equal about">
              <section>
                <h3>About the redesign</h3>
                <p>We’re in the process of creating a new site from the ground up—with simple, mobile-friendly designs, more intuitive organization, and clearer content.</p>

                <p>Throughout the redesign process, we’ve been collaborating with people like you to inform the direction and usability of the site. Please continue to send us your thoughts:</p>

                <ul class="list-style-none mln pln pvm">
                  <li class="mbm"><i class="fa fa-arrow-right fa-lg prs" aria-hidden="true"></i> Use the site’s <i class="fa fa-lightbulb-o fa-lg" aria-hidden="true"></i> feedback links to alert us to content errors and design feedback.</li>
                </ul>

              </section>
            </div>
            <div class="show-for-medium medium-14 pll plm columns equal process">
              <section>
                <h3>Where are we in the redesign process?</h3>
                <p>For the past few years, we’ve been working closely with content creators, City colleagues, and the public to rewrite service information and to design features that better meet your needs.</p>
                <div class="row collapse">
                  <div class="medium-8 columns center small-centered ben-franklin-blue marker">
                    <i class="fa fa-map-marker fa-3x" aria-hidden="true"></i>
                  </div>
                </div>
                <div class="row collapse process-bar">
                  <div class="medium-4 columns">
                    <section>
                      <header class="bg-medium-gray">
                        <div class="valign process-label left-arrow-indent right-arrow">
                          <div class="valign-cell">
                            <h4 class="mbn h5 white">Alpha</h4>
                          </div>
                        </div>
                      </header>
                    </section>
                  </div>
                  <div class="medium-4 columns small-text">
                    <section>
                      <header class="bg-medium-gray">
                        <div class="valign process-label left-arrow-indent right-arrow">
                          <div class="valign-cell">
                            <h4 class="mbn h5 white">Beta</h4>
                          </div>
                        </div>
                      </header>
                    </section>
                  </div>
                  <div class="medium-8 columns small-text">
                    <section>
                      <header class="bg-ben-franklin-blue">
                        <div class="valign process-label bg-ben-franklin-blue left-arrow-indent right-arrow-ben-franklin-blue right-arrow">
                          <div class="valign-cell">
                            <h4 class="mbn h5 white">phila.gov</h4>
                          </div>
                        </div>
                      </header>
                      <div class="description current">
                        <span class="phs small-text">The homepage of beta.phila.gov became the official City homepage in 2018 and the content of beta.phila.gov and phila.gov were merged.</span>
                      </div>
                    </section>
                  </div>
                  <div class="medium-8 columns end">
                    <section>
                      <header>
                        <div class="valign process-label bg-medium-gray left-arrow-indent right-arrow">
                          <div class="valign-cell">
                            <h4 class="mbn h5 white">Migration</h4>
                          </div>
                        </div>
                      </header>
                      <div class="description">
                        <span class="phs small-text">We're continuing to bring content onto this new platform. With your help, we’ll continue to improve it.</span>
                      </div>
                    </section>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </section>
      </div>
    </div>
    <!-- End Redesigning Phila.gov -->

  </main><!-- #main -->
</div><!-- .site-main .home -->

<?php get_footer(); ?>
