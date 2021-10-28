module.exports = $(function(){
  /* Async loaded feedback forms */

  $("footer .feedback-updated").on('click', function(){
    $('html,body').animate({
        scrollTop: $('.feedback-updated').position().top - $('header .is-stuck').height()
      }, 700 );
    return false;
  });

});

