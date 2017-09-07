function anchorOffset() {
  /* Account for sticky header when anchor links are present */
  var $anchor = $(':target');
  var fixedElementHeight = $('.sticky').outerHeight();
  if ($anchor.length > 0)
    window.scrollTo(0, $anchor.offset().top - fixedElementHeight);
}
$(window).on('hashchange load', anchorOffset);
$('body').on('click', "a[href^='#']", function (ev) {
  if (window.location.hash === $(this).attr('href')) {
    ev.preventDefault();
    anchorOffset();
  }
});

if (window.location.hash) {
  $(window).trigger('hashchange');
}
