$(function() { 
    $('#site-wide-alert .close-button').on('click', function() { 
        var id_alert = $(this).data('id-alert'); 
        $.post( phila_js_vars.ajaxurl, { action: 'alert_closed_session', id_alert: id_alert } ); 
    }); 
});
