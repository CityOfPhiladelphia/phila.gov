module.exports = $(function () {

    $('.print-entire-guide').click(function () {
        $('.guides-print-all-content').addClass('make-visible');
        //reset print view so if user tries to print normally, they just get the page content
        setTimeout(() => $('.guides-print-all-content').removeClass('make-visible'), 200)
    });

    if (Foundation.MediaQuery.is('small only')) {

        $("#page-title-button, #nav-menu-caret, .nav-subheader").click(function () {
            $(".nav-container").toggle();
            $("#guides-nav").toggle();
            $(".guides .sticky-container").height("100%");
            $('body').toggleClass('no-scroll');
            $('#wpadminbar').toggle();
        });

        var hash = window.location.hash
        if (hash == '' || hash == '#' || hash == undefined) return false;
        var target = $(hash);
        var headerHeight = $('.page-title-button').height() + 20;
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
            $('html,body').stop().animate({
                scrollTop: target.offset().top - headerHeight //offsets for fixed header
            }, 'linear');
        }

        var headerHeight = $('.page-title-button').height() + 20;

        $('a[href*="#"]:not([href="#"])').click(function() {
            var target = $(this.hash);
              $('html,body').stop().animate({
                scrollTop: target.offset().top - headerHeight
              }, 'linear');   
        });    
    }

});