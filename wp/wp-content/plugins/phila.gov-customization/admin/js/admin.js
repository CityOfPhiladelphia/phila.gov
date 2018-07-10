function phila_get_user_roles_callback() {
  if (phila_WP_User.includes('multi_department_access') || phila_WP_User.includes('administrator') || phila_WP_User.includes('editor') || phila_WP_User.includes('secondary_all_departments') ){
    return true;
  }else{
    return false;
  }
}
function phila_user_read_only(){
  if ( phila_WP_User.includes( 'primary_admin_read_only' ) ){
    return true;
  }
}

/* For all admins */
jQuery(document).ready(function($) {

  //disable dupliate action on document pages, document meta not saving state propery.
  if ( ( typenow === 'document') && adminpage.indexOf('post') > -1 ){
    $('#duplicate-action').css('display', 'none')
  }


  // Set error placement, and highlights for category selection
  jQuery.validator.setDefaults({
    errorPlacement: function( error, element ) {
      if( error.attr('id').indexOf('post_category') > -1 ) {
          error.insertAfter( $( element ).parents('.categorydiv').eq(0) );
      } else {
          error.insertAfter( element );
      }
    },
    highlight: function( element, errorClass ) {
      if( jQuery( element ).attr('name').indexOf('post_category') > -1 ) {
        jQuery( element ).parents('.categorydiv').eq(0).addClass( errorClass );
      } else {
        jQuery( element ).addClass( errorClass );
      }
    },
    unhighlight: function( element, errorClass ) {
      if( jQuery( element ).attr('name').indexOf('post_category') > -1 ) {
        jQuery( element ).parents('.categorydiv').eq(0).removeClass( errorClass );
      } else {
        jQuery( element ).removeClass( errorClass );
      }
    }
  });

  $('[data-readonly="true"]').attr('readonly','readonly');

  if ( typeof typenow === 'undefined' ) {
    return;

  } else {

    // Remove slug on attachment to prevent clicks on the link
    if ( typenow == 'attachment' && adminpage.indexOf( 'post' ) > -1 ) {
      jQuery( '#edit-slug-box' ).hide();
    }

    if ( ( typenow != 'attachment' ) && adminpage.indexOf( 'post' ) > -1 )  {

      $('#post').validate({
        rules: {
          'post_title': 'required'
        }
      });
      //Don't allow editing of title field when duplicated and increase text limit so validation won't prevent save of draft, but not on staff directory where there is no title field
      if ( typenow != 'staff_directory') {

        if( $( "#title" ).val().indexOf('[Duplicated]') != -1){
          $('#title').rules('add', {
              maxlength: 72 + 14
            });
            $( "#title" ).attr('disabled', true);
            $( "<div style='color:#838383; padding-left:5px;'>This field isn't avilable to edit. To change the title, save as a new item.</div> " ).insertAfter('#title');

        }else{
        $('#title').rules('add', {
            maxlength: 72
          });
        }
        $('#phila_meta_desc').rules('add', {
          maxlength: 140
        });

      }
      // Set validations for custom post type Service Page
      if ( typenow == 'service_page' && adminpage.indexOf( 'post' ) > -1 ) {
        $('select[name="parent_id"]' ).rules( 'add', {
          'required': true
        });

        // HOW TO VALIDATE A REQUIRED TAXONOMY!
        // jQuery( "input[name='tax_input[service_type][]']" ).rules( 'add', {
        //       'required': true
        //     }
        // );
      }
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
      maxlength: 256
    });

  }
  //Force category selection on all content types
  if ( ( typenow != 'attachment' ) && adminpage.indexOf( 'post' ) > -1 ) {

    $( 'input[name="post_category[]"]' ).rules( 'add', {
         'required': true
       }
     );
  }

  /*
  * Intercepts the ajax response sent from Apperance -> Menu -> Departments search results and adds the upmost parent of each child page found. This should make it easier to identify child pages that have the same name.
  */

  if($('body').hasClass('nav-menus-php')){
    $(document).ajaxSuccess(function(event, request, settings){

      var params = settings.data;
      var action = params.match(/action=(.+?)&/);
      var type = params.match(/&type=(.+)/);

      if(action[1] == 'menu-quick-search' && type[1] == 'quick-search-posttype-department_page') {
        var $responseText = $(request.responseText);
        var $checkboxes = $responseText.find('.menu-item-checkbox');
        if ($checkboxes.length > 0){
          var deptPageIds = [];
          $checkboxes.each(function(){
            deptPageIds.push($(this).val());
          });
          if(deptPageIds.length > 0){
            updateResponseCheckboxes(deptPageIds);
          }
        }
      }
      function updateResponseCheckboxes(postIds){
        $.ajax({
          url: searchAjax.ajaxurl,
          dataType: 'json',
          type: 'POST',
          data: {
            action: 'addDepartmentParent',
            postIds: postIds,
            security: searchAjax.ajax_nonce,
          }
        }).success(function(response) {
          if (response) {
            $.each(response, function(id, parent){
              value = id.replace("p=", "");
              parent = "<small class='pparent'><span>Parent:</span> " + parent + "</small>";
              $('#department_page-search-checklist .menu-item-title input[value="' + value + '"]').parent("label").append(parent);
            });
          }
        });
      }
    });
  }

});
