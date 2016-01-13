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

  //only modify wp.media if this is a department site, or publication
  /*if ( (typenow == 'department_page' || typenow == 'document') && adminpage.indexOf('post') > -1 ){
    //make upload tab the default
    wp.media.controller.Library.prototype.defaults.contentUserSetting=false;
    wp.media.controller.Library.prototype.defaults.searchable=false;
    wp.media.controller.Library.prototype.defaults.sortable=false;
  }*/

  if ( (typenow == 'department_page' || typenow == 'document' || typenow == 'service_post') && adminpage.indexOf('post') > -1 ){
    $("#post").validate({
        rules: {
          'post_title' : 'required'
        }
      });
  }
  if (typenow == 'department_page' && adminpage.indexOf('post') > -1 ){
    var heading = $('[id^=phila_block_heading]');
    var content_title = $('[id^=phila_block_content_title]');
    var summary = $('[id^=phila_block_summary]');
    var alert_heading = $('[id^=phila_department_home_alert_title]');
    var id = $('[id^=phila_block_id]');

    heading.attr('maxlength', '20');
    content_title.attr('maxlength', '70');
    summary.attr('maxlength', '255');
    alert_heading.attr('maxlength', '255');
    id.attr('required', 'required');
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
  if (typenow == 'page' && adminpage.indexOf('post') > -1 ){

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
  }
  if (typenow == 'page' && adminpage.indexOf('post') > -1 ){
    $('#post').validate({
      rules: {
         'post_title' : 'required'
       }
    });
    $( '#phila_page_desc' ).rules( 'add', {
      required: true,
      maxlength: 255,
    });
  }
  if (typenow == 'phila_post' && adminpage.indexOf('post') > -1 ){

    $('a#link-post_tag').click();

  }

});
