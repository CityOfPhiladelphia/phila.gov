var site_wide_alert = jQuery('#site-wide-alert');
jQuery( window ).on('load', function() {
    if ( window.sessionStorage ) {
        var alert_name = site_wide_alert.data('alert');
        if ( ! window.sessionStorage.getItem( alert_name ) ) {
            site_wide_alert.slideDown();
        }
    }
});

jQuery('.close-button', site_wide_alert).on('click', function(event) {
    event.preventDefault();
    site_wide_alert.slideUp();
    if ( window.sessionStorage ) {
        var alert_name = site_wide_alert.data('alert');
        window.sessionStorage.setItem( alert_name, true );
    }
    return false;
});