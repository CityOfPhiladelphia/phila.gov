function anchorOffset() {
  var $anchor = $(':target');
  var fixedElementHeight = $('.sticky-container').outerHeight() ? $('.sticky-container').outerHeight() : 0;
  
  if ($anchor.length > 0) {
    window.scrollTo(0, $anchor.offset().top - fixedElementHeight);
  }
}

  $(window).on('hashchange load', function() {
    setTimeout(function() {
      anchorOffset();
    }, 500);
  });

$('body').on('click', "a[href^='#']", function (ev) {
  if (window.location.hash === $(this).attr('href')) {
    ev.preventDefault();
    anchorOffset();
  }
});

// if (window.location.hash) {
//   $(window).trigger('hashchange');
// }