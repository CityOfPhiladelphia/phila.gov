module.exports = $(function () {

  $('#step-1-label').click(function () {
    if (!$('#step-1-label').hasClass('active')) {
      $('#tab-1-content').addClass('active');
      $('#step-1-label').addClass('active');

      $('#step-2-label').removeClass('active');
      $('#step-3-label').removeClass('active');
      $('#tab-2-content').removeClass('active');
      $('#tab-3-content').removeClass('active');
    }
  });

  $('#step-2-label').click(function () {
    if (!$('#step-2-label').hasClass('active')) {
      $('#tab-2-content').addClass('active');
      $('#step-2-label').addClass('active');

      $('#step-1-label').removeClass('active');
      $('#step-3-label').removeClass('active');
      $('#tab-1-content').removeClass('active');
      $('#tab-3-content').removeClass('active');
    }
  });

  $('#step-3-label').click(function () {
    if (!$('#step-3-label').hasClass('active')) {
      $('#tab-3-content').addClass('active');
      $('#step-3-label').addClass('active');

      $('#step-2-label').removeClass('active');
      $('#step-1-label').removeClass('active');
      $('#tab-2-content').removeClass('active');
      $('#tab-1-content').removeClass('active');
    }
  });

});