module.exports = $(function(){
  // Any div summary expand
  $('[data-toggle="expandable"]').click(function(e){
    e.preventDefault();
    $(this).siblings().toggleClass('expandable');
    $(this).prev().attr('aria-expanded', 'true');

    if($(this).html() === ' More + '){
      $(this).html(' Less - ');
      $(this).prev().attr('aria-expanded', 'true');
    } else {
      $(this).html(' More + ');
      $(this).prev().attr('aria-expanded', 'false');

    }
  });

  $('[data-toggle="icon-expand"]').click(function(e){
    e.preventDefault();
    $(this).next().attr('aria-expanded', 'true');
    $(this).next().toggleClass('visible');

    if($(this).html() === ' More + '){
      $(this).html(' Less - ');
      $(this).next().attr('aria-expanded', 'true');
    } else {
      $(this).html(' More + ');
      $(this).next().attr('aria-expanded', 'false');

    }
  });

  $('[data-toggle="expandable-all"]').click(function(e){
    e.preventDefault();
    var parent = '';
    var parentId = $(this).closest('.small-24.columns.results.mbm').attr('id');
    if (parentId != undefined) {
      parent = '#'+parentId;
    }

    if($(this).html() === ' Expand All + '){
      $(this).html(' Collapse All - ');
      $(parent+' .icon-expand-content').each(function () {
        $(this).attr('aria-expanded', 'true');
        if (!$(this).hasClass("visible")) {
          $(this).addClass('visible');
        }
      });
      $(parent+' .icon-expand-link').each(function () {
        if($(this).html() === ' More + '){
          $(this).html(' Less - ');
          $(this).prev().attr('aria-expanded', 'true');
        }
      });
    } else {
      $(this).html(' Expand All + ');
      $(parent+' .icon-expand-content').each(function () {
        $(this).attr('aria-expanded', 'false');
        if ($(this).hasClass("visible")) {
          $(this).removeClass('visible');
        }
      });
      $(parent+' .icon-expand-link').each(function () {
        if($(this).html() === ' Less - '){
          $(this).html(' More + ');
          $(this).prev().attr('aria-expanded', 'false');
        }
      });
    }
  });
});
