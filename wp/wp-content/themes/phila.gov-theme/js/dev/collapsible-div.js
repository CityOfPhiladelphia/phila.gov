module.exports = $(function(){
  // Staff summary expand
  $('[data-toggle="data-staff-bio"]').click(function(e){
    e.preventDefault();
    $(this).parent().siblings().toggleClass('expandable');
    if($(this).html() === ' Expand + '){
      $(this).html(' Collapse - ');
    } else {
      $(this).html(' Expand + ');
    }
  });
});
