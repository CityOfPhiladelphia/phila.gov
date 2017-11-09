module.exports = $(function(){
  // Any div summary expand
  $('[data-toggle="data-expandable"]').click(function(e){
    e.preventDefault();
    $('this').parent('collapsible').toggleClass('expandable');
    if($(this).html() === ' Expand + '){
      $(this).html(' Collapse - ');
    } else {
      $(this).html(' Expand + ');
    }
  });
});
