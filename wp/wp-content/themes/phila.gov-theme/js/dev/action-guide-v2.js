module.exports = $(function () {

  function getUrlParameter(sParam) {
    let sPageURL = window.location.search.substring(1),
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
    let activeTabNumber = getUrlParameter('tab');
    tabNavigation( activeTabNumber );
  });

  function tabNavigation( destinationTabNumber ) {
    let steps = $(".tab-label").length;
    for (let i = 1; i <= steps; i++) {
      let stepId = '#step-'+i+'-label';
      let tabId = '#tab-'+i+'-content';
      if (i == destinationTabNumber) {
        if (!$(stepId).hasClass('active')) {
          $(tabId).addClass('active');
          $(stepId).addClass('active');  
          history.pushState({}, null, window.location.origin+window.location.pathname+'?tab='+i);
          $( ".translation-link" ).each(function() {
            let href = $(this).attr('href');
            if (href) {
              $(this).attr('href', href.split("?")[0] + '?tab='+i);
            }
          });
        }
      }
      else {
        $(stepId).removeClass('active');
        $(tabId).removeClass('active');
      }
    }
  }

  $('.next-tab').click(function () {
    let activeTabNumber = getUrlParameter('tab');
    let nextTabNumber = parseInt(activeTabNumber)+1;
    tabNavigation( nextTabNumber );
  });

  $('.prev-tab').click(function () {
    let activeTabNumber = getUrlParameter('tab');
    let prevTabNumber = parseInt(activeTabNumber)-1;
    tabNavigation( prevTabNumber );
  });

  $('.tab-label').click(function () {
    let tab_id = $(this).attr('id');
    let numberPattern = /\d+/g;
    let tabNumber = tab_id.match( numberPattern )[0];
    tabNavigation( tabNumber );
  });

});