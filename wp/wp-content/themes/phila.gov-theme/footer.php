<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package phila-gov
 */
?>
    </div><!-- #content -->
 </div><!-- #page -->

<footer data-swiftype-index='false' id="colophon" class="site-footer">
  <section class="philly311">
    <div class="row">
      <div class="columns">
        <h2>Philly311</h2>
        <p><a href="/" class="external">311</a> provides direct access to City government information, services, and real-time service updates. Multiple languages are available. Call 3-1-1 or tweet @philly311 for a quick response.</p>
      </div>
    </div>
    <div class="row pal">
      <div class="small-24 medium-8 columns plm prxl">
        <h3>Interact with 311 online</h3>

        <a href="#/" class="button icon clearfix">
          <div class="valign">
            <i class="fa fa-bullhorn valign-cell"></i>
            <div class="button-label valign-cell">Report a problem</div>
          </div>
        </a>

        <a href="#/" class="button icon clearfix">
          <div class="valign">
            <i class="fa fa-question valign-cell"></i>
            <div class="button-label valign-cell">Ask a question</div>
          </div>
        </a>

        <a href="#/" class="button icon clearfix">
          <div class="valign">
            <i class="fa fa-check-square-o valign-cell"></i>
            <div class="button-label valign-cell">Track a request</div>
          </div>
        </a>

      </div>
      <div class="medium-16 columns trending-requests plxl">
        <h3>Trending Requests</h3>
          <ul class="columns-2">
            <li>
              <a href="#" >Sample textarea</a>
            </li>
            <li>
              <a href="#" >Sample textarea</a>
            </li>
            <li>
              <a href="#" >Sample textarea</a>
            </li>
            <li>
              <a href="#" >Sample textarea</a>
            </li>
            <li>
              <a href="#" >Sample textarea</a>
            </li>
            <li>
              <a href="#" >Sample textarea</a>
            </li>
            <li>
              <a href="#" >Sample textarea</a>
            </li>
            <li>
              <a href="#" >Sample textarea</a>
            </li>
          </ul>
      </div>
    </div>
  </section>
  <div class="contact">
    <?php echo phila_get_dept_contact_blocks(); ?>
    <?php get_template_part( 'partials/content', 'modified' ) ?>
  </div>
  <section class="fat">
    <div class="row">
      <div class="large-8 columns">
        <h1>Government</h1>
        <nav class="government">
          <ul>
            <li><a href="http://alpha.phila.gov"><?php phila_util_echo_website_url() ;?></a></li>
            <li><a href="/departments"><?php printf( __('City Government Directory', 'phila-gov') ); ?></a></li>
            <li><a href="/mayor"><?php printf( __('Mayor\'s Office', 'phila-gov') ); ?></a></li>
            <li><a href="http://iframe.publicstuff.com/#?client_id=242"><?php printf( __('Report an Issue / 311', 'phila-gov') ); ?></a></li>
            <li><a href="/news"><?php printf( __('News', 'phila-gov') ); ?></a></li>
          </ul>
        </nav>
      </div>
      <div class="large-16 columns">
        <h1>Browse alpha.phila.gov</h1>
        <nav class="browse-alpha">
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
                     echo '<ul class="columns-2">';
                     foreach ( $terms as $term ) {
                         echo '<li><a href="/browse/' . $term->slug .  '">' . $term->name . '</a>';
                     }
                     echo '</ul>';
                    }
                    ?>
              </nav>
        </div>
    </div><!-- row -->
  </section><!-- fat -->
  <div class="row classic">
    <nav class="columns center">
      <ul class="inline-list">
          <li><a href="/terms-of-use">Terms of use</a></li>
          <li><a href="http://www.phila.gov/privacy/pdfs/FinalCityOpenRecords.pdf">Right to know (pdf)</a></li>
          <li><a href="/privacypolicy">Privacy Policy</a></li>
      </ul>
    </nav>
  </div>
</footer><!-- #colophon -->

<script type="text/javascript">
  // For search.js
  var SWIFTYPE_ENGINE = '<?php echo SWIFTYPE_ENGINE?>';
</script>

<?php wp_footer(); ?>

</body>
</html>
