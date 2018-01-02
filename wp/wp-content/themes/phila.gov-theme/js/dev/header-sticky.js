// Smooth Sticky Header
$(function() {
    var stickyHeaderOffset = $('#global-sticky-nav').offset().top;
    var stickyBar = $('#global-sticky-nav .phila-sticky');
    var isLoggedIn = $('#wpadminbar').length || false;
    if (isLoggedIn) {
        stickyHeaderOffset -= $('#wpadminbar').outerHeight();
    }
    
    function validateSticky() {
        if (isLoggedIn) {
            stickyBar.data('margin-top', $('#wpadminbar').outerHeight());
        }
        var curScrollPos = $(window).scrollTop();
        if (curScrollPos >= stickyHeaderOffset) {
            stickyBar.addClass('sticky');
        } else {
            stickyBar.removeClass('sticky');
        }
    }
    $(window).on('scroll', validateSticky);
    validateSticky();
});