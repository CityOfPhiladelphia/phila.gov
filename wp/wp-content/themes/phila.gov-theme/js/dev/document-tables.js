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

  $('.search-sort-table').each(function( j ) {
    var table = new List('sortable-table-' + j, options)
    table.on('updated', function (list) {
      console.log(table)

    if (list.matchingItems.length > 0) {
      $('.no-results').hide()
    } else {
      $('.no-results').show()
    }
    if ( table.i + pageNum > table.visibleItems.length) {
      $('.next').addClass('disabled')
    }else{
      $('.next').removeClass('disabled')
    }
  })

  $('.next-' + j).on('click', function(e){
      table.show( table.i + pageNum , pageNum);
      e.preventDefault();
  })

  $('.prev-' + j).on('click', function(e){
      // i, page
      table.show( table.i - pageNum, pageNum);
      e.preventDefault();
  })
});

});
