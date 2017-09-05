var $thumb = $('.js-thumbnail-image').detach();
$('.post-content > div').prepend($thumb);

var postRestBase = phila_js_vars.postRestBase;
var postID = phila_js_vars.postID;

$(document).on('open.zf.reveal', '#phila-lightbox', function (e) {
  var $modal = $(this);
  var $content = $('.lightbox-content');
  var ajaxURL = 'https://ec2-54-210-141-119.compute-1.amazonaws.com/wp-json/wp/v2/' + postRestBase + '/' + postID + '/' + '?_embed=true' ;

  if (ajaxURL) {
    $content.html('Loading...');

    $.ajax(ajaxURL).done(function (response) {
      var fullSizeImg = response._embedded["wp:featuredmedia"]["0"].media_details.sizes.large.source_url;
      var featuredCaption = response._embedded["wp:featuredmedia"]["0"].caption.rendered;

      $content.html('<img src="' + fullSizeImg + '" alt="">');
      $content.append(featuredCaption);
    })
    .fail(function() {
      $content.html('Image cannot be loaded.');
    });
  }
});
