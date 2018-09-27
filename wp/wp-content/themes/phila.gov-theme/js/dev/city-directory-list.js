var List = require('list.js');
module.exports = $(function(){

  //City government directory filter list
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

});
