module.exports = jQuery(document).ready(function($) {
  var currentPath = window.location.pathname;

  $( $( '.global-nav a' ) ).each( function() {
    if ( currentPath === $( this ).attr('href') || currentPath === $( this ).data( 'link' ) ){

      $(this).addClass('js-is-current');
      //special handling for services
    }else if( currentPath.indexOf('/services/') === 0 ){
      $('.services-menu-link a').addClass('js-is-current');
    }
  });
});
