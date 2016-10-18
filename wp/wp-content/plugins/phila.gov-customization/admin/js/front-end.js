/*For all admins, to appear when logged in, on the front-end */

jQuery( document ).ready( function( $ ) {
  var wpAdminBar = $('#wpadminbar').height();
  var navHeight = $('.global-nav').outerHeight() + wpAdminBar;

  function checkBrowserHeight( navHeight ){

    var wh = window.innerHeight;

    var sh = $('#services-mega-menu').height();

    sh = sh + navHeight;

    if ( $('.sticky').hasClass('.is-stuck') ){
      navHeight = $('.sticky-container').height() + wpAdminBar;
    }

    if( wh <= sh ) {
      $('#services-mega-menu').css({
        'position': 'absolute',
        'top': 0
      });
      $('#page').addClass('hide');
      $('footer').addClass('hide');
      $('body').removeClass('no-scroll');

    }else{
      $('body').addClass('no-scroll');
      $('#page').removeClass('hide');
      $('footer').removeClass('hide');

    }

  }
    $('.sticky').on('sticky.zf.stuckto:top', function(){

      $('.sticky').css('top', wpAdminBar);
    });

     $('.sticky').on('sticky.zf.unstuckfrom:top', function(){

       $('.sticky').css('top', 0);

    });

    $('.mega-menu-dropdown').on('show.zf.dropdown', function() {
      checkBrowserHeight(navHeight);
    });

});
