var List = require('list.js');

module.exports = $(function(){
  var options = {
    searchClass: 'table-search',
    listClass: 'search-sortable',
    sortClass: 'table-sort',
    valueNames: [ 'title', 'category', 'date', 'author', 'format' ]
  }

  $('.document-table').each(function( i ) {
    console.log(i)
    var table = new List('sortable-table-' + i, options)
  })


  //prevent enter from refreshing the page and stopping filter search
  $('#filter-list input').keypress(function(event){
    if(event.keyCode === 13) {
      event.preventDefault();
      return false;
    }
  })
});
