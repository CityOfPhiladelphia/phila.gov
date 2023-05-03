jQuery( function( $ ) {

    $( '.deploy-all' ).on( 'click', function() {
    $.post(window.gridsome_js_vars.deploy_all_webhook, JSON.stringify({page_slug: 'home' ,
department_code: gridsome_js_vars.deploy_all_dept_billing_code})
        );
    });
});

