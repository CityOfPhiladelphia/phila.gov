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
      <div class="row">
        <section class="medium-18 small-centered columns">
          <header>
            <h1><?php printf( __( 'What can we help you find?') ) ?></h1>
          </header>
          <?php get_search_form(); ?>
          <div class="row call-to-action">
            <div class="small-6 columns">
              <a href="http://iframe.publicstuff.com/#?client_id=242" target="_blank">
                <span class="fa-stack fa-3x" aria-hidden="true">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-stack-1x fa-inverse"><span class="h6">311</span></i>
                </span>
                <span class="show-for-sr">311</span>
                  <p>Report a Problem</p>
                <span class="accessible"> Opens in new window</span>
              </a>
              </div>
              <div class="small-6 columns">
                <a href="https://secure.phila.gov/PaymentCenter/AccountLookup/" target="_blank">
                 <span class="fa-stack fa-3x" aria-hidden="true">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-credit-card fa-stack-1x fa-inverse"></i>
                  </span>
                  <p>Pay a Bill</p>
                  <span class="accessible"> Opens in new window</span>
                </a>
              </div>
              <div class="small-6 columns">
                <a href="http://www.phila.gov/personnel/JobOpps.html" target="_blank">
                  <span class="fa-stack fa-3x" aria-hidden="true">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-briefcase fa-stack-1x fa-inverse"></i>
                  </span>
                  <p>Find a Job</p>
                  <span class="accessible"> Opens in new window</span>
                </a>
              </div>
              <div class="small-6 columns">
                <a href="/property">
                  <span class="fa-stack fa-3x" aria-hidden="true">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-home fa-stack-1x fa-inverse"></i>
                  </span>
                  <p>Property Search</p>
                </a>
            </div>
          </div><!--#popular -->
          <div class="row expanded topic-main-nav">
          <?php
             $args = array(
              'orderby' => 'name',
              'fields'=> 'all',
              'parent' => 0,
              'hide_empty'=> true
             );
            $terms = get_terms( 'topics', $args );
              if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                foreach ( $terms as $term ) {
                  echo '<a href="/browse/' . $term->slug .  '">' . $term->name . '</a>';
                 }
              }
            ?>
          </div>
        </section>
      </div>
    </div>
    <div id="site-nav" class="row expanded">
      <div class="small-24 columns links">
        <nav>
          <ul class="menu">
            <li><a href="/departments"><i class="fa fa-sitemap"  aria-hidden="true"></i> <?php printf( __('City Government Directory', 'phila-gov') ); ?></a></li>
            <li><a href="/departments/mayor"><i class="fa fa-university"  aria-hidden="true"></i> <?php printf( __('Office of the Mayor', 'phila-gov') ); ?></a></li>
            <li><a href="http://www.phila.gov/map" class="external"><i class="fa fa-map-marker"  aria-hidden="true"></i> <?php printf( __('Maps', 'phila-gov') ); ?></a></li>
          </ul>
        </nav>
      </div>
    </div>
    <div class="news-row row expanded">
      <div class="columns">
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

        </div><!-- .row -->
      </div><!-- .home-news -->
    </div>
    <section id="active">
      <div class="row expanded">
        <div class="columns">
          <div class="row">
            <div class="small-24 large-24 columns">
              <header>
                <h1 class="contrast">Most Active</h1>
              </header>
            </div>
          </div>
          <div class="row">
            <div class="medium-24 large-6 columns">
              <div class="mlm-mu mrm-mu border-right">
                <a href="http://www.phila.gov/revenue/realestatetax/" class="h3" target="_blank">Real Estate Tax<span class="accessible"> Opens in new window</span></a><br>
                <span>Revenue</span>
                </div>
            </div>
            <div class="medium-24 large-18 columns">
              <div class="mam-mu">Real Estate Tax bills are sent in December for the following year and payments are due March 31st.</div>
            </div>
            <div class="medium-24 large-24 columns">
              <hr>
            </div>
          </div>
          <div class="row">
            <div class="medium-24 large-6 columns">
              <div class="mlm-mu mrm-mu border-right">
                <a href="http://www.phila.gov/zoningarchive/" class="h3" target="_blank">Zoning Archive<span class="accessible"> Opens in new window</span></a><br>
                <span>Licenses and Inspections <abbr>(L+I)</abbr></span>
                </div>
              </div>
              <div class="medium-24 large-18 columns">
                <div class="mam-mu">Search and view all previous applications, approved uses and site drawings for a parcel of land.</div>
              </div>
              <div class="medium-24 large-24 columns">
                <hr>
              </div>
          </div>
          <div class="row">
            <div class="medium-24 large-6 columns">
              <div class="mlm-mu mrm-mu border-right">
                <a href="http://www.phila.gov/prisons/Facilities/Pages/default.aspx" class="h3" target="_blank">Correctional Facilities<span class="accessible"> Opens in new window</span></a><br>
              <span>Prisons</span>
              </div>
            </div>
            <div class="medium-24 large-18 columns">
              <div class="mam-mu">Find facility history, visiting rules, and hours.</div>
            </div>
            <div class="medium-24 large-24 columns">
              <hr>
            </div>
          </div>
          <div class="row">
            <div class="medium-24 large-6 columns">
              <div class="mlm-mu mrm-mu border-right">
                <a href="http://www.phila.gov/Revenue/individuals/Pages/default.aspx" class="h3" target="_blank">Individual Taxes<span class="accessible"> Opens in new window</span></a><br>
                <span>Revenue</span>
              </div>
            </div>
            <div class="medium-24 large-18 columns">
              <div class="mam-mu">Learn about taxes that individuals must remit and/or file in Philadelphia.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section><!--#active-->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
