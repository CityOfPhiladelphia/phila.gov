module.exports = $(function(){
  $('.stage-tracker .chevron .bg-dark-gray').click(function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.stage-tracker .chevron').each(function() {
      $(this).removeClass('open');
    });
    $(this).closest(".chevron").toggleClass('open');
  });
  $('.stage-tracker .chevron .description').click(function(e){
    e.preventDefault();
    e.stopPropagation();
  });
  $(document).on('click', function (e) {
    $('.stage-tracker .chevron').each(function() {
      $(this).removeClass('open');
    });
  });
});
