$('table.js-hide-empty').each(function(i, obj) {

  var table = this

  $("th", this).each(function(i) {

    var remove = 0;
    var tds = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')');

    tds.each(function(j) {
      if (this.innerHTML == '') remove++;
      if (this.innerHTML == ' ') remove++;
      if (this.innerHTML == '<b><span class="responsive-label">Description: </span></b> ') remove++;
    });

    if (remove == ($('tr', table).length - 1)) {
      $(this).hide();
      tds.hide();
    };
  });
});
