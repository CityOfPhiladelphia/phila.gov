var hiddenCols = $("table.js-hide-empty");

if (hiddenCols.length > 0) {
  $("th", hiddenCols).each(function(i) {
    var remove = 0;
    var tds = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')');
    tds.each(function(j) {
      if (this.innerHTML == '') remove++;
    });
    if (remove == ($('tr', hiddenCols).length - 1)) {
      $(this).hide();
      tds.hide();
    };
  });
}
