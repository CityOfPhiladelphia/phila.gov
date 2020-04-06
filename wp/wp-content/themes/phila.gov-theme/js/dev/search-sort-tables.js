var List = require('list.js');
var moment = require('moment');

module.exports = $(function(){

  var pageNum = $( "table" ).hasClass( "staff-directory" ) ? 20 : 6
  var pagination = $("table").hasClass("no-paginate") ? false : true
  
  

  if (pagination){
    var options = {
      searchClass: 'table-search',
      listClass: 'search-sortable',
      sortClass: 'table-sort',
      valueNames: [ 'name', 'title', 'category', { name: 'date', attr : 'data-unix' } , 'author', 'description' ],
      pagination: true,
      page: pageNum,
      paginationClass: 'paginate-links',
    }
  }else{
    var options = {
      searchClass: 'table-search',
      listClass: 'search-sortable',
      sortClass: 'table-sort',
      valueNames: [ 'name', 'title', 'category', { name: 'date', attr : 'data-unix' } , 'author', 'description' ],
    }
  } 

  $('.date').each(function(j){
    let text = $(this).clone().find('span').remove().end().text();
    let date = text.split(" ").filter(function(v){return v!==''});
    let jsDate = new Date(date[1] + " " + date[2] + " "  + date[3]);
    // console.log(jsDate);
    $(this).attr('data-unix', moment(jsDate).unix())
  })

  $('.search-sort-single-table').each(function( j ) {
    var table = new List('sortable-table-' + j, {
      searchClass: 'table-search',
      listClass: 'search-sortable',
      sortClass: 'table-sort',
      valueNames: [ 'name', 'title', 'category', { name: 'date', attr : 'data-unix' } , 'author', 'description' ],
    })
  })

  $('.search-sort-table').each(function( j ) {
    var table = new List('sortable-table-' + j, options)

    table.on('updated', function (list) {
      // console.log(list)

      if (list.matchingItems.length > 0) {
        $('#sortable-table-' + j + ' .no-results').hide()
      } else {
        $('#sortable-table-' + j + '.no-results').show()
      }
      if ( pagination ) {
        checkNavButtons();
      }

      var loggedIn;

      if($('#wpadminbar').length){
        loggedIn = $('#wpadminbar').outerHeight();
      }else {
        loggedIn = 0;
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
      if (pagination) {

      $('.next-' + j).on('click', function(e){
        table.show( table.i + pageNum, pageNum);
        e.preventDefault();
      })

      $('.prev-' + j).on('click', function(e){
        table.show( table.i - pageNum, pageNum);
        e.preventDefault();
      })
      checkNavButtons()
    }
  })

})
