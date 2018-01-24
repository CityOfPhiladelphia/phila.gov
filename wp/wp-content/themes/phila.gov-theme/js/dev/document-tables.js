var List = require('list.js');

module.exports = $(function(){
  var options = {
    searchClass: 'table-search',
    listClass: 'search-sortable',
    sortClass: 'table-sort',
    valueNames: [ 'title', 'category', 'date', 'author', 'description' ]
  }

  $('.document-table').each(function( i ) {
    var table = new List('sortable-table-' + i, options)
  })
});
