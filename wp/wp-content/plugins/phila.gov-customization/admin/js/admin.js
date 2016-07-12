/* For all admins */
jQuery(document).ready(function($) {

  //Rename Pages to "Information Pages"
  //This is here because of permissions weirdness as well as timing problems
  $('#adminmenuwrap .wp-submenu a').each(function(i) {
    if ($(this).attr("href") == "edit.php?post_type=page") {
      $(this).text("Information Page");
    } else if ($(this).attr("href") == "post-new.php?post_type=page") {
      $(this).text("Add Information Page");
    }
  });

  //no one can clone rn
  $('#department-content-blocks .add-clone').css('visibility', 'hidden');

  if ( $('.misc-pub-attachment input[value*=".pdf"]').val() ) {
    $('.post-type-attachment #categorydiv input').prop('disabled', true);
    $('.post-type-attachment #publication_typediv input').prop('disabled', true);
  }
  if ( typeof typenow === 'undefined' ) {
    return;

  } else {

    if ( philaAllPostTypes.indexOf( typenow ) !== -1 && adminpage.indexOf( 'post' ) > -1 ) {
      $('#post').validate({
        rules: {
          'post_title': 'required'
        }
      });
      $('#title').rules('add', {
        maxlength: 72
      });
    }

    if ( typenow == 'news_post' && adminpage.indexOf( 'post' ) > -1 ) {
      $('#phila_news_desc').rules('add', {
        required: true
      });
    }

    if ( typenow == 'phila_post' && adminpage.indexOf( 'post' ) > -1 ) {
      $('#phila_post_desc').rules('add', {
        required: true
      });
    }

    if ( ( typenow == 'page' ) && adminpage.indexOf( 'post' ) > -1 ) {

      if ( $("#page-display input[name=phila_show_in_browse]").length ) {

        $('#page-display input[name=phila_show_in_browse]').click(function() {

          if ($(this).val() == 'yes') {

            $('#page-display .rwmb-textarea-wrapper').show();

          } else if ($(this).val() == 'no') {

            $('#page-display .rwmb-textarea-wrapper').hide();
          }

        });

        if ( $('#page-display input[name=phila_show_in_browse]:checked').val() == 'yes' ) {

          $('#page-display .rwmb-textarea-wrapper').show();

        } else if ( $('#page-display input[name=phila_show_in_browse]:checked').val() == 'no' ) {

          $('#page-display .rwmb-textarea-wrapper').hide();

        }

        $("#phila_page_desc").prop('required', true);

      }

    }

    if ( typenow == 'phila_post' && adminpage.indexOf( 'post' ) > -1 ) {

      $('a#link-post_tag').click();

    }

    if ( ( typenow == 'press_release' || typenow == 'document' ) && adminpage.indexOf( 'post' ) > -1 ) {

      $('.rwmb-date').datepicker();
      if ($(".rwmb-date").datepicker("getDate") === null) {

        $('.rwmb-date').val($.datepicker.setDefaults( $.datepicker.regional[ "" ]) );

        $('.rwmb-date').val($.datepicker.formatDate('MM dd, yy', new Date()));
      }

    }
    if ( ( typenow == 'event_page') && adminpage.indexOf( 'post' ) > -1 ) {
      $("#post").validate({
        rules: {
          'post_title': 'required'
        }
      });
      $('#phila_event_desc').rules('add', {
        maxlength: 365,
        required:true
      });
      $('#phila_event_loc').rules('add', {
        required:true
      });
      $('#phila_event_permit_details').rules('add', {
        maxlength: 200
      });
      $('input[id^="phila_event_block_content_title"]').each(function (i, el) {
        $(this).rules('add', {
          maxlength: 70
        });
      });
      $('textarea[id^="phila_event_block_summary"]').each(function (i, el) {
        $(this).rules('add', {
          maxlength: 200
        });
      });
      $('textarea[id^="phila_service_update_message"]').each(function (i, el) {
        $(this).rules('add', {
          maxlength: 95
        });
      });
      $('input[id^="phila_update_link_text"]').each(function (i, el) {
        $(this).rules('add', {
          maxlength: 80
        });
      });

    }
    if ( typenow == 'staff_directory' && adminpage.indexOf( 'post' ) > -1 ) {
      $("#phila_first_name").prop('required', true);
      $("#phila_last_name").prop('required', true);
      $("#phila_job_title").prop('required', true);
      $('#phila_summary').rules('add', {
        maxlength: 700
      });
    }
  }
  function setOnePageInputVals(){
    //Hide row and column description. This can't be done using rwmb conditionals, because it will hide the whole group. 
    $('#phila_module_row_1_col_1_type').parent().parent().hide();

    $('#phila_module_row_1_col_1_type').val('phila_module_row_1_col_1_custom_text');

    $('#phila_module_row_1_col_2_type').val('phila_module_row_1_col_2_connect_panel');

    $("#phila_module_row_1_col_1_type option").each(function(){
      if ( $(this).val() != 'phila_module_row_1_col_1_custom_text' ){
        $(this).prop('disabled', true);
      }
    });

    $("#phila_module_row_1_col_2_type option").each(function(){
      if ( $(this).val() != 'phila_module_row_1_col_2_connect_panel' ){
        $(this).prop('disabled', true);
      }
    });

    $('#phila_module_row_1_col_1_texttitle').val('What We Do').prop( 'readOnly', true );

    $('#phila_module_row_1_col_1_textarea').rules('add', {
      maxlength: 850
    });
    $('[id^=phila_action_panel_summary_multi_]').rules('add', {
      maxlength: 180
    });
    $( '#phila_department_home_page' ).prop( 'checked', true );

    $('.postarea').hide();

  }
  function setOffSiteInputVals(){
    $( '#phila_department_home_page' ).prop( 'checked', true );
    $('.postarea').hide();
  }

  if ( ( typenow == 'department_page' ) )  {
    var templateSelect = $('#phila_template_select');

    if ( templateSelect.val() == 'one_page_department' ){
      setOnePageInputVals();
      $('#phila_template_select').click();
    }

    if ( templateSelect.val() == 'off_site_department' ){
      setOffSiteInputVals();
      $('#phila_template_select').click();

    }

    templateSelect.change(function() {
      //set fields based on template selection
      if ( templateSelect.val() == 'one_page_department' ){
        setOnePageInputVals();
      }else if( templateSelect.val() == 'off_site_department'){
        setOffSiteInputVals();

      }else{
        //enable the disabled elements
        $('#phila_module_row_1').each(function(){
          var inputs = $( this ).find( ':input' );
          var options = $( this ).find( ':input option' );
          inputs.prop( 'readOnly', false );
          options.prop( 'disabled', false );
        });
        //remove the rules specific to one_page_department
        $('#phila_module_row_1_col_1_textarea').rules('remove', 'maxlength');
        $('[id^=phila_action_panel_summary_multi_]').rules('remove', 'maxlength');
        $( '#phila_department_home_page' ).prop( 'checked', false );
        $('.postarea').show();
      }
    });
  }

});
