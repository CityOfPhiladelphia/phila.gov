<?php
/**
 * The template for displaying the front page.
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main home">
    <div class="hero-content">
      <div class="hero-wrap">
        <div class="mask" style=""></div>
        <div class="row expanded ptl pbs pvxl-mu">
          <div class="medium-14 small-centered columns beta-message">
            <span class="h1 break-after">Beta.phila.gov</span> <span class="h2 sub-title">is a work-in-progress.</span>
            <p class="mvm">We’re looking for your input so we can design a website that better meets your needs. Send us your ideas through the site’s <span class="nowrap"><i class="fa fa-lightbulb-o fa-lg"></i> feedback links.</span></p>
          </div>
        </div>
        <!-- End Beta.phila.gov Message -->
        <div class="row common-requests">
          <div class="small-24 medium-15 large-13 small-centered columns overlap">
            <div class="row collapse call-to-action bg-white pvs pls equal-height">
              <div class="small-12 medium-6 columns">
                <a href="" class="mrs mbs equal">
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
                <a href="/property" class="mrs mbs equal">
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

    <?php $args = array( 'post_type' => 'service_updates' ); ?>
    <?php $service_updates_loop = new WP_Query( $args ); ?>
    <?php include( locate_template( 'partials/content-service-updates.php' ) ); ?>
    <?php wp_reset_query();?>

    <div class="news-row row ptm">
      <div class="columns">
        <section>
          <div class="row">
            <div class="columns">
              <h2 class="contrast"><?php printf( __( 'News' ) ) ?> </h2>
            </div>
          </div>
          <div class="row equal-height">
          <?php
            $args = array(
              'post_type' => array ('news_post'),
              'posts_per_page'    => 3,
              'meta_key'          => 'phila_show_on_home',
              //only show if "yes" is selected
              'meta_value'     => '1'
            );
            $news_query = new WP_Query( $args );
            if ( $news_query->have_posts() ) : while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
              <div class="small-24 medium-8 columns">
                <?php phila_get_home_news(); ?>
              </div>
            <?php endwhile; ?>
            <?php else : ?>
              <div class="alert">No recent news.</div>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </div>
    <!-- End News -->

    <section class="neighborhood-resources">
      <div class="row expanded ptm">
        <div class="columns">
          <div class="row">
            <div class="columns">
              <h2 class=contrast>Neighborhood resources</h2>
            </div>
          </div>
        </div>
      </div>
      <div class="row expanded resource-row">
        <div class="row phm phl-mu equal-height">
          <div class="small-12 medium-6 columns mvl-mu sidewalk bdr-right nbdr-right-mu bdr-bottom-sm">
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
          <div class="small-12 medium-6 columns mvl-mu sidewalk bdr-bottom-sm">
            <a href="http://gsg.phila.gov/map#id=e7d139e404dd4fdaac4ae0bbaf637f79" class="action-panel">
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
          <div class="small-12 medium-6 columns mvl-mu sidewalk bdr-right nbdr-right-mu  bdr-bottom-sm">
            <a href="http://www.phila.gov/parksandrecreation/findafacility/" class="action-panel">
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
          <div class="small-12 medium-6 columns mvl-mu sidewalk bdr-bottom-sm">
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
          <div class="small-12 medium-6 columns mvl-mu sidewalk bdr-right nbdr-right-mu  bdr-bottom-sm">
            <a href="http://gsg.phila.gov/map#id=aa5f6f59d35c45e9bc089b400694f43a" class="action-panel">
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
          <div class="small-12 medium-6 columns mvl-mu sidewalk bdr-bottom-sm">
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
          <div class="small-12 medium-6 columns mvl-mu sidewalk bdr-right nbdr-right-mu">
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
          <div class="small-12 medium-6 columns mvl-mu sidewalk">
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
            <i class="fa fa-lightbulb-o fa-lg" aria-hidden="true"></i> What should we add to this section? Tell us.
          </div>
        </div>
        <div class="feedback-form" data-type="feedback-form" style="display:none;">
          <div class="row expanded">
            <div class="medium-18 large-14 column small-centered mbm" data-type="form-wrapper" >
              <?php // TODO: insert the form on click ?>
              <script type="text/javascript" src="https://form.jotform.com/jsform/62516788470970"></script>
            </div>
          </div>
        </div>
        <div class="row expanded" data-type="feedback-indicator">
          <div class="column center">
            <div class="arrow"></div>
          </div>
        </div>
        <div class="row expanded" data-toggle="feedback" data-type="feedback-footer" style="display:none;">
          <div class="column call-to-action center" >
            <div class="pas"><i class="fa fa-close" aria-hidden="true"></i> Close</div>
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
                <h3>About the Redesign</h3>
                <p>We understand that the City of Philadelphia’s current website, phila.gov, isn’t easy to use. So we’re in the process of creating a new site from the ground up—with simple, mobile-friendly designs, more intuitive organization, and clearer content.</p>

                <p>Throughout the redesign process, we’ve been collaborating with people like you to inform the direction and usability of the site. Please continue to send us your thoughts:</p>

                <ol>
                  <li> Use the site’s <i class="fa fa-lightbulb-o fa-lg" aria-hidden="true"></i> feedback links to alert us to content errors and design feedback.</li>
                  <li><a href="#">Sign up to be a beta.phila.gov tester</a>. We’ll contact you for specific feedback on features as we design them.</li>
                </ol>
              </section>
            </div>
            <div class="show-for-medium medium-14 pll plm columns equal process">
              <section>
                <h3>Where are we in the redesign process?</h3>
                <p>For the past couple of years, we’ve been working closely with content creators, City colleagues, and the public to rewrite service information and to design features that better meet your needs.</p>
                <div class="row collapse">
                  <div class="medium-8 columns center small-centered ben-franklin-blue marker">
                    <i class="fa fa-map-marker fa-3x" aria-hidden="true"></i>
                  </div>
                </div>
                <div class="row collapse process-bar">
                  <div class="medium-8 columns">
                    <section>
                      <header class="bg-medium-gray">
                        <div class="valign process-label left-arrow-indent left-arrow-white">
                          <div class="valign-cell">
                            <h4 class="mbn h5 white">Alpha</h4>
                          </div>
                        </div>
                      </header>
                      <div class="description">
                        <span class="phs small-text">Alpha.phila.gov went live in late 2014. The alpha prototype was revised throughout 2015.</span>
                      </div>
                    </section>
                  </div>
                  <div class="medium-8 columns small-text">
                    <section>
                      <header class="bg-ben-franklin-blue">
                        <div class="valign process-label bg-ben-franklin-blue left-arrow-indent left-arrow-medium-gray">
                          <div class="valign-cell">
                            <h4 class="mbn h5 white">Beta</h4>
                          </div>
                        </div>
                      </header>
                      <div class="description current">
                        <span class="phs small-text">Versions of beta.phila.gov will roll out in 2016 and 2017.</span>
                      </div>
                    </section>
                  </div>
                  <div class="medium-8 columns end">
                    <section>
                      <header>
                        <div class="valign process-label bg-medium-gray left-arrow-indent left-arrow-ben-franklin-blue right-arrow">
                          <div class="valign-cell">
                            <h4 class="mbn h5 white">Launch</h4>
                          </div>
                        </div>
                      </header>
                      <div class="description">
                        <span class="phs small-text">Beta.phila.gov will become the City’s new website in 2017. With your help, we’ll continue to improve it after that.</span>
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
</div><!-- #primary -->

<?php get_footer(); ?>
