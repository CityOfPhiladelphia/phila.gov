jQuery( window ).on('load', function() {
    if ( window.sessionStorage ) {
        if ( ! sessionStorage.getItem('hideAlerts') ) {
            jQuery('#site-wide-alert').slideDown();
        }
    }
});

jQuery('#site-wide-alert .close-button').on('click', function() {
    if ( window.sessionStorage ) {
        sessionStorage.setItem( 'hideAlerts', true );
    }
});