<div data-swiftype-index='false' id="alpha-alert">
  <div class="row">
    <div class="large-17 columns">
    <?php
      global $post;

      $phila_gov_link = sprintf(
        esc_html_x( '%s', 'site-url', 'phila-gov' ), '<a href="http://www.phila.gov/" target="_new" class="external">phila.gov</a>' );

        $about_link = sprintf( esc_html_x( '%s', 'under construction', 'phila-gov' ), '<a href="/about/">work-in-progress</a>' ); ?>

        <p> <?php _e('This site is a ' . $about_link . ' that will change as we add content. Please ', 'phila-gov') ; ?>

        <a class="feedback" href="<?php echo phila_util_echo_feedback_url(); ?>">
            <?php printf( __( 'notify us of errors.', 'phila-gov' )); ?>
          </a>
         </p>

          <a class="go-back small-text external" href="http://www.phila.gov" target="_blank">Take me back to Phila.gov<span class="accessible"> Opens in new window</span></a>
    </div>
    <div class="large-7 columns text-right">
      <i class="fa fa-globe"></i><div id="google_translate_element"></div>
        <script type="text/javascript">
          function googleTranslateElementInit() {
            new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
          }
        </script>
        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    </div>
  </div>
</div>
