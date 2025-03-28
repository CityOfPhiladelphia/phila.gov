module.exports = $(function () {
  //  window.dataLayer = window.dataLayer || [];

  /*Globals */
  var navHeight = $('.global-nav').height();
  var windowWidth = $(window).width();

//Generic class for links that should prevent clickthrough
$('.no-link').click(function(e){
  e.preventDefault();
});

/* Drilldown menu */
$('.menu-icon').click(function(e) {
  extendMenuToggle();
});

//opened submenu
$(document).on('open.zf.drilldown', '[data-drilldown]', function(){
  /* Ensure no events get through on titles */
  $('.is-submenu-parent-item').each(function( ) {
    $(this).click(function(e) {
      return false;
    });
  });
});

$('#services-mega-menu').hover( function(){
  $( '.site-search i' ).addClass('fa-search').removeClass('fa-times');

}, function(){
  $('body').removeClass('no-scroll');
  $( '.site-search i' ).addClass('fa-search').removeClass('fa-times');

});

function extendMenuToggle() {
  $('.menu-icon i').toggleClass('fa-bars').toggleClass('fa-xmark');
  $('.global-nav .menu-icon').toggleClass('active');
  $('#vue-mobile-menu').toggleClass('hide');
  $('html').toggleClass('is-reveal-open');
  console.log('toggle');
}

function checkBrowserHeight(){
  if ( $('body').hasClass('logged-in') ) {
    return;
  }

  var wh = window.innerHeight;

  var sh = $('#services-mega-menu').innerHeight();

  sh = sh + navHeight;

  if ( $('.sticky').hasClass('is-stuck') ){
    navHeight = $('.sticky-container').height();
  }

  if ( wh <= sh ) {
    $('.mega-menu-dropdown').css({
      'position': 'absolute',
      'top': '0'
    });

    $('body').removeClass('no-scroll');
    $('#page').addClass('hide');
    $('footer').addClass('hide');

  }else{

    $('body').addClass('no-scroll');
    showBodyContent();
  }

}

/* Mega menu Dropdown */
$('#services-mega-menu').on('show.zf.dropdown', function() {
  $('#back-to-top').css('display', 'none');
  checkBrowserHeight();
  Foundation.reInit('equalizer');
});


//click and hover handler for desktop service menu link
$('.services-menu-link').on('click mouseover', function () {
  $( '.site-search i' ).addClass('fa-search').removeClass('fa-times');
});


/* All dropdowns */
$(document).on('hide.zf.dropdown', '[data-dropdown]', function() {
  $('body').removeClass('no-scroll');
  if ( !$('.is-drilldown').is(':visible') ){
    $('#page').removeClass('hide');
    $('footer').removeClass('hide');
  }
  $( '.site-search i' ).removeClass('fa-times').addClass('fa-search');
  $('.site-search span').text('Search');
});


/* Site search dropdown */
$('.site-search-dropdown').on('show.zf.dropdown', function(){
  //menu toggle close when menu is already open
  if ( (Foundation.MediaQuery.current === 'small') && $('.is-drilldown').is(':visible') ){
    $('.title-bar').foundation('toggleMenu');
  }
  $( '.site-search i' ).addClass('fa-times').removeClass('fa-search');

  $('.site-search span').text( ( $('.site-search span' ).text() === 'Search' ) ? 'Close' : 'Search' );

  $('body').addClass('no-scroll');

  if ( $('.sticky').hasClass('is-stuck') ){
    navHeight = $('.sticky-container').height();
  }else{
    navHeight = $('.global-nav').height();
  }

  $(this).css('top', navHeight);
});

function showBodyContent(){
  $('#page').removeClass('hide');
  $('footer').removeClass('hide');
}

$( window ).resize(function() {
  //check window width for mobile devices to prevent window resize on scroll.
  if ($(window).width() !== windowWidth) {
    windowWidth = $(window).width();

    if (Foundation.MediaQuery.atLeast('medium')) {
      showBodyContent();
    }
  }
  //orientation doesn't matter, always remove the no-scroll class
  $('body').removeClass('no-scroll');
});


/* prevent search dropdown from becoming dissconnected from header when keyboard is closed on iOS devices */
$('#search-field').focusout(function() {
  if ( Foundation.MediaQuery.current === 'small' ) {
    window.scrollTo(0, 0);
  }
});

$('.clickable-row').click(function() {
  var name = $(this).children().children().children()[1];
  window.dataLayer.push({
    'event' : 'GAEvent',
    'eventCategory' : 'Content Download',
    'eventAction' : phila_js_vars.postTitle,
    'eventLabel' : $(name)[0].innerText,
  });
});

$('.clickable-row .document').click(function(e) {
  var name = $(this).children()[0];
  window.dataLayer.push({
    'event' : 'GAEvent',
    'eventCategory' : 'Content Download',
    'eventAction' : phila_js_vars.postTitle,
    'eventLabel' : name.innerText,
  });
});

  //foundation equalizer rows
  //doesn't work with nested Equalizers, because a unique ID is required.
  if ($('.equal').length > 0) {

      //equalizeByRow: true to force each instance of equalizer to work individually
      var equalizerOptions = {
          equalizeOnStack: true,
          equalizeByRow: true,
          equalizeOn: 'small'
      };

      $('.equal-height').each(function () {
          $(this).find('.equal').attr('data-equalizer-watch', '');
      });

      var equalHeight = new Foundation.Equalizer($('.equal-height'), equalizerOptions);

  }

  //foundation tooltips
  if ($('.has-tip').length > 0) {

      var tooltip = new Foundation.Tooltip($('.has-tip'));

  }
  var mainContent = $('.guide-content').eq(0);

});

document.addEventListener("DOMContentLoaded", function() {
var titles = document.querySelectorAll(".cost .title");
var maxHeight = 0;

titles.forEach(function(title) {
  var titleHeight = title.offsetHeight;
  if (titleHeight > maxHeight) {
    maxHeight = titleHeight;
  }
});

titles.forEach(function(title) {
  title.style.height = maxHeight + "px";
});
});