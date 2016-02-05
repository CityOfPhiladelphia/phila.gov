<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package phila-gov
 */
?>
    <?php
      if ( ! is_front_page() ): ?>
      <div class="row">
        <div class="small-24 columns">
          <div class="panel center">
            <?php echo 'Can\'t find what you are looking for? We\'re still moving content. <a href="';
                  echo get_template_part( 'partials/content', 'feedback-url' );
                  echo '&iHave=This%20website&whatHappened=I%20couldn%27t%20find%20what%20I%20was%20looking%20for"> Let us know what you are trying to find</a>.'; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      </div><!-- #content -->
 </div><!-- #page -->

<footer data-swiftype-index='false' id="colophon" class="site-footer" role="contentinfo">
  <section class="fat">
    <div class="row">
      <div class="large-8 columns">
        <h1>Government</h1>
        <nav class="government">
          <ul>
            <li><a href="http://alpha.phila.gov"><?php util_echo_website_url() ;?></a></li>
            <li><a href="/departments">Department Directory</a></li>
            <li><a href="http://www.phila.gov/mayor">Mayor's Office</a></li>
            <li><a href="http://iframe.publicstuff.com/#?client_id=242">Report an Issue / 311</a></li>
            <li><a href="http://cityofphiladelphia.wordpress.com/">News</a></li>
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
                  'hide_empty'=> false
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
    <div class="site-info large-6 columns">
      <a href="<?php get_template_part( 'partials/content', 'feedback-url' ); ?>"><?php printf( __( 'Provide Feedback', 'phila-gov' ) ); ?></a>
    </div><!-- .site-info -->
    <nav class="large-12 columns">
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
