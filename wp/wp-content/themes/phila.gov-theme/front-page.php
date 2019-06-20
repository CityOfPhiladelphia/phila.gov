<?php
/**
 * The template for displaying the front page.
 *
 * @package phila-gov
 */

get_header(); ?>
<?php 
  $domain = parse_url($_SERVER['HTTP_HOST']);
?>

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
                <a href="https://www.phila.gov/trashday" class="mrs mbs equal" onclick="dataLayer.push({
                      'event': 'GAEvent',
                      'eventCategory': 'Service Button',
                      'eventAction': 'Find trash day',
                      'eventLabel': '<?php echo $domain['host'] ?>'
                    });
                  ">
                  <div class="phs pvm cta-block clearfix">
                    <div class="valign">
                      <div class="valign-cell">
                        <i class="fas fa-trash-alt fa-4x" aria-hidden="true"></i>
                      </div>
                    </div>
                    <div>
                      <p class="h6">Find<span class="break-before-mu"> trash day</span></p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="small-12 medium-6 columns">
                <a href="https://secure.phila.gov/PaymentCenter/AccountLookup/" class="mrs mbs equal"
                onclick="dataLayer.push({
                      'event': 'GAEvent',
                      'eventCategory': 'Service Button',
                      'eventAction': 'Pay a bill',
                      'eventLabel': '<?php echo $domain['host'] ?>'
                    });
                  ">
                  <div class="phs pvm cta-block clearfix">
                    <div class="valign">
                      <div class="valign-cell">
                        <i class="fal fa-credit-card fa-4x" aria-hidden="true"></i>
                      </div>
                    </div>
                    <div>
                      <p class="h6">Pay<span class="break-before-mu"> a bill</span></p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="small-12 medium-6 columns">
                <a href="/jobs/" class="mrs mbs equal"
                onclick="dataLayer.push({
                      'event': 'GAEvent',
                      'eventCategory': 'Service Button',
                      'eventAction': 'Explore City jobs',
                      'eventLabel': '<?php echo $domain['host'] ?>'
                    });
                  ">
                  <div class="phs pvm cta-block clearfix">
                    <div class="valign ">
                      <div class="valign-cell">
                        <i class="fas fa-briefcase fa-4x" aria-hidden="true"></i>
                      </div>
                    </div>
                    <div>
                      <p class="h6">Explore<span class="break-before-mu"> City jobs</span></p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="small-12 medium-6 columns">
                <a href="http://property.phila.gov/" class="mrs mbs equal"
                onclick="dataLayer.push({
                      'event': 'GAEvent',
                      'eventCategory': 'Service Button',
                      'eventAction': 'Search for a property',
                      'eventLabel': '<?php echo $domain['host'] ?>'
                    });
                  ">
                  <div class="phs pvm cta-block clearfix">
                    <div class="valign ">
                      <div class="valign-cell">
                        <i class="fas fa-home fa-4x" aria-hidden="true"></i>
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
      <?php include( locate_template( 'partials/global/cta-go-to-latest.php' ) ); ?>
    </section>

    <section class="common-resources mvl">
      <div class="grid-container grid-x">
        <h2>Common resources</h2>
      </div>
      <div class="grid-x bg-ghost-gray pvl">
        <div class="grid-container">
          <div class="grid-x grid-margin-x">
            <div class="cell medium-12">
              <div class="card hover-fade">
                <a href="/parks-rec-finder/" class="hover-fade">
                  <?php $image = rwmb_meta('phila_v2_photo_callout_block__photo', array('size' => 'medium', 'limit' => 1), $post = '27984')[0]['url']; ?>
                  <img src="<?php echo $image ?>" alt="">
                  <?php wp_reset_query(); ?>
                  <div class="card-description phl pvm">
                    <h3>Parks & Recreation Finder</h3>
                    <p>Use our app to search for activities, parks, rec centers, and more.</p>
                  </div>
                </a>
              </div>
            </div>
            <div class="cell medium-12 grid-x">
              <div class="card card-fixed-height cell shrink align-self-top full hover-fade">
                <a href="/contracts/pages/default.aspx" class="hover-fade">
                  <div class="grid-x">
                    <div class="cell shrink align-self-middle pas">
                      <i class="far fa-copy fa-4x fa-fw"></i>
                    </div>
                    <div class="cell auto align-self-middle pvm">
                      <div class="card-description phl">
                        <h3>Contracts</h3>
                        <p>Find, bid on, get alerts for contract opportunities with the City.</p>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="card card-fixed-height cell shrink align-self-middle full hover-fade">
                <a href="/departments/department-of-licenses-and-inspections/">
                  <div class="grid-x">
                    <div class="cell shrink align-self-middle pas">
                      <i class="fas fa-file-alt fa-4x  fa-fw"></i>
                    </div>
                    <div class="cell auto align-self-middle pvm">
                      <div class="card-description phl">
                        <h3>Licenses, inspections & permits </h3>
                        <p>Get a license, apply for a building permit, get a property certification.</p>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="card card-fixed-height cell shrink align-self-bottom full hover-fade">
                <a href="https://visitphilly.com">
                  <div class="grid-x">
                    <div class="cell shrink align-self-middle pas">
                      <i class="fas fa-users fa-4x fa-fw"></i>
                    </div>
                    <div class="cell auto align-self-middle pvm">
                      <div class="card-description phl">
                        <h3 class="external">Visit Philadelphia</h3>
                        <p>Visitphilly.com is the official visitor site for Greater Philadelphia.</p>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Common Resources -->

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
                <p>We’re in the process of creating a new website for Philadelphia from the ground up—with simple, mobile-friendly designs, more intuitive organization, and clear content.</p>

                <p>Throughout the redesign process, we’ve been collaborating with people like you to inform the direction and usability of the site. Please continue to send us your thoughts:
                  </p>
                <ul class="list-style-none mln pln pvm">
                  <li class="mbm"><i class="fas fa-arrow-right fa-lg prs" aria-hidden="true"></i> Use the site’s <i class="fal fa-lightbulb fa-lg" aria-hidden="true"></i> feedback links to alert us to content errors and design feedback.</li>
                </ul>
              </section>
            </div>
            <div class="show-for-medium medium-14 pll plm columns equal process">
              <section>
                <h3>Where are we in the redesign process?</h3>
                <p>For the past few years, we’ve been working closely with content creators, City colleagues, and the public to rewrite service information and to design features that better meet your needs.</p>
                <div class="row collapse">
                  <div class="medium-8 columns center small-centered ben-franklin-blue marker">
                    <i class="fas fa-map-marker-alt fa-3x" aria-hidden="true"></i>
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
                        <!-- use &period; so the wordpress-scraper doesn't find/replace the string -->
                        <span class="phs small-text">The homepage of beta&period;phila&period;gov became the official City homepage in 2018, and the content of beta&period;phila&period;gov and phila&period;gov were merged.</span>
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
                        <span class="phs small-text">We’re continuing to bring content onto this new platform. With your help we’ll continue to improve it.</span>
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
