var deparam = require('../dependencies/jquery-deparam.js');
require('../dependencies/jquery.swiftype.autocomplete.js');
require('../dependencies/jquery.swiftype.search.js');
require('js-cookie');
var Mustache = require('mustache');


module.exports = jQuery(document).ready(function($) {

  if ( (window.location.href.indexOf("%3E")  > -1 ) || (window.location.href.indexOf("%3C")  > -1 )){
    return;
  }

  var resultTemplate = '<div data-type="{{&contentType}}"><article><header class="search-entry-header"><h3>';

  resultTemplate += '<a href="{{&url}}" rel="bookmark">{{title}}</a></h3></header>';

  resultTemplate += '<div class="mbm"><i class="{{&icon}}" aria-hidden="true"></i> <i>{{&contentType}}</i></div>';

  resultTemplate += '<p class="entry-summary">{{&summary}}</p></article><hr class="mhn">';

  var legacyTemplate = '<article><header class="search-entry-header"><h3 class="entry-title">';

  legacyTemplate += '<a href="{{&url}}" rel="bookmark">{{title}}</a></h3></header>';

  legacyTemplate += '<p class="entry-summary">{{&summary}}</p></article><hr class="mhn">';

  var SWIFTYPE_ENGINE = 'ALSW3neJArH_ozFvSDse';

  var $stSearchInput = $("#st-search-input");

  var searchConfig = {
    facets: {},
    filters: {
      content_type: undefined
    }
  };

  var readFilters = function() {
    return {
      page: {
        content_type: searchConfig.filters.content_type
      }
    }
  }

  var customRenderer = function(documentType, item) {

    var view = {
      url: encodeURI(item.url),
      title: item.title.replace(/<[^>]*>?/gm, ''),
      summary: item.highlight.body || (item.body.length > 250 ? item.body.substring(0, 250) + '...' : item.body),
      content_type: item.content_type,
      icon: ''
    };

    if ( item.tags === 'wordpress' || item.tags === 'app' ) {
      if(item.content_type  === 'programs') {
        view.contentType = 'Program and initiative'
        view.icon = 'fas fa-info-circle'
      }else if(item.content_type === 'post' || item.content_type === 'press_release' || item.content_type === 'news' || item.content_type === 'phila_post' || item.content_type === 'news_post'){
        view.contentType = 'News & events'
        view.icon = 'fas fa-microphone'
      }else if( item.content_type === 'department_page'){
        view.contentType = 'Department'
        view.icon = 'fas fa-sitemap'
      }else if(item.content_type === 'service_page'){
        view.contentType = 'Service'
        view.icon = 'fas -falist'
      }else if(item.content_type === 'document'){
        view.contentType = 'Document'
        view.icon = 'fas fa-file-alt'
      }
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
      $resultCount.html("No results found for <i>" + data['info']['page']['query'] +
      "</i>. <div class='info panel mtm row'><div class='medium-3 columns hide-for-small-only'><i class='far fa-frown fa-4x'></i></div> <div class='text small-24 medium-21 columns'><p class='h3 mbm'>We're sorry, we didn't find any results that match your search terms.</h3> Suggestions: <ul><li>Check your spelling. </li><li>Try different search terms.</li></ul></div></div>");

    } else {
      $resultCount.html("Found <b><span>" + totalResultCount + "</span></b> results for \"<i>" + data['info']['page']['query'] +"\"</i>");
    }

    if (spellingSuggestion !== null) {
      $('.info.panel .text').append('<span class="st-spelling-suggestion">Did you mean <a href="#" data-hash="true" data-spelling-suggestion="' + spellingSuggestion + '">' + spellingSuggestion + '</a>?</span>');
    }
  };

  function scrollTop(){
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return false;
  }

  function customRenderPaginationForType(type, currentPage, totalPages) {
    var pages = '<nav><ul class="no-bullet paginate-links">',
      previousPage, nextPage;
    if (currentPage != 1) {
      previousPage = currentPage - 1;
      pages = pages + '<li><a href="#" onClick="' + scrollTop() + '" class="prev" data-hash="true" data-page="' + previousPage  + '"><i class="fas fa-arrow-left" aria-hidden="true"></i> Previous</a></li>';
    }
    if (currentPage < totalPages) {
      nextPage = currentPage + 1;
      pages = pages + '<li><a href="#" onClick="' + scrollTop() + '" class="next" data-hash="true" data-page="' + nextPage + '">Next <i class="fas fa-arrow-right" aria-hidden="true"></i></a></li>';
    }
    pages += '</ul></nav>';

    return pages;
  }


  $('.content-type').on('click', function(e){
    var current = $(this).data('type')

    searchConfig.filters.content_type = current;

    if ( current === 'post'){
      searchConfig.filters.content_type = ['post', 'phila_post', 'news', 'press_release' ]
    }

    //reset pagination to 1
    window.location.href = escapeHtml(window.location.href).replace(/stp=\d{0,3}/, 'stp=1');
    $stSearchInput.swiftypeSearch();

    reloadResults();
  })

  $('#content-types').on('click', 'a.clear-all', function(e) {
    e.preventDefault();

    $('.content-type').prop('checked', false);

    searchConfig.filters.content_type = [];

    //reset pagination to 1
    window.location.href = escapeHtml(window.location.href).replace(/stp=\d{0,3}/, 'stp=1');

    $stSearchInput.swiftypeSearch();

    reloadResults();
  })

  $stSearchInput.swiftypeSearch({
    engineKey: SWIFTYPE_ENGINE,
    resultContainingElement: '#st-results-container',
    renderFunction: customRenderer,
    postRenderFunction: customPostRenderFunction,
    renderPaginationForType: customRenderPaginationForType,
    filters: readFilters,
    facets: { page: ['content_type'] },
  });
  
  //handles searches from the global menu
  $("#search-form").submit(function (e) {
    e.preventDefault();
    var userString = $(this).find(".search-field").val();
    window.location.href = '/search/#stq=' + Swiftype.htmlEscape(userString);
  })

  $("#st-search-form").submit(function (e) {
    e.preventDefault();
    var userString = $(this).find(".search-field").val();
    window.location.href = '/search/#stq=' + Swiftype.htmlEscape(userString);
  })

  var addressRe = /\d+ \w+/;
  var $propertyLink = $('#property-link');

  function addressSearch () {
    $propertyLink.css('display', 'none');

    // Also check OPA API for results if it looks like an address
    var params = $.deparam(location.hash.substr(1));
    var query = Swiftype.htmlEscape(params.stq);
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

  function escapeHtml(unsafe) {
    return unsafe
      .replace(/</g, "")
      .replace(/>/g, "")
      .replace(/"/g, "")
      .replace(/'/g, "");
  }

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
    $(window).trigger('hashchange');
  }

  $(window).on('hashchange', function (e) {
    addressSearch();
  }).trigger('hashchange');

  // Autocomplete
  $('.swiftype').swiftype({
    engineKey: SWIFTYPE_ENGINE,
    renderFunction: customAutocompleteRender,
    resultLimit: 5
  })

});
