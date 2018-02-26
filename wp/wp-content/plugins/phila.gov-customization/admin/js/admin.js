function phila_get_user_roles_callback() {
  if (phila_WP_User.includes('multi_department_access') || phila_WP_User.includes('administrator') || phila_WP_User.includes('editor') ){
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

  // Set error placement, and highlights for custom taxonomy checkboxes
  // MAYBE WE WILL NEED THIS IN THE FUTURE.
  // jQuery.validator.setDefaults({
  //   errorPlacement: function( error, element ) {
  //     if( error.attr('id').indexOf('tax_input') > -1 ) {
  //         error.insertAfter( $( element ).parents('.categorydiv').eq(0) );
  //     } else {
  //         error.insertAfter( element );
  //     }
  //   },
  //   highlight: function( element, errorClass ) {
  //     if( jQuery( element ).attr('name').indexOf('tax_input') > -1 ) {
  //       jQuery( element ).parents('.categorydiv').eq(0).addClass( errorClass );
  //     } else {
  //       jQuery( element ).addClass( errorClass );
  //     }
  //   },
  //   unhighlight: function( element, errorClass ) {
  //     if( jQuery( element ).attr('name').indexOf('tax_input') > -1 ) {
  //       jQuery( element ).parents('.categorydiv').eq(0).removeClass( errorClass );
  //     } else {
  //       jQuery( element ).removeClass( errorClass );
  //     }
  //   }
  // });

  $('[data-readonly="true"]').attr('readonly','readonly');

  if ( typeof typenow === 'undefined' ) {
    return;

  } else {

    // Remove slug on attachment to prevent clicks on the link
    if ( typenow == 'attachment' && adminpage.indexOf( 'post' ) > -1 ) {
      jQuery( '#edit-slug-box' ).hide();
    }
    //remove attachment from post type list
    delete philaAllPostTypes[2];

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
      maxlength: 256
    });

  }

  /*
  * Intercepts the ajax response sent from Apperance -> Menu -> Departments search results and adds the upmost parent of each child page found. This should make it easier to identify child pages that have the same name".
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
          url: myAjax.ajaxurl,
          dataType: 'json',
          type: 'POST',
          data: {
            action: 'addDepartmentParent',
            postIds: postIds,
            security: myAjax.ajax_nonce,
          }
        }).success(function(response) {
          if (response) {
            $.each(response, function(id,parent){
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
