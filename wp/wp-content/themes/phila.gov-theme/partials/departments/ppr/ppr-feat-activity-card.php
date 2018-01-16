
<div class="cell ppr-feat-activity large-8 medium-8 small-20 align-self-center">
  <h3 data-program_name="program_name"></h3>
  <p data-desc class="ppr-feat-activity__desc"></p>
  <a data-id class="ppr-feat-activity__learn-more" href="https://beta.phila.gov/parks-rec-finder/#/program/">Learn More</a>

    <div class="ppr-feat-activity__location flex-container">
      <i class="fa fa-map-marker" aria-hidden="true"></i>
      <div>
        <h5 data-facility_name class="h5 ppr-feat-activity__location-header"></h5>
        <address data-address>
          <span data-street></span><br/>
          <span data-city></span>,
          <span data-state></span>
          <span data-zip></span>
        </address>
        <a data-facility href="https://beta.phila.gov/parks-rec-finder/#/location/">View on Map</a>
      </div>
    </div>
    <footer class="ppr-feat-activity__meta align-spaced flex-container text-center">
      <h5 class="text-center">Ages <br/>
        <span data-age_low></span> - <span data-age_high></span>
      </h5>
      <h5>Gender <br/>
        <span data-gender></span>
      </h5>
      <h5>Fee <br/>
        <span data-fee></span>
      </h5>
    </footer>
    <?php include(dirname(__FILE__).'/ppr-loader-svg.php'); ?>
  </div>
