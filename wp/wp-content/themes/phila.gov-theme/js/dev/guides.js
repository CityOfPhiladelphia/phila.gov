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

    // $(".nav-container").hide();
    
    if (Foundation.MediaQuery.is('small only')) {

   if  ($('.nav-container').is(':visible')) 
   {
    $('body').css("overflow", "hidden")
    } 

        $("#page-title-button, #nav-menu-caret, .nav-subheader").click(function () {
            $(".nav-container").toggle();
            $("#guides-nav").toggle();
            $(".guides .sticky-container").height("100%");
        });
      }

      $('.nav-subheader').click(function() {
        
      } );
    //calc offset from page-title-button and offset top of anchor x pix
  
});