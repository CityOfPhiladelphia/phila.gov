<?php
/**
 * The template for displaying the front page.
 *
 * @package phila-gov
 */

get_header(); 

$mobile_homepage_image = rwmb_meta( 'homepage_mobile', array( 'object_type' => 'setting' ), 'phila_settings' );
$desktop_homepage_image = rwmb_meta( 'homepage_desktop', array( 'object_type' => 'setting' ), 'phila_settings' );
?>

<div class="site-main home">
  <main>
    <div class="hero-content" style="background-image: url('<?php echo $desktop_homepage_image; ?>');">
      <img class="show-for-small-only" src="<?php echo $mobile_homepage_image; ?>" alt="">
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

        <div class="grid-x common-requests">
          <div class="small-24 medium-15 large-13 small-centered cell overlap">
            <div class="grid-x collapse call-to-action bg-white pvs pls equal-height">
              <div class="small-24 medium-auto cell">
                  <a href="https://www.phila.gov/programs/coronavirus-disease-2019-covid-19/" class="mrs mbs equal" onclick="dataLayer.push({
                        'event': 'GAEvent',
                        'eventCategory': 'Service Button',
                        'eventAction': 'Covid information',
                        'eventLabel': 'www.phila.gov'
                      });
                    ">
                    <span class="accessible">Covid-19 updates</span>
                    <div class="phs pvm cta-block clearfix" aria-hidden="true">
                      <div class="valign">
                        <div class="valign-cell">
                          <i class="fas fa-virus fa-4x"></i>
                        </div>
                      </div>
                      <div>
                        <p class="h6">COVID-19<span class="break-before-mu"> updates</span></p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="small-12 medium-auto cell">
                  <a href="https://www.phila.gov/trashday" class="mrs mbs equal" onclick="dataLayer.push({
                        'event': 'GAEvent',
                        'eventCategory': 'Service Button',
                        'eventAction': 'Find trash day',
                        'eventLabel': 'www.phila.gov'
                      });
                    ">
                    <span class="accessible">Find trash day</span>
                    <div class="phs pvm cta-block clearfix" aria-hidden="true">
                      <div class="valign">
                        <div class="valign-cell">
                          <i class="fas fa-trash-alt fa-4x"></i>
                        </div>
                      </div>
                      <div>
                        <p class="h6">Find<span class="break-before-mu"> trash day</span></p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="small-12 medium-auto cell">
                  <a href="https://epay.phila.gov/paymentcenter/accountlookup/"  class="mrs mbs equal"
                  onclick="dataLayer.push({
                        'event': 'GAEvent',
                        'eventCategory': 'Service Button',
                        'eventAction': 'Pay a bill',
                        'eventLabel': 'www.phila.gov'
                      });
                    ">
                    <span class="accessible">Pay a bill</span>
                    <div class="phs pvm cta-block clearfix"  aria-hidden="true">
                      <div class="valign">
                        <div class="valign-cell">
                          <i class="fal fa-credit-card fa-4x"></i>
                        </div>
                      </div>
                      <div>
                        <p class="h6">Pay<span class="break-before-mu"> a bill</span></p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="small-12 medium-auto cell">
                  <a href="/jobs/" class="mrs mbs equal"
                  onclick="dataLayer.push({
                        'event': 'GAEvent',
                        'eventCategory': 'Service Button',
                        'eventAction': 'Explore City jobs',
                        'eventLabel': 'www.phila.gov'
                      });
                    ">
                  <span class="accessible">Explore City jobs</span>

                    <div class="phs pvm cta-block clearfix" aria-hidden="true">
                      <div class="valign ">
                        <div class="valign-cell">
                          <i class="fas fa-briefcase fa-4x"></i>
                        </div>
                      </div>
                      <div>
                        <p class="h6">Explore<span class="break-before-mu"> City jobs</span></p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="small-12 medium-auto cell">
                  <a href="http://property.phila.gov/" class="mrs mbs equal"
                  onclick="dataLayer.push({
                        'event': 'GAEvent',
                        'eventCategory': 'Service Button',
                        'eventAction': 'Search for a property',
                        'eventLabel': 'www.phila.gov'
                      });
                    ">
                    <span class="accessible">Search for a property</span>
                    <div class="phs pvm cta-block clearfix" aria-hidden="true">
                      <div class="valign ">
                        <div class="valign-cell">
                          <i class="fas fa-home fa-4x"></i>
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
    <?php $service_args = array( 'post_type' => 'service_updates', 'category_name' => 'homepage', 'posts_per_page' => -1  ); ?>
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
      <div class="grid-x pvs">
        <div class="grid-container">
          <div class="grid-x grid-margin-x">
            <div class="cell medium-12">
              <div class="card hover-fade">
                <a href="/parks-rec-finder/" class="hover-fade">
                  <?php $image = rwmb_meta('phila_v2_photo_callout_block__photo', array('size' => 'medium', 'limit' => 1), $post = '27984')[0]['url']; ?>
                  <img src="<?php echo $image ?>" alt="">
                  <?php wp_reset_query(); ?>
                  <div class="card-description bg-ghost-gray phl pvm">
                    <h3>Parks & Recreation Finder</h3>
                    <p>Use our app to search for activities, parks, rec centers, and more.</p>
                  </div>
                </a>
              </div>
            </div>
            <div class="cell medium-12 grid-x">
              <div class="card card-fixed-height cell shrink align-self-top full hover-fade underline">
                <a href="https://contracts.phila.gov" class="hover-fade">
                  <div class="grid-x">
                    <div class="cell shrink align-self-middle pas">
                      <i class="far fa-copy fa-4x fa-fw"></i>
                    </div>
                    <div class="cell auto align-self-middle pvm">
                      <div class="card-description phl">
                        <h3>Contracts Hub</h3>
                        <p>Find contract opportunities and vendor information for your business.</p>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="card card-fixed-height cell shrink align-self-middle full hover-fade underline">
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

  </main><!-- #main -->
</div><!-- .site-main .home -->

<?php get_footer(); ?>