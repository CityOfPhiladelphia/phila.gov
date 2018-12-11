var List = require('list.js');

module.exports = $(function(){

  var pageNum = $( "table" ).hasClass( "staff" ) ? 20 : 3

  var options = {
    searchClass: 'table-search',
    listClass: 'search-sortable',
    sortClass: 'table-sort',
    valueNames: [ 'name', 'title', 'category', 'date', 'author', 'description' ],
    pagination: true,
    page: pageNum,
    paginationClass: 'paginate-links',
  }

  $('.search-sort-table').each(function( i ) {
    var table = new List('sortable-table-' + i, options)
    table.on('updated', function (list) {
      console.log(table)

    if (list.matchingItems.length > 0) {
      $('.no-results').hide()
    } else {
      $('.no-results').show()
    }

  })
  $('.next').on('click', function(){
      table.show(table.i + 1);
      console.log(table.i - 1);
  })

  $('.prev').on('click', function(){
      table.show(table.i - 1);
      console.log(table.i - 1)
  })
});


});
