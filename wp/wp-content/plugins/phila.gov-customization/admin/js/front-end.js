/*For all admins, to appear when logged in, on the front-end */

jQuery( document ).ready( function( $ ) {
  var wpAdminBar = $('#wpadminbar').height();
  var navHeight = $('.global-nav').outerHeight() + wpAdminBar;

    $('.sticky').on('sticky.zf.stuckto:top', function(){
      console.log('sticky');
      var navHeight = $('.sticky-container').outerHeight();
      $('.dropdown-pane').css({
        'top': navHeight
      });

      $('.sticky').css('top', wpAdminBar);
    });

     $('.sticky').on('sticky.zf.unstuckfrom:top', function(){
       $('.dropdown-pane').css({
         'top': navHeight
       });

       $('.sticky').css('top', 0);

    });

});
