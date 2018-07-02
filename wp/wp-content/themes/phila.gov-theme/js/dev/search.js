var deparam = require('../dependencies/jquery-deparam.js');
require('../dependencies/jquery.swiftype.autocomplete.js');
require('../dependencies/jquery.swiftype.search.js');
require('js-cookie');
var Mustache = require('mustache');

module.exports = jQuery(document).ready(function($) {

  var resultTemplate = '<article><header class="search-entry-header"><h3 class="entry-title">';

  resultTemplate += '<a href="{{&url}}" rel="bookmark">{{title}}</a></h3></header>';

  resultTemplate += '<p class="entry-summary">{{&summary}}</p></article><hr>';

  var legacyTemplate = '<article><header class="search-entry-header"><h3 class="entry-title"><span class="label mrm bg-dark-gray">Legacy</span>';

  legacyTemplate += '<a href="{{&url}}" rel="bookmark">{{title}}</a></h3></header>';

  legacyTemplate += '<p class="entry-summary">{{&summary}}</p></article><hr>';

  var SWIFTYPE_ENGINE = 'ALSW3neJArH_ozFvSDse';

  var customRenderer = function(documentType, item) {

    var view = {
      url: encodeURI(item.url),
      title: item.title,
      summary: item.highlight.body || (item.body.length > 300 ? item.body.substring(0, 300) + '...' : item.body)
    };
    if ( item.tags === 'wordpress' || item.tags === 'app' ) {
      return Mustache.render(resultTemplate, view);
    }else{
      return Mustache.render(legacyTemplate, view);
    }
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
      $resultCount.html("Found <b><span>" + totalResultCount + "</span></b> results");
    }

    if (spellingSuggestion !== null) {
      $resultContainer.append('<div class="st-spelling-suggestion">Did you mean <a href="#" data-hash="true" data-spelling-suggestion="' + spellingSuggestion + '">' + spellingSuggestion + '</a>?</div>');
    }
  };

  function customRenderPaginationForType(type, currentPage, totalPages) {
    var pages = '<nav class="navigation paging-navigation">',
      previousPage, nextPage;
    if (currentPage != 1) {
      previousPage = currentPage - 1;
      pages = pages + '<a href="#" class="st-prev prev page-numbers" data-hash="true" data-page="' + previousPage  + '"><i class="fa fa-arrow-left" aria-hidden="true"></i> previous</a>';
    }
    if (currentPage < totalPages) {
      nextPage = currentPage + 1;
      pages = pages + '<a href="#" class="st-next next page-numbers" data-hash="true" data-page="' + nextPage + '">next <i class="fa fa-arrow-right" aria-hidden="true"></i></a>';
    }
    pages += '</nav>';
    return pages;
  };

  var $stSearchInput = $("#st-search-input");
  $stSearchInput.swiftypeSearch({
    engineKey: SWIFTYPE_ENGINE,
    resultContainingElement: '#st-results-container',
    renderFunction: customRenderer,
    postRenderFunction: customPostRenderFunction,
    renderPaginationForType: customRenderPaginationForType
  });

  $("#search-form").submit(function (e) {
    e.preventDefault();
    window.location.href = '/search/#stq=' + $(this).find(".search-field").val();
  });



  function hashQuery () {
    // Fill search input with query from hash
    var params = $.deparam(location.hash.substr(1));

    $stSearchInput.val(params.stq);

    //create link back to phila.gov with search param
    var link = "https://cse.google.com/cse?oe=utf8&ie=utf8&source=uds&start=0&cx=003474906032785030072:utbav7zeaky&hl=en&q=" + params.stq + "#gsc.tab=0&gsc.q=" + params.stq + "&gsc.sort=";
    var a = $('.classic-gov-search');
    if (a.length != 0) {
      a[0].href = link;
    }

  }

  // Fill search box on page load
  hashQuery();

  var addressRe = /\d+ \w+/;
  var $propertyLink = $('#property-link');

  function addressSearch () {
    // Also check OPA API for results if it looks like an address
    var params = $.deparam(location.hash.substr(1));
    var query = params.stq;
    var queryEncoded = encodeURIComponent(query);

    if (addressRe.test(params.stq)) {
      $.ajax('https://api.phila.gov/ais/v1/addresses/' + queryEncoded, {
          dataType: $.support.cors ? 'json' : 'jsonp',
          data: { gatekeeperKey: 'ad0050d3c6e40064546a18af371f7826' }
        })
      .done(function (data) {
        if (data.total_size) {
          $propertyLink.prop('href', '/property/?a=' + queryEncoded + '&u=');
          $propertyLink.css('display', 'block');
        }
      });
    }
  }


  $(window).on('hashchange', function (e) {
    addressSearch();
  }).trigger('hashchange');

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
  $('.swiftype').swiftype({
    engineKey: SWIFTYPE_ENGINE,
    renderFunction: customAutocompleteRender,
    resultLimit: 5
  })

});
