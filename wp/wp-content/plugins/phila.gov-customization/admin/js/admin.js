function phila_get_user_roles_callback() {
  if (phila_WP_User.includes('multi_department_access') || phila_WP_User.includes('administrator') || phila_WP_User.includes('editor') || phila_WP_User.includes('secondary_all_departments') ){
    return true;
  }else{
    return false;
  }
}
function phila_get_user_roles_is_admin() {
  return phila_WP_User.includes('administrator');
}
function phila_user_read_only(){
  if ( phila_WP_User.includes( 'primary_admin_read_only' ) ){
    return true;
  }
}

/* For all admins */
jQuery(document).ready(function($) {

  var currentURL = window.location.href;

if (currentURL.indexOf('edit-tags.php') > -1) {
    //remove news highlighting
    $('.menu-icon-post').removeClass('wp-has-current-submenu wp-menu-open');
    $('.menu-icon-post').addClass('wp-not-current-submenu');
    //remove service page highlighting
    $('.menu-icon-service_page').removeClass('wp-has-current-submenu wp-menu-open');
    $('.menu-icon-service_page').addClass('wp-not-current-submenu');

    if(currentURL.indexOf('edit-tags.php?taxonomy=post_tag') > -1){
      //add tags highlighting
      $('.toplevel_page_edit-tags\\?taxonomy\\=post_tag').removeClass('wp-not-current-submenu');
      $('.toplevel_page_edit-tags\\?taxonomy\\=post_tag').addClass('wp-has-current-submenu wp-menu-open');
    }

    if (currentURL.indexOf('edit-tags.php?taxonomy=service_type&post_type=service_page') > -1) {
      //add categories highlighting
      $('.toplevel_page_edit-tags\\?taxonomy\\=service_type\\&post_type\\=service_page').removeClass('wp-not-current-submenu');
      $('.toplevel_page_edit-tags\\?taxonomy\\=service_type\\&post_type\\=service_page').addClass('wp-has-current-submenu wp-menu-open');
    }

    if(currentURL.indexOf('edit-tags.php?taxonomy=audience') > -1){
      //add audience highlighting
      $('.toplevel_page_edit-tags\\?taxonomy\\=audience').removeClass('wp-not-current-submenu');
      $('.toplevel_page_edit-tags\\?taxonomy\\=audience').addClass('wp-has-current-submenu wp-menu-open');
    }

    if(currentURL.indexOf('edit-tags.php?taxonomy=category') > -1){
      //add categories highlighting
      $('.toplevel_page_edit-tags\\?taxonomy\\=category').removeClass('wp-not-current-submenu');
      $('.toplevel_page_edit-tags\\?taxonomy\\=category').addClass('wp-has-current-submenu wp-menu-open');
    }

}

$('#menu-posts-calendar a:contains("All Calendars")').text('All calendars');
$('#menu-posts-calendar a:contains("Add New")').text('Add calendar');

$('#menu-posts-text-blocks div:contains("Text Blocks")').text('Text blocks');
$('#menu-posts-text-blocks a:contains("All Text Blocks")').text('All text blocks');
$('#menu-posts-text-blocks a:contains("Add New")').text('Add text block');

$('#adminmenu a:contains("Nested View")').text('Nested view');

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

    // Change "Submit for review" to "Set to pending" for post and document
    if ( typenow == 'post' || typenow == 'document') {
      if(phila_WP_User.includes('secondary_blog_contributor') || phila_WP_User.includes('secondary_advanced_post_contributor') || phila_WP_User.includes('secondary_document_page_contributor')) {
        $('#publish').val("Set to pending");
      }
    }
    
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
      if ( typenow != 'staff_directory' ) {

        if( $( "#title" ).val().indexOf('[Duplicated]') != -1){
          $('#title').rules('add', {
              maxlength: 72 + 14
            });
            $( "#title" ).attr('disabled', true);
            $( "<div style='color:#838383; padding-left:5px;'>This field isn't available to edit. To change the title, save as a new item.</div> " ).insertAfter('#title');

        } else {
          if (typenow == 'post'){
            return;
          } else if ( typenow == 'department_page' ) {
            $('#title').rules('add', {
              maxlength: 144
            });
          } else {
            $('#title').rules('add', {
              maxlength: 72
            });
          }
        }
      }
      // Set validations for custom post type Service Page
      if ( typenow == 'service_page' && adminpage.indexOf( 'post' ) > -1 ) {
        $('select[name="parent_id"]' ).rules( 'add', {
          'required': true
        });

      }
    }
    if ( ( typenow == 'post' ) && adminpage.indexOf( 'post' ) > -1 ) {

      if ($(".rwmb-date").datepicker().datepicker("getDate") === null) {

        $(".rwmb-date").datepicker().datepicker("setDate", new Date());
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
