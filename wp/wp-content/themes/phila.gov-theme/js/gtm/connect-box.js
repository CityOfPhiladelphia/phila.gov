$(function() {
  window.dataLayer = window.dataLayer || [];

  $('.connect-box .website').click( function( ){
    var name = $(this)

    window.dataLayer.push({
      'event' : 'GAEvent',
      'eventCategory' : 'Contact us',
      'eventAction' : 'Website',
      'eventLabel' : name.html().trim(),
    });
  });
  $('.connect-box .phone-link').click( function( ){
    var name = $(this)

    window.dataLayer.push({
      'event' : 'GAEvent',
      'eventCategory' : 'Contact us',
      'eventAction' : 'Phone',
      'eventLabel' : name.html().trim(),
    });
  });
  $('.connect-box .u-email').click( function( ){
    var name = $(this)

    window.dataLayer.push({
      'event' : 'GAEvent',
      'eventCategory' : 'Contact us',
      'eventAction' : 'Email',
      'eventLabel' : name.html().trim(),
    });
  });
});