var $thumb = $('.js-thumbnail-image').detach();
$('.post-content > div').prepend($thumb);

Foundation.Reveal.defaults.vOffset = 20;
var closeButton = "<button class=\"close-button\" data-close aria-label=\"Close modal\" type=\"button\"><span aria-hidden=\"true\">&times;</span></button>";

if (typeof phila_js_vars !== 'undefined') {

  var postRestBase = phila_js_vars.postRestBase;
  var postID = phila_js_vars.postID;

  $(document).on('open.zf.reveal', '#phila-lightbox-feature', function (e) {
    var $modal = $(this);
    var ajaxURL = 'https://flbjdoa008.execute-api.us-east-1.amazonaws.com/'+postID;
    $modal.html('Loading...');
    $.ajax(ajaxURL).done(function (response) {
      var fullSizeImg = response._embedded["wp:featuredmedia"]["0"].media_details.sizes.full.source_url;
      var featuredCaption = response._embedded["wp:featuredmedia"]["0"].caption.rendered;
      $modal.html('<div class="lightbox-content"><img src="' + fullSizeImg + '" alt="" /></div>');

      if(response._embedded["wp:featuredmedia"]["0"].meta_box.phila_media_credit.length) {
        var featuredCredit = document.createElement("p");
        featuredCredit.innerHTML = '<strong>Photo by: '+response._embedded["wp:featuredmedia"]["0"].meta_box.phila_media_credit+'</strong>';
        $modal.append(featuredCredit);
      }
      
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

  for (let item in phila_language_list) {
    let li = document.createElement("li");
    if ($('.single-post').length ) { 
      let a_tag = document.createElement("a");
      a_tag.className += " phm";
      a_tag.className += " translation-link";
      a_tag.className += " phs";
      a_tag.className += " "+phila_language_list[item].language+'-translation';
      if(phila_language_list[item].value == window.location.href.split(/[?#]/)[0]) {
        a_tag.className += " active"
      }
      a_tag.href = phila_language_list[item].value;
      a_tag.innerHTML = phila_language_list[item].key;
      a_tag.addEventListener("click", function() {
        window.dataLayer.push({
          'event' : 'GAEvent',
          'eventCategory' : 'Translation Services',
          'eventAction' : phila_language_list[item].language,
          'eventLabel' : window.location.pathname,
        });
      });
      
      li.appendChild(a_tag);
    } else { // programs - translated content template
      const urlParams = new URLSearchParams(window.location.search);
      let activeLang = urlParams.get('lang');
      if (!activeLang) {
        activeLang = 'english';
      }
      let a_tag = document.createElement("a");
      if(phila_language_list[item].value == activeLang) {
        a_tag.className += " active";
      }
      a_tag.className += " phm";
      a_tag.className += " translation-link";
      a_tag.className += " phs";
      a_tag.className += " "+phila_language_list[item].language+'-translation';
      a_tag.href += "?lang="+phila_language_list[item].value;
      a_tag.innerHTML = phila_language_list[item].key;
      a_tag.addEventListener("click", function(event) {
        event.preventDefault();
        if (history.pushState) {
            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?lang="+phila_language_list[item].value;
            window.history.pushState({path:newurl},'',newurl);
        }
        updateActiveLanguage(phila_language_list[item].value);
        window.dataLayer.push({
          'event' : 'GAEvent',
          'eventCategory' : 'Translation Services',
          'eventAction' : phila_language_list[item].language,
          'eventLabel' : window.location.pathname,
        });
      });
      
      li.appendChild(a_tag);
      $('#'+activeLang+'-form').show();
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
    $modal.html('<div class="lightbox-content"><img src="' + url + '" alt=""/></div>').foundation('open');
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

function updateActiveLanguage(activeLang) {
  $('.embedded-translated-form').hide();
  $(".translation-link.active").removeClass("active");
  $('.translation-link').each(function () {
    if ($(this).hasClass(activeLang+"-translation")) {
      $(this).addClass("active");
      $('#'+activeLang+'-form').show();
    }
  });
}