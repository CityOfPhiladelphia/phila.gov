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

      if (list.matchingItems.length > 0) {
        $('.no-results').hide()
      } else {
        $('.no-results').show()
      }
      checkNavButtons()
      })

      function checkNavButtons() {

        if ( table.i === 1 && table.page === pageNum && table.visibleItems.length === pageNum ) {
          $('.prev-' + j).addClass('disabled')
        }else{
          if( table.visibleItems.length < pageNum ){
            $('.prev-' + j).addClass('disabled')
          }else {
            $('.prev-' + j).removeClass('disabled')
          }
        }
        console.log(table)
        console.log(table.visibleItems.length)
        console.log(table.i)


        if ( table.visibleItems.length < pageNum ) {
          $('.next-' + j).addClass('disabled')
        }else{
          $('.next-' + j).removeClass('disabled')
        }
      }

    $('.next-' + j).on('click', function(e){
      table.show( table.i + pageNum, pageNum);
      e.preventDefault();
    })

    $('.prev-' + j).on('click', function(e){
      table.show( table.i - pageNum, pageNum);
      e.preventDefault();
    })
    checkNavButtons()
  })

})
