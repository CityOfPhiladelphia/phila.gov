jQuery( function( $ ) {

    $( '.translate-sitewide-alerts' ).on( 'click', function() {
    $.post(window.phila_sitewide_alert_js_vars.update_translations_webhook, JSON.stringify({page_slug: 'site_wide_alert' ,
department_code: phila_sitewide_alert_js_vars.update_translations_dept_billing_code})
        );
    });
});

