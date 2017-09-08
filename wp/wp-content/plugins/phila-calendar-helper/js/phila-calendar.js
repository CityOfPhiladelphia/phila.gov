jQuery( document ).ready( function() {
    jQuery( '#publish' ).click( function() {
        if( ! jQuery( this ).data( "valid" ) ) {
            if( jQuery('#post input[name="phila_calendar_google_calendar_id"]' ).val().indexOf( 'calendar.google.com' ) <= -1 ){
                alert( "Error: Oops!, it looks like you didn't entered a valid Google Calendar ID" );
                jQuery( "#post" ).data( "valid", false );

                //hide loading icon, return Publish button to normal
                jQuery('#ajax-loading').hide();
                jQuery('#publish').removeClass('button-primary-disabled');
                jQuery('#save-post').removeClass('button-disabled');
            } else {
                jQuery( "#post" ).data( "valid", true );
            }
        }
    });
});