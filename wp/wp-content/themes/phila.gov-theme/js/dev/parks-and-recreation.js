jQuery(document).ready(function($) {
    var featActivitiesQueryURL = 'https://phl.carto.com/api/v2/sql?q=SELECT *, facility->>0 as facility, gender->>0 as gender FROM ppr_programs INNER JOIN ppr_facilities ON ppr_facilities.id = ppr_programs.facility->>0 AND ppr_programs.program_is_featured AND ppr_programs.program_is_public AND ppr_programs.program_is_approved LIMIT 3'
    var $activityCards = $('.ppr-feat-activity')
    var featLocationTypesQueryURL = 'https://phl.carto.com/api/v2/sql?q=SELECT count(*) AS count, ppr_location_types.location_type_name, ppr_location_types.location_type_description, ppr_location_types.location_type_photo, ppr_location_types.id , ppr_location_types.location_type_is_published, ppr_location_types.location_type_is_featured FROM ppr_location_types AS ppr_location_types  INNER JOIN ppr_facilities ON (ppr_facilities.location_type->>0 = ppr_location_types.id)  INNER JOIN ppr_website_locatorpoints ON (ppr_facilities.website_locator_points_link_id = ppr_website_locatorpoints.linkid)  WHERE ppr_location_types.location_type_is_published AND ppr_location_types.location_type_is_featured GROUP BY ppr_location_types.location_type_name, ppr_location_types.location_type_description, ppr_location_types.location_type_photo,  ppr_location_types.id, ppr_location_types.location_type_is_published, ppr_location_types.location_type_is_featured LIMIT 6';
    var $locationTypeCards = $('.ppr-feat-locationType')

    var render = function(card, data){
        for(var prop in data) {
            // go go gadget recursion
            if(typeof data[prop] == 'object'){
                render(card, data[prop])
            }

            var template = $(card).find('[data-'+prop+']')
            if(template.length){
                $(card).find('.ppr-loader').remove()
                $(card).addClass('loaded')
                if(typeof data[prop] != 'object') {
                    let imageRegex = /(photo)|(image)|(img)/;

                    // if(imageRegex.test(prop)){
                    //     if(data[prop] == '' || data[prop] == null ){
                    //         template.remove()
                    //     }
                    //     template.attr('src', data[prop])
                    // }
                    //
                    //
                    template.html(data[prop])
                }
            }
        }
    }



    function loadCardData (queryURL, $cards) {
        $.get(queryURL, function(results){
            var data = results.rows
            $cards.each(function(idx, card){
                if(data[idx]){
                   render(card, data[idx])
                } else {
                    $(card)
                      .find('.ppr-loader')
                      .html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i>')
                    $(card).addClass('error')
                    $(card).animate({opacity: 0}, 250)
                }

            })
        })
    }

    if($activityCards.length) {
        loadCardData(featActivitiesQueryURL, $activityCards)
    }

    if($locationTypeCards.length) {
        loadCardData(featLocationTypesQueryURL, $locationTypeCards)
    }


});
