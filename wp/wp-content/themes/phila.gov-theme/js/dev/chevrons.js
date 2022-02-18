module.exports = $(function(){

  var size = Foundation.MediaQuery.is('large up');

  if (size) {
    $('.stage-container').hover(function(e){

      $('.stage-tracker .chevron').each(function() {
        $(this).removeClass('open');
      });
      $(this).closest(".chevron").toggleClass('open');

    });
    $('.stage-tracker .chevron .description').hover(function(e){
      e.stopPropagation();
    });
  }

});
