var deparam = require('../dependencies/jquery-deparam.js');
require('../dependencies/jquery.swiftype.autocomplete.js');
require('../dependencies/jquery.swiftype.search.js');
require('js-cookie');
var Mustache = require('mustache');

module.exports = jQuery(document).ready(function($) {

  var resultTemplate = '<div data-type="{{&contentType}}"><article><header class="search-entry-header"><h3>';

  resultTemplate += '<a href="{{&url}}" rel="bookmark">{{title}}</a></h3></header>';

  resultTemplate += '<div class="mbm"><i class="fa fa-{{&icon}}" aria-hidden="true"></i> <i>{{&contentType}}</i></div>';

  resultTemplate += '<p class="entry-summary">{{&summary}}</p></article><hr>';

  var legacyTemplate = '<article><header class="search-entry-header"><h3 class="entry-title"><span class="label mrm bg-dark-gray">Legacy</span>';

  legacyTemplate += '<a href="{{&url}}" rel="bookmark">{{title}}</a></h3></header>';

  legacyTemplate += '<p class="entry-summary">{{&summary}}</p></article><hr>';

  var SWIFTYPE_ENGINE = 'ALSW3neJArH_ozFvSDse';

  var $stSearchInput = $("#st-search-input");

  var searchConfig = {
    contentType: undefined
  };

  var readFilters = function() {
    return {
      contentType: window.searchConfig
    }
  }

  var customRenderer = function(documentType, item) {
    console.log(item)

    var view = {
      url: encodeURI(item.url),
      title: item.title,
      summary: item.highlight.body || (item.body.length > 250 ? item.body.substring(0, 250) + '...' : item.body),
      contentType: item.content_type,
      icon: ''
    };

    if ( item.tags === 'wordpress' || item.tags === 'app' ) {
      if(item.content_type  === 'programs') {
        view.contentType = 'Program'
        view.icon = 'users'
      }else if(item.content_type === 'post' || item.content_type === 'press_release' || item.content_type === 'news' || item.content_type === 'phila_post' || item.content_type === 'news_post'){
        view.contentType = 'News & events'
        view.icon = 'newspaper-o'
      }else if( item.content_type === 'department_page'){
        view.contentType = 'Department'
        view.icon = 'sitemap'
      }else if(item.content_type === 'service_page'){
        view.contentType = 'Service'
        view.icon = 'gears'
      }else if(item.contentType === 'document_page'){
        view.contentType = 'Document'
        view.icon = 'file-text-o'
      }

      return Mustache.render(resultTemplate, view);
    }else{
      $('#legacy-content').css('display', 'block');
      return Mustache.render(legacyTemplate, view);
    }
  };


  var $resultCount = $('#result-count');

  var customPostRenderFunction = function(data) {
    var totalResultCount = 0;
    var $resultContainer = this.getContext().resultContainer;
    var spellingSuggestion = null;

    if (data['info']) {
      console.log(data['info']);
      $.each(data['info'], function(index, value) {
        totalResultCount += value['total_result_count'];
        if ( value['spelling_suggestion'] ) {
          spellingSuggestion = value['spelling_suggestion']['text'];
        }

      });
    }

    if (totalResultCount === 0) {
      $resultCount.text("No results found for \"<i>" + data['info']['page']['query'] +"\"</i>. <div class=\"info panel\"><p class=\"h3\">We're sorry, we didn't find any results that match your search terms.</h3>Suggestions: <ul><li>Check your spelling. </li><li>Try different search terms.</li>");
    } else {
      $resultCount.html("Found <b><span>" + totalResultCount + "</span></b> results for \"<i>" + data['info']['page']['query'] +"\"</i>");
    }

    if (spellingSuggestion !== null) {
      $resultContainer.append('<div class="st-spelling-suggestion">Did you mean <a href="#" data-hash="true" data-spelling-suggestion="' + spellingSuggestion + '">' + spellingSuggestion + '</a>?</div>');
    }
  };

  function customRenderPaginationForType(type, currentPage, totalPages) {
    var pages = '<nav><ul class="no-bullet paginate-links">',
      previousPage, nextPage;
    if (currentPage != 1) {
      previousPage = currentPage - 1;
      pages = pages + '<li><a href="#page" class="prev" data-hash="true" data-page="' + previousPage  + '"><i class="fa fa-arrow-left" aria-hidden="true"></i> Previous</a></li>';
    }
    if (currentPage < totalPages) {
      nextPage = currentPage + 1;
      pages = pages + '<li><a href="#page" class="next" data-hash="true" data-page="' + nextPage + '">Next <i class="fa fa-arrow-right" aria-hidden="true"></i></a></li>';
    }
    pages += '</ul></nav>';
    return pages;
  };

  $stSearchInput.swiftypeSearch({
    engineKey: SWIFTYPE_ENGINE,
    resultContainingElement: '#st-results-container',
    renderFunction: customRenderer,
    postRenderFunction: customPostRenderFunction,
    renderPaginationForType: customRenderPaginationForType,
    filters: readFilters,
  });

  $("#search-form").submit(function (e) {
    e.preventDefault();
    window.location.href = '/search/#stq=' + $(this).find(".search-field").val();
  });


  $('.content-type').on('click', function(e){
    if ($(this).attr('checked')) {
      // Visually update the checkboxes
      $('.content-type').attr('checked', false);
      $(this).attr('checked', true);
      // Update the search parameters
      window.searchConfig.contentType = $(this).data('type');
    } else {
      window.searchConfig.contentType = undefined;
    }

    reloadResults();
  })


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
    $propertyLink.css('display', 'none');

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

  var reloadResults = function() {
    $(window).hashchange();
  };
  // Autocomplete
  $('.swiftype').swiftype({
    engineKey: SWIFTYPE_ENGINE,
    renderFunction: customAutocompleteRender,
    resultLimit: 5
  })

});
