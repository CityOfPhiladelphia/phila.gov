jQuery( document ).ready(function() {
    jQuery('#njt-yc-noti-dismiss').click(function(){
        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                    action: 'njt_yaycommerce_dismiss',
                    nonce: yaycommerce.nonce,
                    type: 'noti'
                },
                success: function(){
                    jQuery('#njt-yc').hide("slow")
                },
                error: function(response){
                    jQuery('#njt-yc').hide("slow")
                    console.log(response.error)
            }
        })
    })
});