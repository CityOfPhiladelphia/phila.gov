module.exports = $(function(){

  $('.stage-container').hover(function(e){

    $('.stage-tracker .chevron').each(function() {
      $(this).removeClass('open');
    });
    $(this).closest(".chevron").toggleClass('open');

  });
  $('.stage-tracker .chevron .description').hover(function(e){
    e.stopPropagation();
  });

  var size = Foundation.MediaQuery.is('medium down');
  if (size) {
  $('.stage-container').click(function(e){

    $('.stage-tracker .chevron').each(function() {
      $(this).removeClass('open');
    });
    $(this).closest(".chevron").toggleClass('open');
    e.stopPropagation();
  });
    $('.stage-tracker .chevron .description').click(function(e){
      e.stopPropagation();
    });
  }

});
