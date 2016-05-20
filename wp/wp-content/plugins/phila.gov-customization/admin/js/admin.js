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

    // if ( ( typenow == 'department_page') && adminpage.indexOf('post') > -1 ){
    //     var colTypes = {};
    //
    //     function setColInfo(){
    //
    //       for (var row = 1; row < 3; row++){
    //         for(var col = 1; col < 3; col++){
    //           var moduleType = $('#phila_module_row_' + row + '_col_' + col + '_type').val();
    //           var moduleOptions = '#phila_module_row_' + row + '_col_' + col + '_type';
    //           colTypes['row'+row+'Col'+col] = {'row':row, 'col':col, 'loc':moduleOptions,'type':moduleType};
    //         };
    //       };
    //
    //     };
    //
    //     function hideOptions(c){
    //
    //         for (i in c){
    //           if ( c[i]['row']=== 1){
    //             $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_texttitle').closest('.rwmb-text-wrapper').hide();
    //             $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_textarea').closest('.rwmb-textarea-wrapper').hide();
    //             $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_post_style').closest('.rwmb-select-wrapper').hide();
    //
    //             if (c[i]['type'] === 'phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_blog_posts'){
    //               $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_post_style').closest('.rwmb-select-wrapper').toggle();
    //             }
    //             else if (c[i]['type'] === 'phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_custom_text'){
    //               $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_texttitle').closest('.rwmb-text-wrapper').toggle();
    //               $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_textarea').closest('.rwmb-textarea-wrapper').toggle();
    //             }
    //           }
    //         else if ( c[i]['row']=== 2){
    //           // Hide Calendar ID option
    //           $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_cal_id').closest('.rwmb-text-wrapper').hide();
    //           // Hide Calendar URL option
    //           $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_cal_url').closest('.rwmb-url-wrapper').hide();
    //
    //           if (c[i]['type'] === 'phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_calendar'){
    //             // Toggle Calendar ID option
    //             $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_cal_id').closest('.rwmb-text-wrapper').toggle();
    //             // Toggle Calendar URL option
    //             $(c[i]['loc']).closest('.rwmb-group-wrapper').find('#phila_module_row_' + c[i]['row'] + '_col_' + c[i]['col'] + '_cal_url').closest('.rwmb-url-wrapper').toggle();
    //           }
    //         }
    //       }
    //     };
    //
    //     setColInfo();
    //     hideOptions(colTypes);
    //
    //     $( "#phila_module_row_1_col_1_type, #phila_module_row_1_col_2_type, #phila_module_row_2_col_1_type, #phila_module_row_2_col_2_type" ).change(function() {
    //       setColInfo();
    //       hideOptions(colTypes);
    //     });
    //
    //   }

  }
});
