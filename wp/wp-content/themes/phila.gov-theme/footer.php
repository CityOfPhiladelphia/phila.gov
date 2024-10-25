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
  <a href="#page" id="back-to-top"><i class="fas fa-arrow-up" aria-hidden="true"></i><br>Top</a>
<?php get_template_part( 'searchform' ); ?>
<?php get_template_part( 'partials/content', 'service-mega-menu' ); ?>

<footer data-swiftype-index='false' id="global-footer" class="site-footer">
  <?php get_template_part( 'partials/departments/v2/content', 'footer' ) ?>

  <?php get_template_part( 'partials/content', 'modified' ) ?>
  <?php if( !is_home() ) : ?>
    <?php get_template_part( 'partials/content', 'feedback' ); ?>
  <?php endif; ?>
  <?php echo phila_get_dept_contact_blocks(); ?>
  <div id="full-footer-start" class="phila-footer">
    <div class="row pvs ptl-mu phm equal-height">
      <section class="medium-8 columns phm pvm pvn-mu equal">
        <h2 class="mtn mbm">Elected officials</h2>
        <nav>
          <ul>
            <li class="pvxs"><a href="https://www.phila.gov/departments/mayor/">Mayor</a></li>
            <li class="pvxs"><a href="http://phlcouncil.com/" class="external">City Council</a></li>
            <li class="pvxs"><a href="http://www.courts.phila.gov/">Courts</a></li>
            <li class="pvxs"><a href="https://phillyda.org/" class="external">District Attorney</a></li>
            <li class="pvxs"><a href="https://controller.phila.gov/">City Controller</a></li>
            <li class="pvxs"><a href="https://phillysheriff.com/" class="external">Sheriff</a></li>
            <li class="pvxs"><a href="https://vote.phila.gov/	">City Commissioners</a></li>
            <li class="pvxs"><a href="https://www.phila.gov/departments/register-of-wills/">Register of Wills</a></li>
          </ul>
        </nav>
      </section>
      <section class="medium-8 columns phm pll-mu pvm pvn-mu equal">
        <h2 class="mtn mbm">Open government</h2>
        <nav>
          <ul>
            <li class="pvxs"><a href="https://codelibrary.amlegal.com/codes/philadelphia/latest/overview" class="external">Philadelphia Code &amp; Charter</a></li>
            <li class="pvxs"><a href="https://www.phila.gov/departments/department-of-records/">City records</a></li>
            <li class="pvxs"><a href="https://www.phila.gov/departments/department-of-records/proposed-regulations/#/">City agency regulations</a></li>
            <li class="pvxs"><a href="https://www.phila.gov/departments/mayor/executive-orders/">Executive orders</a></li>
            <li class="pvxs"><a href="https://www.phila.gov/programs/integrity-works/">Honesty in government</a></li>
            <li class="pvxs"><a href="https://vote.phila.gov/">Voting & elections</a></li>
            <li class="pvxs"><a href="https://www.phila.gov/programs/open-data-program/">Open data</a></li>
            <li class="pvxs"><a href="https://www.phila.gov/documents/city-of-philadelphia-organization-chart/">City organization chart</a></li>
          </ul>
        </nav>
      </section>
      <section class="medium-8 columns phm pll-mu pvm pvn-mu equal">
        <h2 class="mtn mbm">Explore Philadelphia</h2>
        <nav>
          <ul>
            <li class="pvxs">
              <a href="https://www.septa.org/" class="external">SEPTA</a><br></li>
              <li><a href="https://www.visitphilly.com/" class="external">Visit Philadelphia</a></li>
          </ul>
        </nav>
        <nav class="city-social mtl">
          <ul class="inline-list">
            <li class="pbm">
              <a href="https://www.facebook.com/cityofphiladelphia" class="prl" data-analytics="social">
                <i class="fab fa-facebook-f fa-2x" title="Facebook" aria-hidden="true"></i>
                <span class="show-for-sr">Facebook</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://twitter.com/PhiladelphiaGov" class="prl"  data-analytics="social">
                <i class="fa-brands fa-x-twitter fa-2x" title="Twitter" aria-hidden="true"></i>
                <span class="show-for-sr">Twitter</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.instagram.com/cityofphiladelphia/" class="prl" data-analytics="social">
                <i class="fab fa-instagram fa-2x" title="Instagram" aria-hidden="true"></i>
                <span class="show-for-sr">Instagram</span>
              </a>
            </li>
            <li class="pvxs">
              <a href="https://www.phila.gov/departments/office-of-innovation-and-technology/phlgovtv/" class="prl">
                <span class="fa fa-stack fa-lg">
                  <i class="fa fa-tv fa-stack-2x" title="TV" aria-hidden="true"></i>
                  <i class="govtv-container fa fa-stack-1x">
                    <span class="govtv-text">GovTV</span>
                  </i>
                </span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.youtube.com/user/PhilaGov" class="prl" data-analytics="social">
                <i class="fab fa-youtube fa-2x" title="Youtube" aria-hidden="true"></i>
                <span class="show-for-sr">Youtube</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.flickr.com/photos/phillycityrep" class="prl" data-analytics="social">
                <i class="fab fa-flickr fa-2x" title="Flickr" aria-hidden="true"></i>
                <span class="show-for-sr">Flickr</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://github.com/CityOfPhiladelphia" class="prl"  data-analytics="social">
                <i class="fab fa-github fa-2x" title="GitHub" aria-hidden="true"></i>
                <span class="show-for-sr">GitHub</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.linkedin.com/showcase/phlcitycareers" class="prl"  data-analytics="social">
                <i class="fab fa-linkedin-in fa-2x" title="LinkedIn" aria-hidden="true"></i>
                <span class="show-for-sr">LinkedIn</span>
              </a>
            </li>
          </ul>
        </nav>
      </section>
    </div> <!-- row -->
  </div><!-- /phila-footer -->
  <div class="bg-black">
    <div class="row classic">
      <nav class="columns center">
        <ul class="inline-list">
            <li><a href="/terms-of-use">Terms of use</a></li>
            <li><a href="https://www.phila.gov/open-records-policy/">Right to know</a></li>
            <li><a href="https://www.phila.gov/privacypolicy">Privacy Policy</a></li>
            <li><a href="https://www.phila.gov/documents/ada-policies/">Accessibility</a></li>
        </ul>
      </nav>
    </div>
  </div>
</footer><!-- #colophon -->

<?php wp_footer(); ?>
  <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL}, 'google_translate_element');
    }
  </script>
  <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  <script>$(document).foundation();</script>
  <?php if (get_post_type() === 'service_page') :?>
    <script>$(function(){setTimeout(function(){$(".equal").length>0&&($(".equal-height").each(function(){$(this).find(".equal").attr("data-equalizer-watch","")}),new Foundation.Equalizer($(".equal-height"),{equalizeOnStack:!0,equalizeByRow:!0,equalizeOn:"small"}))},500)});</script>
  <?php endif;?>
  <?php if (function_exists('rwmb_meta')): ?>
    <?php $append_after_footer = rwmb_meta( 'phila_append_after_footer', $args = array('type' => 'textarea'), $post->ID); ?>
    <?php if ( !$append_after_footer == '' ): ?>
      <!-- Begin Custom Markup Metabox: Append to Footer -->
      <?php echo $append_after_footer; ?>
      <!-- End Custom Markup Metabox: Append to Footer -->
    <?php endif;?>
  <?php endif; ?>
  </body>
</html>
