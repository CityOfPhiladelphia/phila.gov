jQuery( function( $ ) {

    $( '.translate-homepage' ).on( 'click', function() {
    $.post(window.phila_homepage_js_vars.update_translations_webhook, JSON.stringify({page_slug: 'home' ,
department_code: phila_homepage_js_vars.update_translations_dept_billing_code})
        );
    });
});

