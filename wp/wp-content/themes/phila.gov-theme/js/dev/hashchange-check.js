function anchorOffset() {
  var $anchor = $(':target');
  var fixedElementHeight = $('.sticky').outerHeight();
  console.log("anchoring offset");
  console.log('$anchor.length:', $anchor.length);
  console.log('$anchor.offset().top:', $anchor.offset().top);
  console.log('fixedElementHeight:', fixedElementHeight);

  if ($anchor.length > 0)
    window.scrollTo(0, $anchor.offset().top - fixedElementHeight);
}

// $(window).on('hashchange load', anchorOffset());
$(window).on('hashchange load', setTimeout(() => {
  anchorOffset();
}, 250));

$('body').on('click', "a[href^='#']", function (ev) {
  if (window.location.hash === $(this).attr('href')) {
    ev.preventDefault();
    anchorOffset();
  }
});

if (window.location.hash) {
  $(window).trigger('hashchange');
}