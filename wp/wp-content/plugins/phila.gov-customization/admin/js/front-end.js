/*For all admins, to appear when logged in, on the front-end */

jQuery( document ).ready( function( $ ) {
  var wpAdminBar = $('#wpadminbar').height();
  var navHeight = $('.global-nav').outerHeight() + wpAdminBar;

  function setGlobalNav(){

    if ( $('.sticky').hasClass('is-stuck') ){
      navHeight = $('.sticky-container').height() + wpAdminBar;

    }else{
      navHeight = $('.global-nav').outerHeight() + wpAdminBar;
    }

    $('.dropdown-pane').css({
      'top': navHeight
    });
  }

    $('.sticky').on('sticky.zf.stuckto:top', function(){
      setGlobalNav();

      $('.sticky').css('top', wpAdminBar);
    });

     $('.sticky').on('sticky.zf.unstuckfrom:top', function(){
       setGlobalNav();

       $('.sticky').css('top', 0);

    });

    $(document).on('show.zf.dropdown', '[data-dropdown]', function() {
      setGlobalNav();
    });

});
