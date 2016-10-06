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

  /*Globals */
  var navHeight = $('.global-nav').height();
  var currentPath = window.location.pathname;
  var windowWidth = $(window).width();

  //Generic class for links that should prevent clickthrough
  $('.no-link').click(function(e){
    e.preventDefault();
  });

  $('.top-bar').css( 'top', navHeight );

  $( $( '.desktop-nav a' ) ).each( function() {
    if ( currentPath == $( this ).attr('href') ||   currentPath == $( this ).data( 'link') ){

      $(this).addClass('js-is-current');
      //special handling for services
    }else if( currentPath.includes('/services/') ){
      $('.services-menu-link a').addClass('js-is-current');
    }
  });

  /* Provide option for explict show/hide declarations, with jQuery fallbacks for older (ios) browsers */
  function togglePageBody( show ){
    if( show === true ){
      $('#page').show();
      $('#page').removeClass('hide');

      $('footer').show();
      $('footer').removeClass('hide');
      return;
    }

    if( show === false ){

      $('#page').hide();
      $('#page').addClass('hide');

      $('footer').hide();
      $('footer').addClass('hide');
      return;
    }
    $('#page').toggle();
    $('#page').toggleClass('hide');

    $('footer').toggle();
    $('footer').toggleClass('hide');


  }
  /* Drilldown menu */

  $(document).on('toggled.zf.responsiveToggle', '[data-responsive-toggle]', function(){
    var mobileMenu = new Foundation.Drilldown( $('.mobile-nav-drilldown') );

    if ( $( '.js-current-section' ).length === 0 ) {
      $('li.js-drilldown-back').after( '<li class="js-current-section"></li>' );
    }

    $('.menu-icon .title-bar-title').text( ( $('.menu-icon .title-bar-title' ).text() == 'Menu' ) ? 'Close' : 'Menu' );

    $('.global-nav .menu-icon').toggleClass('active');

    $('body').removeClass('no-scroll');

    $('.menu-icon i').toggleClass('fa-bars').toggleClass('fa-close');

    drilldownMenuHeight();

    if($('.mobile-nav-drilldown').is(':visible')){
      togglePageBody(false);

    }else{
      togglePageBody(true);
    }
  });

  var parentLink = ['Main Menu'];

  $(document).on('open.zf.drilldown', '[data-drilldown]', function(){

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

  $('#services-mega-menu').hover( function(){
    $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');

  }, function(){
    $('body').removeClass('no-scroll');
    $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');

  });

  function resetLayout(){
    togglePageBody( true );
    $('.menu-icon i').addClass('fa-bars').removeClass('fa-close');
    $('.menu-icon .title-bar-title').text('Menu');
    $('.menu-icon').removeClass('active');

    $('#services-mega-menu').foundation('close');

    $('body').removeClass('no-scroll');

    if ( $('.is-drilldown').is(':visible') ) {
      $('.title-bar').foundation('toggleMenu');
    }
  }

  function resetScroll(){
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

  function checkBrowserHeight( navHeight ){
    if ( $('body').hasClass('logged-in') ) {
      return;
    }

    var wh = window.innerHeight;

    var sh = $('#services-mega-menu').innerHeight();

    sh = sh + navHeight;

    if ( $('.sticky').hasClass('.is-stuck') ){
      navHeight = $('.sticky-container').height();
    }

    if ( wh <= sh ) {
      $('#services-mega-menu').css({
        'position': 'absolute',
        'top': 0
      });

      togglePageBody( false );
      $('body').removeClass('no-scroll');

    }else{

      togglePageBody( true );
      $('body').addClass('no-scroll');

    }

  }
  /* Mega menu Dropdown */
  $('#services-mega-menu').on('show.zf.dropdown', function() {

    $('#back-to-top').css('display', 'none');

    checkBrowserHeight( navHeight );
  });

  //click and hover handler for desktop service menu link
  $('.services-menu-link').on('click mouseover', function () {
    $( '.site-search i' ).addClass('fa-search').removeClass('fa-close');
  });

  /* All dropdowns */
  $(document).on('hide.zf.dropdown', '[data-dropdown]', function() {
    togglePageBody( true );
    $('body').removeClass('no-scroll');
  });

  /* Site search dropdown */
  $('.site-search-dropdown').on('show.zf.dropdown', function(){
    if ( $('.is-drilldown').is(':visible') ) {
      $('.title-bar').foundation('toggleMenu');
      togglePageBody(true);
    }

    $( '.site-search i' ).addClass('fa-close').removeClass('fa-search');

    $('.site-search span').text( ( $('.site-search span' ).text() == 'Search' ) ? 'Close' : 'Search' );

    $('body').addClass('no-scroll');

  });

  $('.site-search-dropdown').on('hide.zf.dropdown', function() {
    $( '.site-search i' ).removeClass('fa-close').addClass('fa-search');
    $('.site-search span').text('Search');
  });

  function drilldownMenuHeight(){
    if (Foundation.MediaQuery.current == 'small') {
      var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

      var drilldownHeight = $('.is-drilldown').outerHeight();
      var singleHeight = $('.is-drilldown li').outerHeight() + 10;
      $('.is-drilldown ul').css({
        'height': drilldownHeight +  singleHeight + 'px'
      });
    }
  }

  $( window ).resize(function() {

    //check window width for mobile devices to prevent window resize on scroll.
    if ($(window).width() != windowWidth) {
      windowWidth = $(window).width();

      checkBrowserHeight( navHeight ) ;

      drilldownMenuHeight();

      if (Foundation.MediaQuery.atLeast('medium')) {
        $('.sticky:visible').foundation('_calc', true);
        resetLayout();
      }
    }
    $(window).bind('orientationchange', function(e){

    resetLayout();

  });

});

  /* prevent search dropdown from becoming dissconnected from header when keyboard is closed on iOS devices */
  document.addEventListener('focusout', function(e) {
    if ( Foundation.MediaQuery.current == 'small' ) {
      window.scrollTo(0, 0);
    }
  });

  resetScroll();

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

  //Set cookie to 'opt-out' from beta-banner
  $('[data-action="beta-opt-out"]').on('click', function() {
    if (Cookies.get('beta') != undefined) {
      Cookies.set('beta', 'opt-out');
    }
  });

});
