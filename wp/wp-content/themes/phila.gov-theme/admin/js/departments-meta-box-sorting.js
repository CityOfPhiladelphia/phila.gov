

jQuery( document ).ready( function( $ ) {


    // make sure you are editing a department page and have admin role permissions
    if(window.postboxes && window.postboxes.page == 'department_page' && window.phila_WP_User.indexOf('administrator') != -1){

    /**
     * Use the WP API backbone model to set the post meta
     * @param {string} post_id   the post id to update the meta data for
     * @param {Object} post_meta {key: metadata kye, value: meta data value to save }
     */
    function setPostMeta(post_id, post_meta){
        console.log('fetch post meta for post: '+ post_id);

            var department_postMeta = new wp.api.collections.PostMeta('',{parent: post_id});

                department_postMeta.fetch()
                  .done(function(data) {
                    console.log(data)
                    var someKey = department_postMeta.findWhere({key: post_meta.key});
                    someKey.set('value', post_meta.value);
                    someKey.save({parent: post_id});

                  });
    }





    /**
     * Once the admin user is done dragging the meta boxes save the order to post meta
     */
    $( ".ui-sortable" ).on( "sortupdate", function( event, ui ) {


        var postVars ={},
            page_columns = $('.columns-prefs input:checked').val() || 0;


            $('.meta-box-sortables').each( function() {

              if(this.id.split( '-' )[0] == 'normal'){
                var sortables = $( this ).sortable( 'toArray' );
                var visible_sortables = [];

                for (var i = sortables.length - 1; i >= 0; i--) {
                    if($("#"+sortables[i]).data('visible') == 'visible'){
                        visible_sortables.push(sortables[i])
                    }
                }

                postVars[ 'order[' + this.id.split( '-' )[0] + ']' ] = visible_sortables.reverse().join(',');
                // postVars[ 'order[' + this.id.split( '-' )[0] + ']' ] = $( this ).sortable( 'toArray' ).join( ',' );

            }


            } );


         setPostMeta($("#post_ID").val(), { key: 'phila_meta-box-order', value: postVars['order[normal]'] });


    } );


    }//end if(window.postboxes.page == 'department_page')

});
