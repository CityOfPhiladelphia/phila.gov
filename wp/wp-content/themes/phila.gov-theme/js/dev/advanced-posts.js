module.exports = $(function () {
  function showSlides2() {
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

  $(document).ready(function () {
    showSlides2();
  });
});
