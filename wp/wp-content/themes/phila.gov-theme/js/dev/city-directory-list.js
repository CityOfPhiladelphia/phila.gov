var List = require('list.js');

module.exports = $(function(){
  //City government directory filter list
  new List('filter-list', {
    valueNames: ['item', 'item-desc']
  });

});
