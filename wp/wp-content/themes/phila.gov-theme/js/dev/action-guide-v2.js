module.exports = $(function () {

  var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
      sURLVariables = sPageURL.split('&'),
      sParameterName,
      i;

    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split('=');

      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
      }
    }
  };

  $( document ).ready(function() {
    switch(getUrlParameter('tab')) {
      case '1':
        if (!$('#step-1-label').hasClass('active')) {
          $('#tab-1-content').addClass('active');
          $('#step-1-label').addClass('active');
      
          $('#step-2-label').removeClass('active');
          $('#step-3-label').removeClass('active');
          $('#tab-2-content').removeClass('active');
          $('#tab-3-content').removeClass('active');
        }
        break;
      case '2':
        if (!$('#step-2-label').hasClass('active')) {
          $('#tab-2-content').addClass('active');
          $('#step-2-label').addClass('active');
    
          $('#step-1-label').removeClass('active');
          $('#step-3-label').removeClass('active');
          $('#tab-1-content').removeClass('active');
          $('#tab-3-content').removeClass('active');
        }
        break;
      case '3':
        if (!$('#step-3-label').hasClass('active')) {
          $('#tab-3-content').addClass('active');
          $('#step-3-label').addClass('active');
    
          $('#step-2-label').removeClass('active');
          $('#step-1-label').removeClass('active');
          $('#tab-2-content').removeClass('active');
          $('#tab-1-content').removeClass('active');
        }
        break;
      default:
    }
  });

  $('#step-1-label, #step-2-to-1-nav span, #step-2-to-1-nav i').click(function () {
    if (!$('#step-1-label').hasClass('active')) {
      $('#tab-1-content').addClass('active');
      $('#step-1-label').addClass('active');

      $('#step-2-label').removeClass('active');
      $('#step-3-label').removeClass('active');
      $('#tab-2-content').removeClass('active');
      $('#tab-3-content').removeClass('active');
      history.pushState({}, null, window.location.origin+window.location.pathname+'?tab=1');
    }
  });

  $('#step-2-label, #step-1-to-2-nav span, #step-1-to-2-nav i, #step-3-to-2-nav span, #step-3-to-2-nav i').click(function () {
    if (!$('#step-2-label').hasClass('active')) {
      $('#tab-2-content').addClass('active');
      $('#step-2-label').addClass('active');

      $('#step-1-label').removeClass('active');
      $('#step-3-label').removeClass('active');
      $('#tab-1-content').removeClass('active');
      $('#tab-3-content').removeClass('active');
      history.pushState({}, null, window.location.origin+window.location.pathname+'?tab=2');
    }
  });

  $('#step-3-label, #step-2-to-3-nav span, #step-2-to-3-nav i').click(function () {
    if (!$('#step-3-label').hasClass('active')) {
      $('#tab-3-content').addClass('active');
      $('#step-3-label').addClass('active');

      $('#step-2-label').removeClass('active');
      $('#step-1-label').removeClass('active');
      $('#tab-2-content').removeClass('active');
      $('#tab-1-content').removeClass('active');
      history.pushState({}, null, window.location.origin+window.location.pathname+'?tab=3');
    }
  });

});