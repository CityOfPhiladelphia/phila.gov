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
          <span><a href="http://www.phila.gov/311" class="external">311</a> provides direct access to City government information, services, and real-time service updates. Multiple languages are available. Call 311 or <a href="https://twitter.com/philly311" class="external">tweet @philly311</a> for a quick response.</span>
        </div>
      </div>
      <div class="row pvn pvl-mu equal-height">
        <div class="small-24 medium-8 columns pll prxl pvm ptn-mu pbl-mu pbl-mu sidewalk bdr-right-mu interact-311 clearfix equal">
          <section>
            <h3 class="h4 dark-gray">Interact with 311 online</h3>
            <a href="http://iframe.publicstuff.com/#?client_id=242" class="button icon clearfix">
              <div class="valign">
                <i class="fa fa-bullhorn valign-cell"></i>
                <div class="button-label valign-cell">Report a problem</div>
              </div>
            </a>
            <a href="http://www.phila.gov/311/findananswer/Pages/default.aspx" class="button icon clearfix">
              <div class="valign">
                <i class="fa fa-question valign-cell"></i>
                <div class="button-label valign-cell">Ask a question</div>
              </div>
            </a>
            <a href="https://cityofphiladelphia.github.io/service-request-tracker/" class="button icon clearfix">
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
                  <a href="http://www.phila.gov/OPA/AbatementsExemptions/Pages/Homestead.aspx" class="external">Apply for a Homestead Exemption</a>
                </li>
                <li>
                  <a href="http://www.phila.gov/prisons/Facilities/Pages/default.aspx" class="external">Correctional facilities</a>
                </li>
                <li>
                  <a href="http://www.philapark.org/violations/" class="external">Pay a parking violation</a>
                </li>
                <li>
                  <a href="https://secure.phila.gov/WRB/WaterBill/Account/GetAccount.aspx" class="external">Pay a water bill</a>
                </li>
                <li>
                  <a href="https://ework.phila.gov/revenue/" class="external">Pay a Real Estate Tax bill</a>
                </li>
                <li>
                  <a href="http://property.phila.gov/">Search for property information</a>
                </li>
                <li>
                  <a href="http://www.philadelphiastreets.com/sanitation/residential/collection-schedules" class="external">Trash and recycling schedule</a>
                </li>
                <li>
                  <a href="https://alpha.phila.gov/services/become-a-water-customer/property-owners/">Turn water service on or off</a>
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
        <h2 class="mtn mbm">Take action in your community</h2>
        <nav class="take-action">
          <ul>
            <li class="pvxs"><a href="http://www.phila.gov/phillyrising//index.html" class="external">PhillyRising Collaborative</a></li>
            <li class="pvxs"><a href="http://citizensplanninginstitute.org/" class="external">Citizens Planning Institute</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/311/aboutus/Pages/NeighborhoodLaison.aspx" class="external">Neighborhood Liaison program</a></li>
            <li class="pvxs"><a href="http://citizensplanninginstitute.org/citizens-toolkit" class="external">Citizen action toolkit</a></li>
            <li class="pvxs"><a href="http://www.philadelphiastreets.com/pmbc/" class="external">Clean up your block</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/MDO/SpecialEvents/Pages/PermitsandApplications.aspx" class="external">Apply for an event permit</a></li>
            <li class="pvxs"><a href="http://gsg.phila.gov/map" class="external">City, District, Council, &amp; Ward maps</a></li>
          </ul>
        </nav>
      </section>
      <section class="medium-8 columns phm pll-mu pvm pvn-mu equal">
        <h2 class="mtn mbm"> Know your City government</h2>
        <nav class="take-action">
          <ul>
            <li class="pvxs"><a href="https://alpha.phila.gov/departments/mayor/" class="">Mayor’s Office</a></li>
            <li class="pvxs"><a href="http://phlcouncil.com/" class="external">City Council</a></li>
            <li class="pvxs"><a href="https://alpha.phila.gov/departments/" class="">City government directory</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/openbudget/" class="external">City budget</a></li>
            <li class="pvxs"><a href="http://www.amlegal.com/codes/client/philadelphia_pa/" class="external">Philadelphia Code &amp; Charter</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/records/index.html" class="external">City records</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/ethicsboard/Pages/default.aspx" class="external">Honesty in government</a></li>
            <li class="pvxs"><a href="http://www.philadelphiavotes.com/" class="external">Voting &amp; elections</a></li>
            <li class="pvxs"><a href="http://www.phila.gov/data/" class="external">Open data</a></li>
          </ul>
        </nav>
      </section>
      <section class="medium-8 columns phm pll-mu pvm pvn-mu equal">
        <h2 class="mtn mbm">Connect with City government</h2>
        <nav class="city-social">
          <ul class="inline-list">
            <li class="pbm">
              <a href="https://www.facebook.com/PhiladelphiaCityGovernment/" class="prl">
                <i class="fa fa-facebook fa-3x" title="Facebook" aria-hidden="true"></i>
                <span class="show-for-sr">Facebook</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://twitter.com/PhiladelphiaGov" class="prl">
                <i class="fa fa-twitter fa-3x" title="Twitter" aria-hidden="true"></i>
                <span class="show-for-sr">Twitter</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.instagram.com/cityofphiladelphia/" class="prl">
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
              <a href="https://www.youtube.com/user/PhilaGov" class="prl">
                <i class="fa fa-youtube fa-3x" title="Youtube" aria-hidden="true"></i>
                <span class="show-for-sr">Youtube</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://www.flickr.com/photos/phillycityrep" class="prl">
                <i class="fa fa-flickr fa-3x" title="Flickr" aria-hidden="true"></i>
                <span class="show-for-sr">Flickr</span>
              </a>
            </li>
            <li class="pbm">
              <a href="https://github.com/CityOfPhiladelphia" class="prl">
                <i class="fa fa-github fa-3x" title="GitHub" aria-hidden="true"></i>
                <span class="show-for-sr">GitHub</span>
              </a>
            </li>
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

<script type="text/javascript">
  // For search.js
  var SWIFTYPE_ENGINE = '<?php echo SWIFTYPE_ENGINE?>';
</script>

<?php wp_footer(); ?>
  <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
    }
  </script>
  <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
