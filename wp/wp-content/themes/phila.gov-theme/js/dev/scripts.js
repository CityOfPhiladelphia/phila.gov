/**
 *
 * Custom js for our theme
 *
**/

//department filter list
new List('filter-list', {
  valueNames: ['item', 'item-desc']
});

//provide function for preventing link follow-through
function noLink(e){
  e.preventDefault();
}

//provide logo fallback for old browsers. Thanks https://css-tricks.com/a-complete-guide-to-svg-fallbacks/
function svgasimg() {
  return document.implementation.hasFeature(
    'http://www.w3.org/TR/SVG11/feature#Image', '1.1');
}

if (!svgasimg()){
  var e = document.getElementsByTagName('img');
  if (!e.length){
    e = document.getElementsByTagName('IMG');
  }
  for (var i=0, n=e.length; i<n; i++){
    var img = e[i],
        src = img.getAttribute('src');
    if (src.match(/svgz?$/)) {
      /* URL ends in svg or svgz */
      img.setAttribute('src',
             img.getAttribute('data-fallback'));
    }
  }
}

jQuery(document).ready(function($) {

  var gNavHeight = $('.global-nav').height();

  $('.top-bar').css('top', gNavHeight );


  var currentPath = window.location.pathname;

  $( $( '.desktop-nav a' ) ).each( function() {
    if ( currentPath == $( this ).attr('href') ||   currentPath == $( this ).data( 'link') ){

      $(this).addClass('js-is-current');
      //special handling for services
    }else if( currentPath.includes('/services/') ){
      $('.services-menu-link a').addClass('js-is-current');
    }
  });


  $(document).on('toggled.zf.responsiveToggle', '[data-responsive-toggle]', function(){

    var mobileMenu = new Foundation.Drilldown( $('.mobile-nav-drilldown') );

    if ( $( '.js-current-section').length == 0 ) {
      $('li.js-drilldown-back').after( '<li class="js-current-section"></li>' );
    }

    //close dropdowns when sidenav is open
    $('.dropdown-pane').foundation('close');
    var drilldownHeight = $('.is-drilldown').outerHeight();

    $('#page').toggleClass('hide');
    $('footer').toggleClass('hide');

    $('.global-nav .menu-icon').toggleClass('active');
    $('.menu-icon i').toggleClass('fa-bars').toggleClass('fa-close');
    $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');
    $('.menu-icon .title-bar-title').text(($('.menu-icon .title-bar-title').text() == 'Close') ? 'Menu' : 'Close');

    drilldownMenuHeight();

  });
  //Generic class for links that should prevent clickthrough

  $('.no-link').click(function(e){
    e.preventDefault();
  });

  //click and hover handler for desktop service menu link
  $('.services-menu-link').on('click mouseover', function () {
    $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');
  });

  //Listener for megamenu dropdown to ensure no-scroll gets removed
  $('#services-mega-menu').hover( function(){
    $('body').addClass('no-scroll');
    $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');

  }, function(){
    $('body').removeClass('no-scroll');
    $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');

  });

  function resetLayout(){
    $('#page').removeClass('hide');
    $('footer').removeClass('hide');
    $('body').removeClass('no-scroll');
    $('.menu-icon i').addClass('fa-bars').removeClass('fa-close');
    $('.menu-icon .title-bar-title').text('Menu');
    $('.menu-icon').removeClass('active');
  }

  //TODO: clean this up
  function resetPage(){
    $('#page').click( function() {
      $('body').removeClass('no-scroll');
      $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');
    });

    $('footer').click( function() {
      $('body').removeClass('no-scroll');
      $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');
    });

    $(document).keyup(function(e) {
      //on escape, also remove no-scroll
      if (e.keyCode == 27) {
        $('body').removeClass('no-scroll');
      }
    });
  }
  //thanks http://stackoverflow.com/questions/4814398/how-can-i-check-if-a-scrollbar-is-visible
  //determines if content is scrollable
  $.fn.hasScrollBar = function() {
    return this.get(0).scrollHeight > this.height();
  }
  /* Drilldown menu */
  var parentLink = ['Main Menu'];

  $(document).on('open.zf.drilldown', '[data-drilldown]', function(){

    var activeHeight = $('.is-active').outerHeight();

    $('.dropdown-pane').foundation('close');

    parentLink.push( $(this).find('.is-active').last().prev().text() );

    $(this).find('.is-active').last().addClass('current-parent');

    $('.current-parent > li.js-drilldown-back a').text( 'Back to ' + parentLink.slice(-2)[0] );

    $('.js-current-section').html( parentLink.slice(-1)[0] );

    /* Ensure no events get through on titles */
    $('.js-current-section').each(function( ) {
      $(this).click(function(e) {
        return false;
      });

    });

  });

  $(document).on('hide.zf.drilldown', '[data-drilldown]', function(){
    parentLink.pop();

    $('.current-parent > li.js-drilldown-back a').text( 'Back to ' + parentLink.slice(-2)[0] );

    $('.js-current-section').html( parentLink.slice(-1)[0] );

  });

  var navHeight = $('.global-nav').outerHeight();

  //ensure dropdown stays below header on scroll and open/close
  function updateMegaMenuNavHeight(){
    if ( $('body').hasClass('logged-in') ) {
      return;
    }

    if ( $('.sticky').hasClass('is-stuck') ){
      navHeight = $('.sticky-container').outerHeight();

    }else{
      navHeight = $('.global-nav').outerHeight();
    }

    $('.dropdown-pane').css({
      'top': navHeight
    });

  }

  /* Dropdown */
  $(document).on('show.zf.dropdown', '[data-dropdown]', function() {
    //if ( Foundation.MediaQuery.atLeast('medium') ) {
    $('body').addClass('no-scroll');
    //}
    $('#back-to-top').css('display', 'none');

    updateMegaMenuNavHeight();

  });

  $('.site-search').on('show.zf.dropdown', function(){
    $( '.site-search i' ).addClass('fa-close').removeClass('fa-search');
  });

  $('.site-search').click(function(){
    $( '.site-search i' ).toggleClass('fa-close');
    $( '.site-search i' ).toggleClass('fa-search');


  $('document').on('click.zf.dropdown', '[data-dropdown]', function(){
    $('body').removeClass('no-scroll');
  });

  /* sticky nav */
  $('.sticky').on('sticky.zf.stuckto:top', function(){
    updateMegaMenuNavHeight();
  });

   $('.sticky').on('sticky.zf.unstuckfrom:top', function(){
     updateMegaMenuNavHeight();
  });

  function drilldownMenuHeight(){
    if (Foundation.MediaQuery.current == 'small') {
      var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

      var drilldownHeight = $('.is-drilldown').outerHeight();
      var singleHeight = $('.is-drilldown li').outerHeight();
      $('.is-drilldown ul').css({
        'height': drilldownHeight +  singleHeight + 'px'
      });
    }
  }

  $( window ).resize(function() {
    updateMegaMenuNavHeight();
    drilldownMenuHeight();

    if (Foundation.MediaQuery.atLeast('medium')) {

      $('.sticky:visible').foundation('_calc', true);
    }
    resetLayout();

  });

//  drilldownMenuHeight();

  resetPage();

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
  ajaxMailChimpForm($('#mc-embedded-subscribe-form'), $('#mc_embed_signup'));
  // Turn the given MailChimp form into an ajax version of it.
  // If resultElement is given, the subscribe result is set as html to
  // that element.
  function ajaxMailChimpForm($form, $resultElement){
    // Hijack the submission. We'll submit the form manually.
    $form.submit(function(e) {
      e.preventDefault();
      if (!isValidEmail($form)) {
        var error =  'A valid email address must be provided.';
        $resultElement.append(error);
        $resultElement.css('color', '#f99300');
      } else {
        $resultElement.css('color', 'black');
        $resultElement.append('Subscribing...');
        submitSubscribeForm($form, $resultElement);
      }
    });
  }
  // Validate the email address in the form
  function isValidEmail($form) {
    var email = $form.find('input[type="email"]').val();
    if (!email || !email.length) {
        return false;
    } else if (email.indexOf('@') == -1) {
        return false;
    }
    return true;
  }
  // Submit the form with an ajax/jsonp request.
  // Based on http://stackoverflow.com/a/15120409/215821
  function submitSubscribeForm($form, $resultElement) {
    $.ajax({
      type: 'GET',
      url: $form.attr('action'),
      data: $form.serialize(),
      cache: false,
      dataType: 'jsonp',
      jsonp: 'c', // trigger MailChimp to return a JSONP response
      contentType: 'application/json; charset=utf-8',
      error: function(error){
          // According to jquery docs, this is never called for cross-domain JSONP requests
      },
      success: function(data){
        if (data.result != 'success') {
          var message = data.msg || 'Sorry. Unable to subscribe. Please try again later.';
          $resultElement.css('color', '#f99300');
          if (data.msg && data.msg.indexOf('already subscribed') >= 0) {
            message = 'You\'re already subscribed. Thank you.';
            $resultElement.css('color', 'black');
          }else if (data.msg && data.msg.indexOf('zip code') >= 0) {
            message = 'Please enter a valid zip code.';
            $resultElement.css('color', 'f99300');
          }
          $resultElement.append(message);
        } else {
          $resultElement.css('color', '#58c04d');
          $resultElement.html('Thank you!<br>You must confirm the subscription in your inbox.');
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
    $('[data-type="feedback-form"] iframe').css( 'height', '');
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

  //Site-Wide Feedback Form
  $('[data-toggle="data-site-wide-feedback"]').click(function() {
    $('[data-toggle="data-site-wide-feedback"], [data-site-wide-feedback]').toggle();
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
