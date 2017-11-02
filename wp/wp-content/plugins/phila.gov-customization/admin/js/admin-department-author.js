/* only loads in the admin,
for users who do not have the PHILA_ADMIN capability */

jQuery(document).ready(function($){

  //force top category to be checked all the time, unless the user has access to mutiple categories
  if( !phila_WP_User.includes('multi_department_access') && !phila_WP_User.includes('secondary_all_departments')) {
    var required_cat = $('#categorychecklist > li:first-child input');
    if( !required_cat.attr('checked')  ) {
      required_cat.attr('checked','checked');
    }
  }

  if ( philaAllPostTypes.indexOf( typenow ) !== -1 && adminpage.indexOf( 'post' ) > -1 ) {
  //At least one category must be selected
  $("#publish").one('click', function () {

    var categories = document.getElementsByName("post_category[]");

     if(categories[0].checked==false && categories[1].checked==false && categories[2].checked==false) {

      $('#categorydiv').addClass('error');

      $('#categorydiv').before('<label id="title-error" class="error" for="categorydiv">Category selection is required.</label>');

      return false;
     }
     return true;
   });
  }

  //hide all category and tag menu items, department authors shouldn't see those.
  $('a[href*="edit-tags.php"]').parent().css('display', 'none');

  var menuIdString = $('#menu-id').text().trim();
  var allMenuIDs = menuIdString.split(' ');
  var match = document.getElementById( allMenuIDs );

  //hide all menu locations
  $('.menu-theme-locations input').parent().css('display', 'none');
  //display menu locations that match current user roles
  for (var i = 0; i < allMenuIDs.length ; i++) {
    var currentMenuId = document.getElementById( allMenuIDs[i] );
    $(currentMenuId).parent().css('display', 'block');
  }

  var menuNameString = $('#menu-name').text().trim();
  var allMenuNames = menuNameString.split(' ');

  //show menus that match current user roles
  for (var i = 0; i < allMenuNames.length ; i++) {
    var currentMenuName = allMenuNames[i];
    $( '.manage-menus option:contains("" + currentMenuName + "")').show();
  }
  //add correct menu classes to 'nav menu' link
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
  if ( ( typenow == 'post') && adminpage.indexOf('post') > -1 ){
    if( !phila_WP_User.includes('secondary_press_release_editor') && !phila_WP_User.includes('secondary_press_release_contributor') ) {

      $('#phila_template_select option').each( function () {
        if( $(this).val() !== '' && $(this).val() !== 'post' ){
          $(this).css('display', 'none');
        }
      });
    }
  }

});
