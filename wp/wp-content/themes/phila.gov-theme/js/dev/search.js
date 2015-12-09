(function ($) {

var resultTemplate = '<article><header class="search-entry-header"><h3 class="entry-title">';
resultTemplate += '<a href="{{&url}}" rel="bookmark">{{title}}</a></h3></header>';
resultTemplate += '<p class="entry-summary">{{&summary}}</p></article><hr>';

var customRenderer = function(documentType, item) {
  var view = {
    url: encodeURI(item.url),
    title: item.title,
    summary: item.highlight.body || (item.body.length > 300 ? item.body.substring(0, 300) + '...' : item.body)
  };
  return Mustache.render(resultTemplate, view);
};

var $resultCount = $('#result-count');

var customPostRenderFunction = function(data) {
  var totalResultCount = 0;
  var $resultContainer = this.getContext().resultContainer;
  var spellingSuggestion = null;

  if (data['info']) {
    $.each(data['info'], function(index, value) {
      totalResultCount += value['total_result_count'];
      if ( value['spelling_suggestion'] ) {
        spellingSuggestion = value['spelling_suggestion']['text'];
      }

    });
  }

  if (totalResultCount === 0) {
    $resultCount.text("No results found");
  } else {
    $resultCount.html("Found <b>" + totalResultCount + "</b> results");
  }

  if (spellingSuggestion !== null) {
    $resultContainer.append('<div class="st-spelling-suggestion">Did you mean <a href="#" data-hash="true" data-spelling-suggestion="' + spellingSuggestion + '">' + spellingSuggestion + '</a>?</div>');
  }
};

var $stSearchInput = $("#st-search-input");
$stSearchInput.swiftypeSearch({
  engineKey: SWIFTYPE_ENGINE, // Env var set in footer by php
  resultContainingElement: '#st-results-container',
  renderFunction: customRenderer,
  postRenderFunction: customPostRenderFunction
});

$("#search-form").submit(function (e) {
  e.preventDefault();
  window.location.href = '/search/#stq=' + $(this).find(".search-field").val();
});

function hashQuery () {
  // Fill search input with query from hash
  var params = $.deparam(location.hash.substr(1));
  $stSearchInput.val(params.stq);
}

// Fill search box on page load
hashQuery();

var addressRe = /\d+ \w+/;
var $propertyLink = $('#property-link');
function addressSearch () {
  // Also check OPA API for results if it looks like an address

  $propertyLink.hide();

  var params = $.deparam(location.hash.substr(1));
  var query = params.stq;
  var queryEncoded = encodeURIComponent(query);

  if (addressRe.test(params.stq)) {
    $.ajax('https://api.phila.gov/opa/v1.1/address/' + queryEncoded + '/?format=json',
      {dataType: $.support.cors ? 'json' : 'jsonp'})
      .done(function (data) {
        if (data.total) {
          $propertyLink.prop('href', '/property/?a=' + queryEncoded + '&u=');
          $propertyLink.show();
        }
      });
  }
}

addressSearch();
$(window).hashchange(addressSearch);

function getPath (url) {
  // Use this to only get the path on the URL
  // Handy for this working across environments
  var a = document.createElement('a');
  a.href = url;
  return a.pathname;
}

function customAutocompleteRender (document_type, item) {
  return '<a class="autocomplete-link" href="' + getPath(item.url) + '">' + Swiftype.htmlEscape(item.title) + '</a>';
}

// Autocomplete
$('.search-field').swiftype({
  engineKey: SWIFTYPE_ENGINE, // Env var set in footer by php
  renderFunction: customAutocompleteRender
})

})(jQuery);
