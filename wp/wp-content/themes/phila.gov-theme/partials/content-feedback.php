<?php

/*
  Partial for our feedback area.
*/
?>
<div class="row mtl">
  <div class="small-24 columns">
    <?php if( is_404() ) : ?>
      <img src="<?php echo get_stylesheet_directory_uri() . '/img/city-skyline.svg' ?>" />
    <?php endif; ?>
    <div class="feedback-updated phm phn-mu <?php echo ( !is_404() ) ? 'mvs mtm-mu ' : '' ?>mbxl-mu">
      <div class="row" data-toggle="feedback">
        <div class="small-24 columns">
          <div class="call-to-action phs pvm center">
            <a href="#" class="no-link">We’re always working to improve phila.gov.
            <span class="break-before-sm"> How can we make this page better?</span></a>
          </div>
        </div>
      </div>
      <div class="feedback-form" data-type="feedback-form" style="display:none;">
        <div class="row">
          <div class="medium-18 large-14 column small-centered mbm clearfix" data-type="form-wrapper" >
            <div id="feedback-container"><iframe src="https://phila.formstack.com/forms/philagov_feedback?referrer=<?php echo get_permalink()?>" title="phila.gov feedback" width="600" height="900" frameBorder="0"></iframe></div>
          </div>
        </div>
      </div>
      <div class="row" data-type="feedback-indicator">
        <div class="small-24 columns center">
          <div class="arrow-wrapper">
            <div class="arrow"></div>
          </div>
        </div>
      </div>
      <div class="row expanded" data-toggle="feedback" data-type="feedback-footer" style="display:none;">
        <div class="small-24 columns">
          <div class="call-to-action center">
            <div class="pas"><a href="#" class="no-link"><i class="fas fa-times" aria-hidden="true"></i> Close</a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>