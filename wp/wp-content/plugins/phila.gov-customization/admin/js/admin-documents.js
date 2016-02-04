jQuery(document).ready(function($){
  /*documents page specific */
  if ( typeof typenow === 'undefined'){
    return;

  }else{

    if ( ( typenow == 'document') && adminpage.indexOf('post') > -1 ){

      $('.rwmb-add-media').text('+ Upload Files');

      $( "#phila_document_description" ).rules( "add", {
        maxlength: 350, required: true
      });

      $('.rwmb-date').datepicker();
      if($(".rwmb-date").datepicker("getDate") === null) {
        $('.rwmb-date').datepicker('setDate', new Date());
      }
      var $eventSelect = $('.rwmb-select-advanced');
    }
  }
});
