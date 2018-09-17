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

  if(Foundation.MediaQuery.current == 'small') {
    $('.accordion').foundation('up', $('.accordion-content'));
  }

});
