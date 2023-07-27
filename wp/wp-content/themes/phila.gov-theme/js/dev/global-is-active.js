module.exports = jQuery(document).ready(function ($) {
  var currentPath = window.location.pathname;

  $($('.global-nav a')).each(function () {
    if (currentPath === $(this).attr('href') || currentPath === $(this).data('link')) {

      $(this).addClass('js-is-current');
    } else if (currentPath.indexOf('/services/') === 0 || currentPath.indexOf('/services/') === 1) {
      $('.serviceså-menu-link a').addClass('js-is-current');
    } else if (currentPath.indexOf('/programs/') === 0 || currentPath.indexOf('/programs/') === 1) {
      $('.programs-menu-link a').addClass('js-is-current');
    } else if (currentPath.indexOf('/departments/') === 0 || currentPath.indexOf('/departments/') === 1) {
      $('.departments-menu-link a').addClass('js-is-current');
    } else if (currentPath.indexOf('/the-latest/') === 0 || currentPath.indexOf('/the-latest/') === 1) {
      $('.news-menu-link a').addClass('js-is-current');
    } else if (currentPath.indexOf('/tools/') === 0 || currentPath.indexOf('/tools/') === 1) {
      $('.tools-menu-link a').addClass('js-is-current');
    } else if (currentPath.indexOf('/documents/') === 0 || currentPath.indexOf('/documents/') === 1) {
      $('.publications-menu-link a').addClass('js-is-current');
    }
  });
});
