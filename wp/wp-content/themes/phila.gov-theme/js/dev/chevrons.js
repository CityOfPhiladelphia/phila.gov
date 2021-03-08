module.exports = $(function(){
  $('.covid-phases .chevron .bg-dark-gray').click(function(e){
    e.preventDefault();
    e.stopPropagation();
    $(this).closest(".chevron").toggleClass('open');
  });
  $('.covid-phases .chevron .description').click(function(e){
    e.preventDefault();
    e.stopPropagation();
  });
  $(document).on('click', function (e) {
    $('.covid-phases .chevron').each(function() {
      $(this).removeClass('open');
    });
  });
});
