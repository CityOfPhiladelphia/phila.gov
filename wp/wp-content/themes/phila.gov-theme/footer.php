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
    $current_post_type =  get_post_type( $post->ID );
    if ( is_front_page() || $current_post_type == 'department_page' ):  ?>
    <?php else : ?>
      <div class="row">
        <div class="small-24 columns">
          <div class="panel center">
            <?php echo phila_still_migrating_content(); ?>
        </div>
      </div>
      <?php endif; ?>

      </div><!-- #content -->
 </div><!-- #page -->

<footer data-swiftype-index='false' id="colophon" class="site-footer" role="contentinfo">
  <?php
    if (isset($_POST)): ?>
      <?php
        if($current_post_type == 'department_page'): ?>
        <section class="contact">
            <?php echo phila_get_dept_contact_blocks(); ?>
          <?php get_template_part( 'partials/content', 'modified' ) ?>
        </section>
      <?php endif; ?>
    <?php endif; ?>
  <section class="fat">
    <div class="row">
      <div class="large-8 columns">
        <h1>Government</h1>
        <nav class="government">
          <ul>
            <li><a href="http://alpha.phila.gov"><?php phila_util_echo_website_url() ;?></a></li>
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
    <div class="site-info large-16 columns">
      <?php printf('This site is a work-in-progress that will change as we add content. Please ');?> <a style="text-transform:uppercase; font-weight:bold;" href="<?php echo phila_util_echo_feedback_url(); ?>"><?php printf( __( 'notify us of errors.', 'phila-gov' ) ); ?></a>
    </div><!-- .site-info -->
    <nav class="large-8 columns">
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
