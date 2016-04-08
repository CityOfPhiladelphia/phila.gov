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

    if ( ( typenow == 'department_page') && adminpage.indexOf('post') > -1 ){
      var colTypes = {};
      var rowOneColOneType = $('#phila_module_row_1_col_1_type').val();
      var rowOneColOneOptions = '#phila_module_row_1_col_1_type';
      var rowOneColTwoType = $('#phila_module_row_1_col_2_type').val();
      var rowOneColTwoOptions = '#phila_module_row_1_col_2_type';

      colTypes.colOne = {'loc':rowOneColOneOptions,'type':rowOneColOneType};
      colTypes.colTwo = {'loc':rowOneColTwoOptions,'type':rowOneColTwoType};

      function hideOptions(c){
        for (i in c){
          if (c[i]['type'] == ''){
            $(c[i]['loc']).closest('.rwmb-group-wrapper').find('.rwmb-group-wrapper').css('display','none');
          }
          else if (c[i]['type'] == 'phila_module_row_1_col_1_blog_posts'){
            $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_1_col_1_textarea').css('display','none');
          }
          else if (c[i]['type'] == 'phila_module_row_1_col_1_custom_text'){
            $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_1_col_1_post_style').css('display','none');
          };
        }
      };

      hideOptions(colTypes);

      console.log(colTypes[1]);
      console.log(colTypes.length);
      // $('#phila_module_row_1_col_1_type').change(function() {
      //   if( $(this).val() == 'phila_module_row_1_col_1_blog_posts' ) {
      //     $(this).closest('.rwmb-group-wrapper').find('.rwmb-group-wrapper').css('display','block');
      //   }
      // });
      // $('#phila_module_row_1').find('.rwmb-group-wrapper').find('.rwmb-group-wrapper').css('display','none');
      // $('#phila_module_row_1').find('input', '.rwmb-file-input-select').prop('disabled', true);

    }
  }
});
