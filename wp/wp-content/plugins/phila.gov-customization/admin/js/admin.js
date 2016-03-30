/* For all admins */
jQuery(document).ready(function($){

  //Rename Pages to "Information Pages"
  //This is here because of permissions weirdness as well as timing problems
  $('#adminmenuwrap .wp-submenu a').each(function(i) {
    if($(this).attr("href") == "edit.php?post_type=page") {
        $(this).text("Information Page");
      }else if($(this).attr("href") == "post-new.php?post_type=page") {
        $(this).text("Add Information Page");
    }
  });

  //no one can clone rn
  $('#department-content-blocks .add-clone').css('visibility', 'hidden');

  if( $('.misc-pub-attachment input[value*=".pdf"]').val() ) {
    $('.post-type-attachment #categorydiv input').prop( 'disabled', true );
    $('.post-type-attachment #publication_typediv input').prop( 'disabled', true );
  }
  if ( typeof typenow === 'undefined'){
      return;

    }else{

    if ( (typenow == 'department_page' || typenow == 'document' || typenow == 'service_post') && adminpage.indexOf('post') > -1 ){
      $("#post").validate({
          rules: {
            'post_title' : 'required'
          }
        });
    }
    if (typenow == 'department_page' && adminpage.indexOf('post') > -1 ){
      if ( $( "#hero-header" ).length ){
        $( '#phila_hero_header_title' ).rules( 'add', {
          maxlength: 35
        });
        $( '#phila_hero_header_body_copy' ).rules( 'add', {
          maxlength: 140
        });
      }
    }
    if (typenow == 'news_post' && adminpage.indexOf('post') > -1 ){
      $('#post').validate({
        rules: {
           'post_title' : 'required'
         }
      });
      $( '#phila_news_desc' ).rules( 'add', {
        required: true
      });
    }

    if (typenow == 'phila_post' && adminpage.indexOf('post') > -1 ){
      $('#post').validate({
        rules: {
           'post_title' : 'required'
         }
      });
      $( '#phila_post_desc' ).rules( 'add', {
        required: true
      });
    }

    if ( ( typenow == 'page' || typenow == 'service_post' ) && adminpage.indexOf('post') > -1 ){
      $('#post').validate({
        rules: {
           'post_title' : 'required'
         }
      });

      if ( $( "#page-display input[name=phila_show_in_browse]" ).length ) {

        $('#page-display input[name=phila_show_in_browse]').click(function(){

          if ( $(this).val() == 'yes' ) {

            $('#page-display .rwmb-textarea-wrapper').show();

          }else if( $(this).val() == 'no' ){

            $('#page-display .rwmb-textarea-wrapper').hide();
          }

        });

        if ( $('#page-display input[name=phila_show_in_browse]:checked').val() == 'yes' ){

          $('#page-display .rwmb-textarea-wrapper').show();

        }else if ($('#page-display input[name=phila_show_in_browse]:checked').val() == 'no'){

          $('#page-display .rwmb-textarea-wrapper').hide();

        }

        $("#phila_page_desc").prop( 'required', true );

      }

    }

    if (typenow == 'phila_post' && adminpage.indexOf('post') > -1 ){

      $('a#link-post_tag').click();

    }

    if ( ( typenow == 'press_release' || typenow == 'document') && adminpage.indexOf('post') > -1 ){


      $('.rwmb-date').datepicker();
      if($(".rwmb-date").datepicker("getDate") === null) {

        $('.rwmb-date').val($.datepicker.formatDate('MM dd, yy', new Date()));
      }

    }

  }
});
