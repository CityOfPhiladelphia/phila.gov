/* loads for users who do not have the PHILA_ADMIN capability */

jQuery(document).ready(function($){
  if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(obj, start) {
       for (var i = (start || 0), j = this.length; i < j; i++) {
           if (this[i] === obj) { return i; }
       }
       return -1;
     }
   }

  //Force top category to be checked all the time, unless the user has access to mutiple categories
  if( !phila_WP_User.includes('multi_department_access') && !phila_WP_User.includes('secondary_all_departments')) {
    var required_cat = $('#categorychecklist > li:first-child input');
    if( !required_cat.attr('checked')  ) {
      required_cat.attr('checked','checked');
    }
  }
  //Force contributrors to add email for review
  if ( phila_WP_User.includes('secondary_department_page_contributror') || phila_WP_User.includes('secondary_service_page_contributor') || phila_WP_User.includes('secondary_programs__initiatives_contributor') ){
    $('#dem_notify_emails').prop('required', 'required')
  }

  //Don't allow non-admins or editors to create new pages from a duplicated page
  if (!phila_WP_User.includes('administrator') || !phila_WP_User.includes('editor')){
    $('#save_as_new').css('display', 'none')
  }


  //If department "contributor" doesn't have access to this post type, hide the publish button, allow publishing action on document pages
  if ( ( typenow != 'document') && adminpage.indexOf('post') > -1 ){
    phila_WP_User.some(
      function(v){
        if ( v.indexOf(typenow) >= 0) {
          if ($('#publish').val() === 'Publish' || $('#publish').val() === 'Update') {
            $('#publish').css('display', 'none')
          }
        }
      }
    )
  }

  //Hide all category and tag menu items, department authors shouldn't see those.
  $('a[href*="edit-tags.php"]').parent().css('display', 'none');

  var menuIdString = $('#menu-id').text().trim();
  var allMenuIDs = menuIdString.split(' ');
  var match = document.getElementById( allMenuIDs );

  //Hide all menu locations
  $('.menu-theme-locations input').parent().css('display', 'none');

  //Display menu locations that match current user roles
  for (var i = 0; i < allMenuIDs.length ; i++) {
    var currentMenuId = document.getElementById( allMenuIDs[i] );
    $(currentMenuId).parent().css('display', 'block');
  }

  var menuNameString = $('#menu-name').text().trim();
  var allMenuNames = menuNameString.split(' ');

  //Show menus that match current user roles
  for (var i = 0; i < allMenuNames.length ; i++) {
    var currentMenuName = allMenuNames[i];
    $( '.manage-menus option:contains("" + currentMenuName + "")').show();
  }
  //Add correct menu classes to 'nav menu' link
  var currentURL = window.location.pathname;

  if (currentURL.indexOf('nav-menus') > -1){
    $('#menu-posts-department_page').removeClass('wp-not-current-submenu');
    $('#menu-posts-department_page').addClass('wp-has-current-submenu wp-menu-open menu-top');
    $('.wp-submenu-wrap li:last-child').addClass('current');
    $('.menu-icon-department_page').removeClass('wp-not-current-submenu');
    $('.menu-icon-department_page').addClass('wp-has-submenu wp-has-current-submenu wp-menu-open');
  }

  if ( typeof typenow === 'undefined'){
    return;

  }else{

    if ( ( typenow == 'attachment') && adminpage.indexOf('post') > -1 ){
      $('#post').validate({
        rules: {
           'post_title' : 'required'
         }
      });
      $( '#attachment_content' ).rules( 'add', {
        maxlength: 225, required: true
      });
    }

    if ( ( typenow == 'department_page') && adminpage.indexOf('post') > -1 ){
      $('[id^=phila_block_id]').parent().parent().hide();
      //hide short description and let users know what they can do to change it
      $('#phila_meta_desc').after( "<i>To request a change to the short description, email <a href='mailto:oddt@phila.gov'>oddt@phila.gov</a>.</i>" )
    }
  }

  if ( ( typenow == 'service_updates') && adminpage.indexOf('post') > -1 ){
    if( !phila_WP_User.includes('multi_department_access') ) {

      $('#phila_update_type option').each( function () {
        if( $(this).val() !== '' && $(this).val() !== 'phones' &&  $(this).val() !== 'offices'){
          $(this).css('display', 'none');
        }
      });
      $('#phila_update_level option').each( function () {
        //2 === Critcal (Red)
        if( $(this).val() !== '' && $(this).val() !== '2' ){
          $(this).css('display', 'none');
        }
      });
    }
  }

  if ( ( typenow == 'programs') && adminpage.indexOf('post') > -1 ){
    $('#phila_meta_desc').after( "<i>To request a change to the short description, email <a href='mailto:oddt@phila.gov'>oddt@phila.gov</a>.</i>" )
  }

  if ( ( typenow == 'post') && adminpage.indexOf('post') > -1 ){
    $('#phila_template_select option').each( function () {
      if( $(this).val() !== '' && $(this).val() !== 'post' ){
        $(this).css('display', 'none');
      }
    });
    if( phila_WP_User.includes('secondary_press_release_editor') || phila_WP_User.includes('secondary_press_release_contributor') ) {
      $('#phila_template_select option').each( function () {
        if( $(this).val() === 'press_release' ){
          $(this).css('display', 'inline-block');
        }
      });
      $("#phila_template_select").val('post');
    }
    if( phila_WP_User.includes( 'secondary_action_guide_editor' ) ) {
      $('#phila_template_select option').each( function () {
        if( $(this).val() === 'action_guide' ){
          $(this).css('display', 'inline-block');
        }
      });
    }

  }
  if ( ( typenow == 'department_page') && adminpage.indexOf('post') > -1 ){

    $('#wp-module_row_1_col_1_module_row_1_col_1_options_phila_module_row_1_col_1_textarea-wrap').after( "<i>To request a change to 'What we do' content, email <a href='mailto:oddt@phila.gov'>oddt@phila.gov</a>.</i>" )

  }

});
