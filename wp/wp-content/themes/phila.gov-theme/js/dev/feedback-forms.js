//Homepage Feedback Form
module.exports = jQuery(document).ready(function($) {
  $(".neighborhood-resources .feedback").on('click', function(){
    $(this).feedbackify('https://form.jotform.com/jsform/62516788470970');
  });

});
