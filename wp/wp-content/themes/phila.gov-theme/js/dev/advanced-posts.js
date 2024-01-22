module.exports = $(function () {
  function showSlides() {
    var containers = $(".slideshow-container");

    containers.each(function () {
      var container = $(this);
      var slides = container.find(".mySlides");
      var dots = container.find(".dots .dot");

      var currentIndex = 0;

      function showSlide(index) {
        slides.eq(currentIndex).removeClass("active");
        currentIndex = index;
        slides.eq(currentIndex).addClass("active");
      }

      container.find(".next").click(function () {
        var nextIndex = (currentIndex + 1) % slides.length;
        showSlide(nextIndex);
        updateDot(nextIndex);
      });

      container.find(".prev").click(function () {
        var prevIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(prevIndex);
        updateDot(prevIndex);
      });

      dots.click(function () {
        var dotIndex = dots.index(this);
        showSlide(dotIndex);
        updateDot(dotIndex);
      });

      function updateDot(index) {
        dots.removeClass("active");
        dots.eq(index).addClass("active");
      }
    });
  }

  $('.lightbox-link').click(function() {
    var imageId = $(this).data('open');
    var closeButton = "<button class=\"close-button\" data-close aria-label=\"Close modal\" type=\"button\"><span aria-hidden=\"true\">&times;</span></button>";
    $("#"+imageId).on("open.zf.reveal", function (e) {
            var $modal = $(this);
            $modal.html("Loading...");
            var imageUrl = $modal.data("image-url");
            var imageCaption = $modal.data("media-caption");
            var imageCredit = $modal.data("media-credit");
    
            $modal.html('<div class="lightbox-content"><img src="' + imageUrl + '" alt=""></div>');
    
            if (imageCredit) {
              var featuredCredit = document.createElement("p");
              featuredCredit.innerHTML =
                "<strong>Photo by: " + imageCredit + "</strong>";
              $modal.append(featuredCredit);
            }
            if (imageCaption) {
              var featuredCaption = document.createElement("p");
              featuredCaption.innerHTML = imageCaption;
              $modal.append(featuredCaption);
            }
            $modal.append(closeButton);
          });
  });

  $(document).ready(function () {
    showSlides();
  });
});
