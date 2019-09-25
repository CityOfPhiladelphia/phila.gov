module.exports = $(function () {

    // console.log("im here baby")
    $("#guides-button-more").click(function () {
        if ($("#guides-intro-text").height() == 100) {
            $("#guides-intro-text").animate(
                { height: "100%" });
            $(this).text(function () {
                return $(this).text().replace("More", "Less");
            });
        }
        else if ($("#guides-intro-text").height() > 100) {
            $("#guides-intro-text").animate({ height: "100px" });
            $(this).text(function () {
                return $(this).text().replace("Less", "More");
            })
        }
    });

    $('.print-entire-guide').click(function(){
        $('.guides-print-all-content').addClass('make-visible');
        //reset print view so if user tries to print normally, they just get the page content
        setTimeout(()=> $('.guides-print-all-content').removeClass('make-visible'), 200) 
    });
    // $(".nav-container").hide();

    if (Foundation.MediaQuery.is('small only')) {

        $("#page-title-button, #nav-menu-caret, .nav-subheader").click(function () {
            $(".nav-container").toggle();
            $("#guides-nav").toggle();
            $(".guides .sticky-container").height("100%");
            $('body').toggleClass('no-scroll');
            $('#wpadminbar').toggle();
        });


        // $(".title-link, .nav-subheader").click( function () {
        //     console.log($('.page-title-button').height());
            
        //         window.scrollTo(window.scrollX, window.scrollY - $('.page-title-button').height());
            
        // });

    }

    
    //calc offset from page-title-button and offset top of anchor x pix

});