var List = require('list.js');
module.exports = $(function(){

  //Any page with a search bar directory filter list
  var filterList = new List('filter-list', {
    valueNames: ['item', 'item-desc']
  });

  //prevent enter from refreshing the page and stopping filter search
  $('#filter-list input').keypress(function(event){
    if(event.keyCode === 13) {
      event.preventDefault();
      return false;
    }
  });

  $('.search-field').on('keyup', function() { 
    if($('.list').children().length === 0) { 
      $('.not-found').css('display', 'block'); 
    } else {
      $('.not-found').css('display', 'none'); 
    }
  });

});
