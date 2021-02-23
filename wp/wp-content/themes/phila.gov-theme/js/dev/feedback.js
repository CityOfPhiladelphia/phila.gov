var postscribe = require('postscribe');

module.exports = $(function(){
  /* Async loaded feedback forms */
  $.fn.feedbackify = function( src ) {

    if( $('#form-container').is(':empty') ) {

      postscribe('#form-container', '<script  type="text/javascript" src="' + src + '"><\/script>');

    }

    $('html,body').animate({
        scrollTop: $('.feedback-updated').position().top - $('header .is-stuck').height()
      }, 700 );
    return false;


  };

  $("footer .feedback-updated").on('click', function(){
    $(this).feedbackify('https://form.jotform.com/jsform/62765090493967');
  });

});
