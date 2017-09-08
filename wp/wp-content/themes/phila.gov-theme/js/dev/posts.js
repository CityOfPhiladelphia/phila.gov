var $thumb = $('.js-thumbnail-image').detach();
$('.post-content > div').prepend($thumb);

if (typeof phila_js_vars !== 'undefined') {

  var postRestBase = phila_js_vars.postRestBase;
  var postID = phila_js_vars.postID;


  $.each($('.lightbox-link'), function(){
    $(this).click(function() {
      var url = $(this).data('img-url');

      $(document).on('open.zf.reveal', '#phila-lightbox', function (e) {
        var $modal = $(this);
        var $content = $('.lightbox-content');
        var ajaxURL = 'https://ec2-54-210-141-119.compute-1.amazonaws.com/wp-json/wp/v2/' + postRestBase + '/' + postID + '/' + '?_embed=true' ;
        $content.html('Loading...');

        if ( url != null ) {
          $content.html('<img src="' + url + '" alt="">');

        }else if( ajaxURL ){

          $.ajax(ajaxURL).done(function (response) {
            var fullSizeImg = response._embedded["wp:featuredmedia"]["0"].media_details.sizes.full.source_url;
            var featuredCaption = response._embedded["wp:featuredmedia"]["0"].caption.rendered;

            $content.html('<img src="' + fullSizeImg + '" alt="">');
            $content.append(featuredCaption);
          })
          .fail(function() {
            $content.html('Image cannot be loaded.');
          });
        }
      });

    });
  });
}

//disable lightboxes on mobile
$(function(){
  if (Foundation.MediaQuery.current === 'small') {
    $('#phila-lightbox').foundation('_destroy');
    $('.lightbox-link').blur();
  }
});
