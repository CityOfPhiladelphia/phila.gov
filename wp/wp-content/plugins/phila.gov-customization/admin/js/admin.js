function phila_get_user_roles_callback() {
  if (phila_WP_User.includes('multi_department_access') || phila_WP_User.includes('administrator') || phila_WP_User.includes('editor') ){
    return true;
  }else{
    return false;
  }
}


/* For all admins */
jQuery(document).ready(function($) {

  $('[data-readonly="true"]').attr('readonly','readonly');

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
      $('#phila_meta_desc').rules('add', {
        maxlength: 140
      });
    }

    if ( ( typenow == 'document' ) && adminpage.indexOf( 'post' ) > -1 ) {

      $('.rwmb-date').datepicker();
      if ($(".rwmb-date").datepicker("getDate") === null) {

        $('.rwmb-date').val($.datepicker.setDefaults( $.datepicker.regional[ "" ]) );

        $('.rwmb-date').val($.datepicker.formatDate('MM dd, yy', new Date()));
      }

    }
    if ( typenow == 'staff_directory' && adminpage.indexOf( 'post' ) > -1 ) {
      $('#phila_first_name').prop('required', true);
      $('#phila_last_name').prop('required', true);
      $('#phila_job_title').prop('required', true);
      $('#phila_display_order').attr('data-msg', 'This field is required and must be a number.');
      $('#phila_display_order').rules('add', {
        required: true,
        //Check whether this value is a number, if not return a failing value.
        normalizer: function( value ) {
          if ( !isNaN( value ) ){
            return ( value );
          } else {
            return '';
          }
        }
      });
    }
  }
  function setOffSiteInputVals(){
    $( '#phila_department_home_page' ).prop( 'checked', true );
    $('.postarea').hide();
  }

  if ( ( typenow == 'department_page' ) && adminpage.indexOf( 'post' ) > -1 )  {
    var templateSelect = $('#phila_template_select');

    //Set character limits for hero-taglines
    $('#phila_hero_header_title_l1').rules( 'add' , {
      maxlength: 20
    });
    $('#phila_hero_header_title_l2').rules( 'add' , {
      maxlength: 15
    });

    if ( templateSelect.val() == 'off_site_department' ){
      setOffSiteInputVals();
      $('#phila_template_select').click();
    }

    if ( templateSelect.val() == 'forms_and_documents_v2' ){
      $( '[id^=phila_action_panel_]' ).prop('required', true);
    }

    templateSelect.change(function() {
      //set fields based on template selection
      if( templateSelect.val() == 'off_site_department'){
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
    //set character lengths for survey module
    $( '#survey_title' ).rules( 'add', {
      maxlength: 50
    });
    $( '#survey_description' ).rules( 'add', {
      maxlength: 140
    });

  }

  if ( ( typenow == 'service_updates' ) && adminpage.indexOf( 'post' ) > -1 )  {
    $.validator.setDefaults({
      ignore: ''
    });
    $('#phila_update_type').prop('required', true);
    $('#phila_update_level').prop('required', true);
    $('#phila_date_format').prop('required', true);
    $('[id^=phila_effective_start_date]').prop('required', true);
    $('[id^=phila_effective_end_date]').prop('required', true);
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
  if ( ( typenow == 'post' ) && adminpage.indexOf( 'post' ) > -1 )  {
    $( '#phila_social_intent' ).rules( 'add', {
      maxlength: 116
    });
  }


});
