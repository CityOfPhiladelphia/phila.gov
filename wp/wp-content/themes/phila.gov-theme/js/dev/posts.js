var $thumb = $('.js-thumbnail-image').detach();
$('.post-content > div').prepend($thumb);

Foundation.Reveal.defaults.vOffset = 20;
var closeButton = "<button class=\"close-button\" data-close aria-label=\"Close modal\" type=\"button\"><span aria-hidden=\"true\">&times;</span></button>";

if (typeof phila_js_vars !== 'undefined') {

  var postRestBase = phila_js_vars.postRestBase;
  var postID = phila_js_vars.postID;

  $(document).on('open.zf.reveal', '#phila-lightbox-feature', function (e) {
    var $modal = $(this);
    var ajaxURL = '/wp-json/wp/v2/' + postRestBase + '/' + postID + '/' + '?_embed=true' ;
    $modal.html('Loading...');
    $.ajax(ajaxURL).done(function (response) {
      var fullSizeImg = response._embedded["wp:featuredmedia"]["0"].media_details.sizes.full.source_url;
      var featuredCaption = response._embedded["wp:featuredmedia"]["0"].caption.rendered;

      $modal.html('<div class="lightbox-content"><img src="' + fullSizeImg + '" alt=""></div>');
      $modal.append(featuredCaption);
      $modal.append( closeButton );
    })
    .fail(function() {
      $modal.html('Image cannot be loaded.');
    });
  });
}


if (typeof phila_language_list !== 'undefined') {
  let phila_language_list_count = phila_language_list.length;
  let show_dropdown = false;
  let i = 0;

  for (let item of phila_language_list) {
    let li = document.createElement("li");
    if(item.value == window.location.href.split(/[?#]/)[0]) {
      li.classList.add('phm', 'phs', 'active')
      li.innerHTML = item.key;
    }
    else {
      let a_tag = document.createElement("a");
      a_tag.classList.add('phm', 'phs', 'translation-link');
      a_tag.href = item.value;
      a_tag.innerHTML = item.key;
      li.appendChild(a_tag);
    }
    
    if (window.matchMedia('(max-width: 660px)').matches && i >= 2 && phila_language_list_count >= 3) {
      document.getElementById("dropdown-translation-bar").appendChild(li);
      show_dropdown = true;
    }
    else if (window.matchMedia('(max-width: 980px)').matches && i >= 4 && phila_language_list_count >= 5) {
      document.getElementById("dropdown-translation-bar").appendChild(li);
      show_dropdown = true;
    }
    else if (i >= 6 && phila_language_list_count >= 7){
      document.getElementById("dropdown-translation-bar").appendChild(li);
      show_dropdown = true;
    }
    else {
      document.getElementById("main-translation-bar").appendChild(li);
    }
    i++;
  }
  if (show_dropdown == false) {
    $('.dropdown-container').hide();
    $('.translations-container .inline-list').addClass("no-dropdown");
  }
}

$( '.column-content p:empty' ).remove();

$(function(){
  //modal for any image that's been added to the page and linked to.
  var $modal = $('#phila-lightbox');
  function loadContent(url){
    $modal.html('<div class="lightbox-content"><img src="' + url + '" alt=""></div>').foundation('open');
    $modal.append(closeButton);
  };

  $('.lightbox-all').on('click', dealWithIt);

  function dealWithIt(e){
    if (Foundation.MediaQuery.current !== 'small') {
      var href = e.currentTarget.dataset.imgUrl;
      var $this = $(this);
      loadContent(href);
    }
  }
  $('.lightbox-all').each(function(){
    $(this).removeAttr('tabindex');

  });

  $(document).on('closed.zf.reveal', '#phila-lightbox', function (e) {
    $('.lightbox-all').removeAttr('tabindex');
  });

});


//disable lightboxes on mobile
$(function(){
  if (Foundation.MediaQuery.current === 'small') {
    if ( $('.lightbox-link') ) {
      $('#phila-lightbox').foundation('_destroy');
      $('#phila-lightbox-feature').foundation('_destroy');
      $('.lightbox-link').blur();
    }
  }
});