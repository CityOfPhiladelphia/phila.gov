jQuery(document).ready(function($) {
    var featActivitiesQueryURL = 'https://phl.carto.com/api/v2/sql?q=SELECT *, facility->>0 as facility, gender->>0 as gender FROM ppr_programs INNER JOIN ppr_facilities ON ppr_facilities.id = ppr_programs.facility->>0 AND ppr_programs.program_is_featured AND ppr_programs.program_is_public AND ppr_programs.program_is_approved AND ppr_programs.program_is_active  LIMIT 3'
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
        $activityCards.each(function(idx, card){
            if(activityData[idx]){
               render(card, activityData[idx])
            } else {
                $(card)
                  .find('.ppr-feat-activity__loader')
                  .html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i>')
                $(card).addClass('error')
            }

        })
    })


});
