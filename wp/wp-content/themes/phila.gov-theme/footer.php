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
        <span><a href="/" class="external">311</a> provides direct access to City government information, services, and real-time service updates. Multiple languages are available. Call 3-1-1 or tweet @philly311 for a quick response.</span>
      </div>
    </div>
    <div class="row pvn pvl-mu equal-height">
      <div class="small-24 medium-8 columns pll pvm pvn-mu sidewalk bdr-right-mu interact-311 equal">
        <h3 class="dark-gray">Interact with 311 online</h3>

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
      <div class="medium-16 columns trending-requests phl plxl-mu pvm pvn-mu equal">
        <h3 class="dark-gray">Trending Requests</h3>
        <!-- TODO: Begin pulling these in from 311 -->
          <ul class="columns-2-mu">
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
  <section class="fat pvs">
    <div class="row phm">
      <div class="medium-8 columns phm pvm pvn-mu bdr-sidewalk bdr-bottom-sm bdr-right-mu">
        <h1 class="mtn mbm">Take action in your community</h1>
        <nav class="take-action">
          <ul>
            <li class="pvxs"><a href="" class="external">PhillyRising Collaborative</a></li>
            <li class="pvxs"><a href="" class="external">Citizens Planning Institute</a></li>
            <li class="pvxs"><a href="" class="external">Neighborhood Liaison program</a></li>
            <li class="pvxs"><a href="" class="external">Citizen action toolkit</a></li>
            <li class="pvxs"><a href="" class="external">Clean up your block</a></li>
            <li class="pvxs"><a href="" class="external">Apply for an event permit</a></li>
            <li class="pvxs"><a href="" class="external">City, District, Council, &amp; Ward maps</a></li>
          </ul>
        </nav>
      </div>
      <div class="medium-8 columns phm pll-mu pvm pvn-mu bdr-sidewalk bdr-bottom-sm bdr-right-mu">
        <h1 class="mtn mbm"> Know your City government</h1>
        <nav class="take-action">
          <ul>
            <li class="pvxs"><a href="" class="external">Mayor’s Office</a></li>
            <li class="pvxs"><a href="" class="external">Citizens Planning Institute</a></li>
            <li class="pvxs"><a href="" class="external">Neighborhood Liaison program</a></li>
            <li class="pvxs"><a href="" class="external">Citizen action toolkit</a></li>
            <li class="pvxs"><a href="" class="external">Clean up your block</a></li>
            <li class="pvxs"><a href="" class="external">Apply for an event permit</a></li>
            <li class="pvxs"><a href="" class="external">City, District, Council, &amp; Ward maps</a></li>
          </ul>
        </nav>
      </div>
      <div class="medium-8 columns phm pll-mu pvm pvn-mu">
        <h1 class="mtn mbm">Connect with City government</h1>
        <nav class="city-social">
          <ul class="inline-list">
            <li class="pvxs">
              <a href="" target="_blank" class="phm">
                <i class="fa fa-tv fa-2x" title="TV" aria-hidden="true"></i>
                <span class="show-for-sr">TV</span>
              </a>
            </li>
            <li class="pvxs">
              <a href="" target="_blank" class="phm">
                <i class="fa fa-facebook fa-2x" title="Facebook" aria-hidden="true"></i>
                <span class="show-for-sr">Facebook</span>
              </a>
            </li>
            <li class="pvxs">
              <a href="" target="_blank" class="phm">
                <i class="fa fa-twitter fa-2x" title="Twitter" aria-hidden="true"></i>
                <span class="show-for-sr">Twitter</span>
              </a>
            </li>
            <br/>
            <li class="pln pvxs">
              <a href="" target="_blank" class="phm">
                <i class="fa fa-instagram fa-2x" title="Instagram" aria-hidden="true"></i>
                <span class="show-for-sr">Instagram</span>
              </a>
            </li>
            <li class="pvxs">
              <a href="" target="_blank" class="phm">
                <i class="fa fa-youtube fa-2x" title="Youtube" aria-hidden="true"></i>
                <span class="show-for-sr">Youtube</span>
              </a>
            </li>
            <li class="pvxs">
              <a href="" target="_blank" class="phm">
                <i class="fa fa-flickr fa-2x" title="Flickr" aria-hidden="true"></i>
                <span class="show-for-sr">Flickr</span>
              </a>
            </li>
            <br/>
            <li class="pvxs">
              <a href="" target="_blank" class="phm">
                <i class="fa fa-github fa-2x" title="GitHub" aria-hidden="true"></i>
                <span class="show-for-sr">GitHub</span>
              </a>
            </li>
          </ul>
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
