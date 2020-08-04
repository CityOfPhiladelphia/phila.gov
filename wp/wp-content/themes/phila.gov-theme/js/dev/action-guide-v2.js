module.exports = $(function () {

  $('#step-1-label, #step-2-to-1-nav span, #step-2-to-1-nav i').click(function () {
    if (!$('#step-1-label').hasClass('active')) {
      $('#tab-1-content').addClass('active');
      $('#step-1-label').addClass('active');

      $('#step-2-label').removeClass('active');
      $('#step-3-label').removeClass('active');
      $('#tab-2-content').removeClass('active');
      $('#tab-3-content').removeClass('active');
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
    }
  });

  $('#step-3-label, #step-3-to-2-nav span, #step-3-to-2-nav i').click(function () {
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