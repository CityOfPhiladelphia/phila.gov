

jQuery( document ).ready( function( $ ) {

    // if(window.postboxes.page == 'department_page'){

    /**
     * User the WP API backbone model to set the post meta
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


                  setTimeout(function() {
                    department_postMeta.fetch().done(function(data) { console.log(data); });
                  }, 2000);

    }






    $( ".ui-sortable" ).on( "sortchange", function( event, ui ) {


        var postVars, page_columns = $('.columns-prefs input:checked').val() || 0;

            postVars = {
                action: 'meta-box-order',
                _ajax_nonce: window.wpApiSettings.nonce,
                page_columns: page_columns,
                page: window.postboxes.page
            };

            $('.meta-box-sortables').each( function() {
                postVars[ 'order[' + this.id.split( '-' )[0] + ']' ] = $( this ).sortable( 'toArray' ).join( ',' );
            } );




     setPostMeta($("#post_ID").val(), {
                        key: 'phila_meta-box-order',
                        value: postVars['order[normal]']
            });



        // var department_post = new wp.api.models.Departments( { id: $("#post_ID").val() } );
        //     department_post.fetch();

        // setTimeout(function() {
        //     console.log(department_post)
        //     // department_post.set({title: department_post.get('title').rendered+' foo'})
        //     // department_post.save();

        // }, 1000);


    // console.log(department_post);




            // $.ajax({
            //     method:     "POST",
            //     url:        window.location.origin + '/wp-json/wp/v2/department/'+ $("#post_ID").val(),
            //     data:       {
            //         meta:{
            //             'meta-box-order': postVars['order[normal]']
            //         }
            //     },
            //     beforeSend: function ( xhr ) {
            //         xhr.setRequestHeader( 'X-WP-Nonce', postVars._ajax_nonce );
            //     },
            //     success : function( response ) {
            //         console.log( response );
            //     },
            //     fail : function( response ) {
            //         console.log( response );
            //     }
            // });

    } );


    // }//end if

});
