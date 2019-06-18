$(function() {
  window.dataLayer = window.dataLayer || [];
  console.log('loaded')

  $('tr.clickable-row').click( function( event ){
    var name = $(this).children().children()[1];
    window.dataLayer.push({
      'event' : 'GAEvent',
      'eventCategory' : 'Content Download',
      'eventAction' : params.postTitle,
      'eventLabel' : $(name)[0].innerText,
    });
    console.log(window.dataLayer)
  });
});