/**
 *
 * Custom js for our theme
 *
**/

//department filter list
new List('filter-list', {
  valueNames: ['item', 'item-desc']
});

jQuery(document).ready(function($) {

  //prevent enter from refreshing the page and stopping filter search
  $('#filter-list input').keypress(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $('.clickable-row').click(function() {
    window.location = $(this).data('href');
  });

  $('.clickable-row').hover(function() {
      $(this).addClass('is-hover');
    },
    function(){
      $(this).removeClass('is-hover');
  });

  /* Hijack mailchimp signup forms to use ajax handler */

  /*NOTE: this method requires that the form action URL from mailchimp uses subscribe/post-json */
  ajaxMailChimpForm($('#mc-embedded-subscribe-form'), $('#mc_embed_signup'));
  // Turn the given MailChimp form into an ajax version of it.
  // If resultElement is given, the subscribe result is set as html to
  // that element.
  function ajaxMailChimpForm($form, $resultElement){
    // Hijack the submission. We'll submit the form manually.
    $form.submit(function(e) {
      e.preventDefault();
      if (!isValidEmail($form)) {
        if(!$resultElement.children().hasClass('error')){
          var error = '<span class="error">A valid email address must be provided.</span>';
          $resultElement.append(error);
          $resultElement.css('color', '#f99300');
        }
      } else {
        $resultElement.css('color', 'black');
        $resultElement.append('Subscribing...');
        submitSubscribeForm($form, $resultElement);
        $('.error').remove();
      }
    });
  }
  // Validate the email address in the form
  function isValidEmail($form) {
    var email = $form.find('input[type="email"]').val();
    if (!email || !email.length) {
        return false;
    } else if (email.indexOf('@') == -1) {
        return false;
    }
    return true;
  }
  // Submit the form with an ajax/jsonp request.
  // Based on http://stackoverflow.com/a/15120409/215821
  function submitSubscribeForm($form, $resultElement) {
    $.ajax({
      type: 'GET',
      url: $form.attr('action'),
      data: $form.serialize(),
      cache: false,
      dataType: 'jsonp',
      jsonp: 'c', // trigger MailChimp to return a JSONP response
      contentType: 'application/json; charset=utf-8',
      error: function(error){
          // According to jquery docs, this is never called for cross-domain JSONP requests
      },
      success: function(data){
        if (data.result != 'success') {
          var message = data.msg || 'Sorry. Unable to subscribe. Please try again later.';
          $resultElement.css('color', '#f99300');
          if (data.msg && data.msg.indexOf('already subscribed') >= 0) {
            message = 'You\'re already subscribed. Thank you.';
            $resultElement.css('color', 'black');
          }else if (data.msg && data.msg.indexOf('zip code') >= 0) {
            message = 'Please enter a valid zip code.';
            $resultElement.css('color', 'f99300');
          }
          $resultElement.append(message);
        } else {
          $resultElement.css('color', '#58c04d');
          $resultElement.html('Thank you!<br>You must confirm the subscription in your inbox.');
        }
      }
    });
  }

  // Staff summary expand
  $('[data-toggle="data-staff-bio"]').click(function(e){
    e.preventDefault();
    $(this).parent().siblings().toggleClass('expandable');
    if($(this).html() === ' Expand + '){
      $(this).html(' Collapse - ');
    } else {
      $(this).html(' Expand + ');
    }
  });

  /* Async loaded feedback forms */
  $.fn.feedbackify = function( src ) {
    postscribe('#form-container', '<script  type="text/javascript" src="' + src + '"><\/script>');
    $('html,body').animate({
        scrollTop: $('.feedback').position().top - $('header .is-stuck').height()
      }, 700 );
    return false;
  };

  $("footer .feedback").on('click', function(){
    $(this).feedbackify('https://form.jotform.com/jsform/62765090493967');
  });

  $(".neighborhood-resources .feedback").on('click', function(){
    $(this).feedbackify('https://form.jotform.com/jsform/62516788470970');
  });

  function hideEmptyCols(table) {
    var rows = $("tr", table).length-1;
    var numCols = $("th", table).length;
    for ( var i=1; i<=numCols; i++ ) {
        if ( $("span:empty", $("td:nth-child(" + i + ")", table)).length == rows ) {
            $("td:nth-child(" + i + ")", table).hide(); //hide <td>'s
            $("th:nth-child(" + i + ")", table).hide(); //hide header <th>
        }
    }
  }

  hideEmptyCols('.staff');

});
