jQuery(document).ready(function($) {
    var featActivitiesQueryURL = 'https://phl.carto.com/api/v2/sql?q=SELECT *, ppr_programs.id as id, programdescriptionshort as desc, facility->>0 as facility, gender->>0 as gender FROM ppr_programs INNER JOIN ppr_facilities ON ppr_facilities.id = ppr_programs.facility->>0 AND ppr_programs.program_is_featured AND ppr_programs.program_is_public AND ppr_programs.program_is_approved AND ppr_programs.program_is_active  LIMIT 3'
    var $activityCards = $('.ppr-feat-activity')
    var featLocationTypesQueryURL = "https://phl.carto.com/api/v2/sql?q=SELECT count(*) AS count, regexp_replace(lower(ppr_location_types.location_type_name), ' ', '-', 'g') as slug, ppr_location_types.location_type_name, ppr_location_types.location_type_description, ppr_location_types.location_type_photo, ppr_location_types.id , ppr_location_types.location_type_is_published, ppr_location_types.location_type_is_featured FROM ppr_location_types AS ppr_location_types  INNER JOIN ppr_facilities ON (ppr_facilities.location_type->>0 = ppr_location_types.id)  INNER JOIN ppr_website_locatorpoints ON (ppr_facilities.website_locator_points_link_id = ppr_website_locatorpoints.linkid)  WHERE ppr_location_types.location_type_is_published AND ppr_location_types.location_type_is_featured GROUP BY ppr_location_types.location_type_name, ppr_location_types.location_type_description, ppr_location_types.location_type_photo,  ppr_location_types.id, ppr_location_types.location_type_is_published, ppr_location_types.location_type_is_featured LIMIT 6";
    var $locationTypeCards = $('.ppr-feat-locationType')

    /**
     * Render a single PPR Card with its loaded data
     * This finds elements with data attributes named for
     * the properites that will be hydrating them with data
     */
    var renderCard = function(card, data){
        for(var prop in data) {
            // go go gadget recursion
            // Walk through a data object to find our values
            if(typeof data[prop] == 'object'){
                renderCard(card, data[prop])
            }
            // find our html
            var template = $(card).find('[data-'+prop+']')

            if(template.length){
                $(card).find('.ppr-loader').remove()
                $(card).addClass('loaded')
                // if we are not recursing through a data object
                // replace the innner html of our template with data
                if(typeof data[prop] != 'object') {
                    // check if it's an image field
                    let imageRegex = /(photo)|(image)|(img)/;
                    if(imageRegex.test(prop)){
                        if(data[prop] == '' || data[prop] == null ){
                            template.remove()
                            return
                        }
                        template.attr('src', data[prop])
                    } else if (template.attr('href')) {
                        let link = template.attr('href')+data[prop]
                        template.attr('href', link)
                    } else {
                        template.html(data[prop])
                    }

                }
            }
        }
    }

    // get image sizes from the PPR Flickr account
    function getFlickrPhoto (photoID) {
        var flickrAPI = 'https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=d725fbb674d097510cba546d70aa0244&photo_id='+photoID+'&format=json&nojsoncallback=1'
        return $.get(flickrAPI)
    }

    // convenience function to render all cards with data
    function renderCards ($cards, data) {
        $cards.each(function(idx, card){
            if(data[idx]){
               renderCard(card, data[idx])
            } else {
                // if no data fade out card
                $(card)
                  .find('.ppr-loader')
                  .html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i>')
                $(card).addClass('error')
                $(card).animate({opacity: 0}, 250)
            }
        })
    }

    // get our card data from the CartoAPI
    function loadCardData (queryURL, $cards, shouldRender) {
        return $.get(queryURL, function(results){
            var data = results.rows
            if(shouldRender){
                renderCards ($cards, data)
            }
        })
    }


    /* =======================================================================
      Load Things to do cards
    ========================================================================== */
    if($activityCards.length) {
        loadCardData(featActivitiesQueryURL, $activityCards, true)
    }

    /* =======================================================================
      Load Our Locations cards
    ========================================================================== */
    if($locationTypeCards.length) {
        // 1. grab location types from CartoAPI
        loadCardData(featLocationTypesQueryURL, $locationTypeCards, false)
        .then(function(data){
            var locationTypes = data.rows
            // 2. get Flickr image sizes for each location type
            var photos = locationTypes.map(function(lType){
                return getFlickrPhoto(lType.location_type_photo)
            })

            Promise.all(photos).then(locationPhotos =>{
                let small_320_photos = locationPhotos.map(photosData => photosData.sizes.size[4].source)
                // 3. once we have all the location types photos map the medium 320 x 214 photo
                //    back into the locationTypes data
                locationTypes = locationTypes.map((type, idx) =>{
                    type.location_type_photo = small_320_photos[idx]
                    return type
                })
                // 4. render all of our cards w/ photos at the same time
                renderCards ($locationTypeCards, locationTypes)
            })
        })
    }


});
