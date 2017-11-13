module.exports = $(function(){
  // Any div summary expand
  $('[data-toggle="expandable"]').click(function(e){
    e.preventDefault();
    $(this).siblings().toggleClass('expandable');
    if($(this).html() === ' Expand + '){
      $(this).html(' Collapse - ');
    } else {
      $(this).html(' Expand + ');
    }
  });
});
