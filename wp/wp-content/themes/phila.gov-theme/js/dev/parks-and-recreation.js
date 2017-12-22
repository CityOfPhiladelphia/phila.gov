jQuery(document).ready(function($) {
    var featActivitiesQueryURL = 'https://phl.carto.com/api/v2/sql?q=SELECT *, facility->>0 as facility, gender->>0 as gender FROM ppr_programs INNER JOIN ppr_facilities ON ppr_facilities.id = ppr_programs.facility->>0 LIMIT 3'
    var $activityCards = $('.ppr-feat-activity')

    var render = function(card, data){
        for(var prop in data) {
            // go go gadget recursion
            if(typeof data[prop] == 'object'){
                render(card, data[prop])
            }
            var template = $(card).find('[data-'+prop+']')
            if(template.length){
                $(card).find('.ppr-feat-activity__loader').remove()
                $(card).addClass('loaded')
                if(typeof data[prop] != 'object') {
                    template.html(data[prop])
                }
            }
        }
    }

    $.get(featActivitiesQueryURL, function(results){
        var activityData = results.rows
        console.log(activityData)
        $activityCards.each(function(idx, card){
            render(card, activityData[idx])
        })
    })


});
