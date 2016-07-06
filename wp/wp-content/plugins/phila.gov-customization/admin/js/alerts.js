/*
* Admin Alerts Custom Js
*
*/
jQuery(document).ready(function($) {
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
