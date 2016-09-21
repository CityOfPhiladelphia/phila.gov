/**
 *
 * Custom js for our theme
 *
**/

//department filter list
new List('filter-list', {
    valueNames: ['item', 'item-desc']
});

jQuery(document).ready(function($) {

  //TODO: Replace this temporary menu-icon snippet with fully fleshed out script
  $('.menu-icon').click(function(){
    $('.menu-icon').toggleClass('active');
  });

  //add search focus on tap or click
  $('.search-icon').click(function() {

    var fadeOn = $( ".fade" ).is( ":visible" );

    $('.search-field').focus();

    if ( !fadeOn ){
      $('#page').prepend('<div class="fade"></div>');
    }

    $('.fade').click(function() {
      $('.fade').remove();
    });

    var string = $('.search-field').val();

    if( string.match(/\S/) ){
      $('.search-submit').submit();
    }
  });

  $('.services-link').click(function(e){
    e.preventDefault();
  });

  //TODO: detect if the dropdown is no longer in the viewport, then hide it.
  $( window ).scroll(function() {
    if( ('.dropdown-pane.is-open' ).length) {
      $('.dropdown-pane').removeClass('is-open');

    }
  });

  $(document).on('show.zf.dropdown', '[data-dropdown]', function() {
    var navHeight = $('.global-nav').height();

    if ( $('.sticky').hasClass('is-stuck') ) {
      navHeight = navHeight - $('.secondary-nav').outerHeight();
    }
    $(this).css({
      'top': navHeight,
      'position': 'fixed'
    });
  });

  //force foudation menus to display horizontally on desktop and vertically when 'is-drilldown' is present ( aka, on mobile )
  /*$('.menu-icon').click(function() {
    $('.is-drilldown').find('ul').addClass('vertical');

  });
  $( window ).resize(function() {
    $('.is-drilldown').find('ul').removeClass('vertical');
  });
*/

  //prevent enter from refreshing the page and stopping filter search
  $('#filter-list input').keypress(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $('.clickable-row').click(function() {
    window.location = $(this).data('href');
  });

  $('.clickable-row').hover(function() {
    $(this).addClass('is-hover');
    },
    function(){
      $(this).removeClass('is-hover');
  });

  /* Hijack mailchimp signup forms to use ajax handler */

  /*NOTE: this method requires that the form action URL from mailchimp uses subscribe/post-json */
  ajaxMailChimpForm($("#mc-embedded-subscribe-form"), $("#mc_embed_signup"));
  // Turn the given MailChimp form into an ajax version of it.
  // If resultElement is given, the subscribe result is set as html to
  // that element.
  function ajaxMailChimpForm($form, $resultElement){
    // Hijack the submission. We'll submit the form manually.
    $form.submit(function(e) {
      e.preventDefault();
      if (!isValidEmail($form)) {
        var error =  "A valid email address must be provided.";
        $resultElement.append(error);
        $resultElement.css("color", "#f99300");
      } else {
        $resultElement.css("color", "black");
        $resultElement.append("Subscribing...");
        submitSubscribeForm($form, $resultElement);
      }
    });
  }
  // Validate the email address in the form
  function isValidEmail($form) {
    var email = $form.find("input[type='email']").val();
    if (!email || !email.length) {
        return false;
    } else if (email.indexOf("@") == -1) {
        return false;
    }
    return true;
  }
  // Submit the form with an ajax/jsonp request.
  // Based on http://stackoverflow.com/a/15120409/215821
  function submitSubscribeForm($form, $resultElement) {
    $.ajax({
      type: "GET",
      url: $form.attr("action"),
      data: $form.serialize(),
      cache: false,
      dataType: "jsonp",
      jsonp: "c", // trigger MailChimp to return a JSONP response
      contentType: "application/json; charset=utf-8",
      error: function(error){
          // According to jquery docs, this is never called for cross-domain JSONP requests
      },
      success: function(data){
        if (data.result != "success") {
          var message = data.msg || "Sorry. Unable to subscribe. Please try again later.";
          $resultElement.css("color", "#f99300");
          if (data.msg && data.msg.indexOf("already subscribed") >= 0) {
            message = "You're already subscribed. Thank you.";
            $resultElement.css("color", "black");
          }else if (data.msg && data.msg.indexOf("zip code") >= 0) {
            message = "Please enter a valid zip code.";
            $resultElement.css("color", "f99300");
          }
          $resultElement.append(message);
        } else {
          $resultElement.css("color", "#58c04d");
          $resultElement.html("Thank you!<br>You must confirm the subscription in your inbox.");
        }
      }
    });
  }

  //Set Hero Header Tagline font sizes
  if( $('.intro .hero-tagline').length && $('.intro .hero-tagline.emphasis').length  ) {
    var smallFontSize = 2;
    var largeFontSize = 3.5;

    if( $('[data-type="hero-measure"]').width() > 350) {
      while ( $('[data-type="hero-measure"]').width() > 350 ) {
        smallFontSize = smallFontSize - .1;
        $('[data-type="hero-measure"]').css('font-size', smallFontSize + 'rem');
      }
      $('[data-type="hero-tagline"]').css('font-size', smallFontSize + 'rem');
    }

    if( $('[data-type="hero-measure-emphasis"]').width() > 350 ) {
      while ( $('[data-type="hero-measure-emphasis"]').width() > 350 ) {
        largeFontSize = largeFontSize - .1;
        $('[data-type="hero-measure-emphasis"]').css('font-size', largeFontSize + 'rem');
      }
      $('[data-type="hero-tagline-emphasis"]').css('font-size', largeFontSize + 'rem');
    }
  }

  //Homepage Feedback Form
  $('[data-toggle="feedback"]').click(function() {
    if ( $('[data-type="feedback-indicator"]').hasClass('up') ){
      $('[data-type="feedback-form"]').slideToggle( function(){
        $('[data-type="feedback-indicator"]').removeClass('up');
        $('[data-type="feedback-footer"]').toggle();
      });
    } else {
      $('[data-type="feedback-form"]').slideToggle();
      $('[data-type="feedback-indicator"]').addClass('up');
      $('[data-type="feedback-footer"]').toggle();
    }
  });

  if ( $('#back-to-top').length ) {
    var fromBottom = $('footer').outerHeight();

    var scrollTrigger = 100, // px
      backToTop = function () {
        var scrollTop = $(window).scrollTop();
        if (scrollTop > scrollTrigger) {
          $('#back-to-top').addClass('show');
        } else {
          $('#back-to-top').removeClass('show');
        }
      };

    backToTop();

    $(window).on('scroll', function () {
      backToTop();
      if ( $('footer').offset().top < $(this).height() + $(this).scrollTop() ){
        $('#back-to-top').css({
          'position': 'absolute',
          'bottom': '1%'
        });
      }else{
        $('#back-to-top').removeAttr( 'style' );
      }

    });

    $('#back-to-top').on('click', function (e) {
      e.preventDefault();
      $('html,body').animate({
        scrollTop: 0
      }, 700);
    });
  }

});
