jQuery(document).ready(function($){
  /*documents page specific */
  if ( typeof typenow === 'undefined'){
    return;

  }else{

    if ( ( typenow == 'document') && adminpage.indexOf('post') > -1 ){
      $('.rwmb-add-media').text('+ Upload Files');

      $( '#title' ).rules( 'add', {
        maxlength: 72
      });

      var $eventSelect = $('.rwmb-select-advanced');
    }
  }
});
