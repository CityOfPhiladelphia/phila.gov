<?php
/**
 * The template for displaying the front page.
 *
 * @package phila-gov
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
      <div class="home-top">
        <div class="row">
          <section id="welcome" class="medium-16 columns">
                <div class="home-search">
                  <header>
                      <h1>What can we help you find?</h1>
                  </header>
                      <?php get_search_form(); ?>
                  </div>
                  <div id="popular" class="row call-to-action">
                      <div class="small-6 columns">
                          <a href="http://iframe.publicstuff.com/#?client_id=242" target="_blank">
                              <span class="fa-stack fa-3x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-stack-1x fa-inverse"><span class="h6">311</span></i>
                              </span>
                              <p>Report a Problem</p>
                              <span class="accessible"> Opens in new window</span>
                          </a>
                      </div>
                      <div class="small-6 columns">
                          <a href="https://secure.phila.gov/PaymentCenter/AccountLookup/" target="_blank">
                             <span class="fa-stack fa-3x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-credit-card fa-stack-1x fa-inverse"></i>
                              </span>
                              <p>Pay a bill<p>
                              <span class="accessible"> Opens in new window</span>
                          </a>
                      </div>
                        <div class="small-6 columns">
                          <a href="http://www.phila.gov/personnel//announce/current/index.html" target="_blank">
                              <span class="fa-stack fa-3x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-briefcase fa-stack-1x fa-inverse"></i>
                              </span>
                             <p>Find a Job</p>
                              <span class="accessible"> Opens in new window</span>
                          </a>
                      </div>
                      <div class="small-6 columns">
                          <a href="/property" target="_blank">
                            <span class="fa-stack fa-3x">
                              <i class="fa fa-circle fa-stack-2x"></i>
                              <i class="fa fa-home fa-stack-1x fa-inverse"></i>
                            </span>
                            <p>Property Search</p>
                              <span class="accessible"> Opens in new window</span>
                          </a>
                    </div>
                  </div><!--#popular -->
            </section>
              <div class="medium-8 columns">
                <section id="services">
                <?php
                /* temp top-level topics list w/ descriptions */
                   $args = array(
                        'orderby' => 'name',
                        'fields'=> 'all',
                        'parent' => 0,
                        'hide_empty'=> true
                   );
                  $terms = get_terms( 'topics', $args );
                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                         echo '<ul>';
                         foreach ( $terms as $term ) {
                             echo '<li><a href="/browse/' . $term->slug .  '">' . $term->name . '</a></li>';
                         }
                         echo '</ul>';
                        }

                        ?>
                        <span class="small-text">More topics coming soon</span>
                  </section>
            </div>
          </div><!--.row -->
    </div>
    <div class="home-news">
      <div class="row">
        <section id="news">
            <?php
                $args = array(
                    'post_type' => array ('news_post'),
                    'posts_per_page'    => 3,
                    'meta_key'          => 'phila_show_on_home',
                    //only show if "yes" is selected
                    'meta_value'     => '1'
                );
                $counter = 0;
                $news_query = new WP_Query($args);
                if ( $news_query->have_posts() ) : while ( $news_query->have_posts() ) : $news_query->the_post(); ?>

                <div class="small-24 medium-8 columns">
                    <div class="story">
                        <?php get_home_news(); ?>
                    </div>
                </div>
            <?php
                $counter++;
                if ($counter === 1 || $counter === 2){
                 //echo '<div class="small-1 columns"></div>';
                }
            ?>
                <?php endwhile; ?>

                <?php else : ?>
                    <div class="alert">No recent news.</div>
                <?php endif; ?>

        </section><!--#news-->
        </div><!-- .row -->
    </div><!-- .home-news -->

    <section id="active" class="row">
      <div class="small-24 large-17 columns related">
        <div class="row">
          <header>
              <h1>Most Active</h1>
          </header>
        <dl>
          <dt class="medium-24 large-8 columns"><a href="http://alpha.phila.gov/property" class="h3" target="_blank">Property Information<span class="accessible"> Opens in new window</span></a>
          <span>Property Assessment</span></dt>
          <dd class="medium-24 large-16 columns">Search and compare property data in the City of Philadelphia</dd>

          <dt class="medium-24 large-8 columns"><a href="http://www.phila.gov/revenue/realestatetax/" class="h3" target="_blank">Real Estate Tax<span class="accessible"> Opens in new window</span></a>
          <span>Revenue</span></dt>
          <dd class="medium-24 large-16 columns">Real Estate Tax bills are sent in December for the following year and payments are due March 31st.</dd>

          <dt class="medium-24 large-8 columns"><a href="http://www.phila.gov/zoningarchive/" class="h3" target="_blank">Zoning Archive<span class="accessible"> Opens in new window</span></a>
          <span>L+I</span></dt>
          <dd class="medium-24 large-16 columns">Search and view all previous applications, approved uses and site drawings for a parcel of land.</dd>

          <dt class="medium-24 large-8 columns"><a href="http://www.phila.gov/prisons/Facilities/Pages/default.aspx" class="h3" target="_blank">Correctional Facilities<span class="accessible"> Opens in new window</span></a>
          <span>Prisons</span></dt>
          <dd class="medium-24 large-16 columns">Find facility history, visiting rules, and hours.</dd>

          <dt class="medium-24 large-8 columns"><a href="http://www.phila.gov/Revenue/individuals/Pages/default.aspx" class="h3" target="_blank">Individual Taxes<span class="accessible"> Opens in new window</span></a>
          <span>Revenue</span></dt>
          <dd class="medium-24 large-16 columns">Learn about taxes that individuals must remit and/or file in Philadelphia.</dd>
        </dl>
      </div>
    </div>
    <div class="small-24 large-7 columns links">
      <a class="button icon full" href="/departments">Department Directory<i class="fa fa-sitemap"></i></a>
      <a class="button icon full" href="/departments/mayor">Mayor's Office<i class="fa fa-university"></i></a>
      <a class="button icon full" href="/news">News<i class="fa fa-microphone"></i></a>
      <a class="button icon full" href="http://www.phila.gov/map" target="_blank">Maps<i class="fa fa-map-marker"></i><span class="accessible"> Opens in new window</span></a>
    </div>

    </section><!--#active-->
  </main><!-- #main -->
</div><!-- #primary -->`

<?php get_footer(); ?>
