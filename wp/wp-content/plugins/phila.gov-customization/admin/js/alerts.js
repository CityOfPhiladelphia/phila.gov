/*
* Admin Alerts Custom Js
*
*/
jQuery(document).ready(function($) {
  /*show/hide metaboxes if alert type is "other" */
  $('.other-icon').hide();
  $('.type-other').hide();

  $('#phila_type').change(function(){
    if($('#phila_type').val() == 'Other') {
      $('.other-icon').show();
      $('.type-other').show();
    } else {
      $('.other-icon').hide();
      $('.type-other').hide();
    }
  });
  if($('#phila_type').val() == 'Other') {
    $('.other-icon').show();
    $('.type-other').show();
  }
  $("#post").validate({
    rules: {
       'post_title' : 'required'
     }
   });
   $( "#title" ).rules( "add", {
     maxlength: 70
   });
   $( ".start-time input" ).rules( "add", {
     required: true
   });
   $( ".end-time input" ).rules( "add", {
     required: true
   });
});
