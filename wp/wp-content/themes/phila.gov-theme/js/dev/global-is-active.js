module.exports = jQuery(document).ready(function($) {
  var currentPath = window.location.pathname;

  $( $( '.global-nav a' ) ).each( function() {
    if ( currentPath === $( this ).attr('href') || currentPath === $( this ).data( 'link' ) ){

      $(this).addClass('js-is-current');
      //special handling for services
    }else if( currentPath.indexOf('/services/') === 0 ){
      $('.services-menu-link a').addClass('js-is-current');
    }else if( currentPath.indexOf('/programs-initiatives/') === 0 ){
      $('.programs-menu-link a').addClass('js-is-current');
    }else if( currentPath.indexOf('/departments/') === 0 ){
      $('.departments-menu-link a').addClass('js-is-current');
    }else if( currentPath.indexOf('/the-latest/') === 0 ){
      $('.news-menu-link a').addClass('js-is-current');
    }else if( currentPath.indexOf('/tools/') === 0 ){
      $('.tools-menu-link a').addClass('js-is-current');
    }else if( currentPath.indexOf('/publications-forms/') === 0 ){
      $('.publications-menu-link a').addClass('js-is-current');
    }
  });
});
