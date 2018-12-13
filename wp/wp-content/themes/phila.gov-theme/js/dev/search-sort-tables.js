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
      console.log('#sortable-table-' + j)

      var loggedIn

      if($('#wpadminbar').length){
        loggedIn = $('#wpadminbar').outerHeight();
      }else {
        loggedIn = 0
      }
      //subtract 50 incase there's a title on the div
      $('html, body').animate({
         scrollTop: $('#sortable-table-' + j).offset().top - $('.primary-menu').outerHeight() - loggedIn - 50
       }, 400);
      })

      function checkNavButtons() {

        if ( table.i === 1 && table.page === pageNum && table.visibleItems.length === pageNum ) {
          $('.prev-' + j).addClass('disabled')
        }else{
          //Handle searches
          if( table.searched == true && table.visibleItems.length < pageNum ){
            $('.prev-' + j).addClass('disabled')
          }else {
            $('.prev-' + j).removeClass('disabled')
          }
        }

        if ( table.visibleItems.length < pageNum ) {
          $('.next-' + j).addClass('disabled')
        }else{
          //Handle last page
          if (table.matchingItems.length % table.items.length == 0 && table.matchingItems.length - table.i  >= pageNum) {
            $('.next-' + j).removeClass('disabled')
          }else{
            $('.next-' + j).addClass('disabled')
          }
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
