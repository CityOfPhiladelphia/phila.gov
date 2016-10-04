<?php

/*
  Partial for our feedback area.
*/
?>
<div class="row">
  <div class="small-24 columns">
    <div class="feedback phm phn-mu mvs mtm-mu mbl-mu">
      <div class="row" data-toggle="feedback">
        <div class="small-24 columns">
          <div class=" call-to-action phs pvl center">
            <i class="fa fa-lightbulb-o fa-x2" aria-hidden="true"></i><span class="break-before-sm"> We're still working on this page's design and content.</span>
            <span class="break-before-sm"> How can we make it better?</span>.
          </div>
        </div>
      </div>
      <div class="feedback-form" data-type="feedback-form" style="display:none;">
        <div class="row">
          <div class="medium-18 large-14 column small-centered mbm clearfix" data-type="form-wrapper" >
            <?php // TODO: insert the form on click ?>
            <div class="site-wide-instructions mvm" data-site-wide-instructions>
              <p>We want to make sure you get the help you need. If you have a question or are interested in providing feedback to City government, please take note of the following.</p>
              <ul>
                <li class="mvl">If you’re experiencing an emergency, please call 9-1-1 immediately.</li>
                <li class="mvl">If you need to file a complaint, submit a service request, contact someone in City government, or ask a question, call 3-1-1 or visit <a href="http://www.phila.gov/311" class="external">phila.gov/311</a>.</li>
                <li class="mvl">If your question or request requires a response from the City, call 3-1-1 or visit <a href="http://www.phila.gov/311" class="external">phila.gov/311</a>.</li>
                <li class="mvl">If you have feedback for the Mayor, call the <a href="https://alpha.phila.gov/departments/mayor/">Office of the Mayor</a> at (215) 686-2181.</li>
              </ul>

              <div data-type="data-continue-feedback">
                <p>Do you want to provide content or design input about beta.phila.gov?</p>
                <div class="center pvs">
                  <a class="button icon clearfix mvs" data-toggle="data-site-wide-feedback">
                    <div class="valign">
                      <div class="button-label valign-cell">Continue</div>
                    </div>
                  </a>
                </div>
              </div>

            </div>
            <div class="border-top-sidewalk" style="display:none;" data-site-wide-feedback>
              <script type="text/javascript" src="https://form.jotform.com/jsform/62765090493967?nojump"></script>
            </div>
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
            <div class="pas"><i class="fa fa-close" aria-hidden="true"></i> Close</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
