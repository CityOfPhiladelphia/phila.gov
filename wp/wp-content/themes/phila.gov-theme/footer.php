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
  <a href="#page" id="back-to-top"><i class="fa fa-arrow-up" aria-hidden="true"></i><br>Top</a>
 <?php get_template_part( 'searchform' ); ?>
 <?php get_template_part( 'partials/content', 'service-mega-menu' ); ?>

<footer data-swiftype-index='false' id="colophon" class="site-footer">
  <?php get_template_part( 'partials/departments/v2/content', 'footer' ) ?>

  <?php get_template_part( 'partials/content', 'modified' ) ?>
  <?php if( !is_home() ) : ?>
    <?php get_template_part( 'partials/content', 'feedback' ); ?>
  <?php endif; ?>
  <?php echo phila_get_dept_contact_blocks(); ?>
  <div id="full-footer-start" class="philly311">
    <section>
      <div class="row">
        <div class="columns intro">
          <h2 class="mbxs">Philly311</h2>
          <span><a href="http://www.phila.gov/311" class="external" aria-label="Philly 311">311</a> provides direct access to City government information, services, and real-time service updates. Multiple languages are available. Call 311 or <a href="https://twitter.com/philly311" class="external">tweet @philly311</a> for a quick response.</span>
        </div>
      </div>
      <div class="row pvn pvl-mu equal-height">
        <div class="small-24 medium-8 columns pll prxl pvm ptn-mu pbl-mu pbl-mu sidewalk bdr-right-mu interact-311 clearfix equal">
          <section>
            <h3 class="h4 dark-gray">Interact with 311 online</h3>
            <a href="http://iframe.publicstuff.com/#?client_id=242" class="button icon full-width clearfix">
              <div class="valign">
                <i class="fa fa-bullhorn valign-cell"></i>
                <div class="button-label valign-cell">Report a problem</div>
              </div>
            </a>
            <a href="http://www.phila.gov/311/findananswer/Pages/default.aspx" class="button icon full-width clearfix">
              <div class="valign">
                <i class="fa fa-question valign-cell"></i>
                <div class="button-label valign-cell">Ask a question</div>
              </div>
            </a>
            <a href="https://cityofphiladelphia.github.io/service-request-tracker/" class="button icon full-width clearfix">
              <div class="valign">
                <i class="fa fa-check-square-o valign-cell"></i>
                <div class="button-label valign-cell">Track a request</div>
              </div>
            </a>
          </section>
        </div>
        <div class="medium-16 columns trending-requests phl pvm pvn-mu equal">
          <section>
            <h3 class="h4 dark-gray">Trending requests</h3>
            <!-- TODO: Begin pulling these in from 311 -->
              <ul class="columns-2-mu">
                <li>
                  <a href="https://<?php phila_util_echo_website_url() ?>/services/property-lots-housing/get-the-homestead-exemption/">Apply for a Homestead Exemption</a>
                </li>
                <li>
                  <a href="http://www.phila.gov/prisons/Facilities/Pages/default.aspx">Correctional facilities</a>
                </li>
                <li>
                  <a href="https://prodpci.etimspayments.com/pbw/include/philadelphia_parking/input.jsp">Pay a parking violation</a>
                </li>
                <li>
                  <a href="https://secure.phila.gov/WRB/WaterBill/Account/GetAccount.aspx">Pay a water bill</a>
                </li>
                <li>
                  <a href="https://<?php phila_util_echo_website_url() ?>/services/payments-assistance-taxes/make-a-payment/pay-your-real-estate-tax/">Pay a Real Estate Tax bill</a>
                </li>
                <li>
                  <a href="http://property.phila.gov/">Search for property information</a>
                </li>
                <li>
                  <a href="http://www.philadelphiastreets.com/sanitation/residential/collection-schedules" class="external">Trash and recycling schedule</a>
                </li>
                <li>
                  <a href="https://<?php phila_util_echo_website_url() ?>/services/water-gas-utilities/water-sewer-services/become-a-water-customer/">Turn water service on or off</a>
                </li>
              </ul>
            </section>
        </div>
      </div>
    </section>
  </div>
  <div class="fat">
    <div class="row pvs ptl-mu phm equal-height">
      <section class="medium-8 columns phm pvm pvn-mu equal">
        <h2 class="mtn mbm">Elected officials</h2>
        <nav class="take-action">
          <ul>
            <li class="pvxs"><a href="https://beta.phila.gov/departments/mayor/">Mayor</a></li>
            <li class="pvxs"><a href="http://phlcouncil.com/" class="external">City Council</a></li>
            <li class="pvxs"><a href="http://www.courts.phila.gov/">Courts</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/districtattorney/pages/default.aspx">District Attorney</a></li>
            <li class="pvxs"><a href="http://www.philadelphiacontroller.org/" class="external">City Controller</a></li>
            <li class="pvxs"><a href="http://www.officeofphiladelphiasheriff.com/" class="external">Sheriff</a></li>
            <li class="pvxs"><a href="https://www.philadelphiavotes.com/" class="external">City Commissioners</a></li>
            <li class="pvxs"><a href="http://secureprod.phila.gov/wills/">Register of Wills</a></li>
          </ul>
        </nav>
      </section>
      <section class="medium-8 columns phm pll-mu pvm pvn-mu equal">
        <h2 class="mtn mbm">Open government</h2>
        <nav class="take-action">
          <ul>
            <li class="pvxs"><a href="https://www.phila.gov/openbudget/" class="">City Budget</a></li>
            <li class="pvxs"><a href="http://www.amlegal.com/codes/client/philadelphia_pa/" class="external">Philadelphia Code &amp; Charter</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/records/index.html">City records</a></li>
            <li class="pvxs"><a href="http://regulations.phila-records.com/">City agency regulations</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/executiveorders/pages/default.aspx">Executive orders</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/integrityworks/pages/default.aspx">Honesty in government</a></li>
            <li class="pvxs"><a href="https://www.philadelphiavotes.com/" class="external">Voting & elections</a></li>
            <li class="pvxs"><a href="https://<?php phila_util_echo_website_url(); ?>/programs/open-data-program/">Open data</a></li>
          </ul>
        </nav>
      </section>
      <section class="medium-8 columns phm pll-mu pvm pvn-mu equal">
        <h2 class="mtn mbm">Connect with City government</h2>
        <nav class="city-social">
          <ul class="inline-list">
            <li class="pbm">
              <a href="https://www.facebook.com/cityofphiladelphia" class="prl" data-analytics="social">
                <i class="fa fa-facebook fa-3x" title="Facebook" aria-hidden="true"></i>
                <span class="show-for-sr">Facebook</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://twitter.com/PhiladelphiaGov" class="prl"  data-analytics="social">
                <i class="fa fa-twitter fa-3x" title="Twitter" aria-hidden="true"></i>
                <span class="show-for-sr">Twitter</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.instagram.com/cityofphiladelphia/" class="prl" data-analytics="social">
                <i class="fa fa-instagram fa-3x" title="Instagram" aria-hidden="true"></i>
                <span class="show-for-sr">Instagram</span>
              </a>
            </li>
            <li class="pvxs">
              <a href="http://www.phila.gov/channel64/Pages/default.aspx" class="prl">
                <span class="fa fa-stack fa-lg">
                  <i class="fa fa-tv fa-stack-2x" title="TV" aria-hidden="true"></i>
                  <i class="fa fa-stack-1x">
                    <span class="h4">64</span>
                  </i>
                  <span class="show-for-sr">TV 64</span>
                </span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.youtube.com/user/PhilaGov" class="prl" data-analytics="social">
                <i class="fa fa-youtube fa-3x" title="Youtube" aria-hidden="true"></i>
                <span class="show-for-sr">Youtube</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.flickr.com/photos/phillycityrep" class="prl" data-analytics="social">
                <i class="fa fa-flickr fa-3x" title="Flickr" aria-hidden="true"></i>
                <span class="show-for-sr">Flickr</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://github.com/CityOfPhiladelphia" class="prl"  data-analytics="social">
                <i class="fa fa-github fa-3x" title="GitHub" aria-hidden="true"></i>
                <span class="show-for-sr">GitHub</span>
              </a>
            </li>
          </ul>
        </nav>
        <nav>
          <ul>
            <li class="pvxs"><a href="https://<?php phila_util_echo_website_url() ?>/departments/">City government directory</a></li>
            <li class="pvxs"><a href="https://<?php phila_util_echo_website_url() ?>/services/">Service directory</a></li>
            <li class="pvxs"><a href="https://<?php phila_util_echo_website_url() ?>/programs/">Programs & initiatives </a></li>
            <li class="pvxs"><a href="https://<?php phila_util_echo_website_url() ?>/documents/">Publications & forms </a></li>
          </ul>
        </nav>
      </section>
    </div> <!-- row -->
  </div><!-- fat -->
  <div class="bg-black">
    <div class="row classic">
      <nav class="columns center">
        <ul class="inline-list">
            <li><a href="/terms-of-use">Terms of use</a></li>
            <li><a href="http://www.phila.gov/privacy/pdfs/FinalCityOpenRecords.pdf">Right to know (pdf)</a></li>
            <li><a href="/privacypolicy">Privacy Policy</a></li>
        </ul>
      </nav>
    </div>
  </div>
</footer><!-- #colophon -->

<?php wp_footer(); ?>
  <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
    }
  </script>
  <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  <script>$(document).foundation();</script>
  <?php if( ( !is_home() ) && ( is_single() ) ) : ?>
      <?php if (function_exists('rwmb_meta')): ?>
        <?php $append_after_footer = rwmb_meta( 'phila_append_after_footer', $args = array('type' => 'textarea'), $post->ID); ?>
        <?php if ( !$append_after_footer == '' ): ?>
          <!-- Begin Custom Markup Metabox: Append to Footer -->
          <?php echo $append_after_footer; ?>
          <!-- End Custom Markup Metabox: Append to Footer -->
        <?php endif;?>
      <?php endif; ?>
  <?php endif; ?>
</body>
</html>
