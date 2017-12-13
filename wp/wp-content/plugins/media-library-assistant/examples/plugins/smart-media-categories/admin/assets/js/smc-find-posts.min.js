/* global ajaxurl */

var smc = {
	// Properties
	settings: {},

	// Utility functions
	utility: {
	},

	// Components
	findPosts: null,
	syncPosts: null,
	bulkActions: null
};

( function( $ ){
	/**
	 * Localized settings and strings
	 */
	smc.settings = typeof smart_media_categories_posts_settings === 'undefined' ? {} : smart_media_categories_posts_settings;
	smart_media_categories_posts_settings = void 0; // delete won't work on Globals

	/**
	 * findPosts displays a popup modal window with a post/page list
	 * from which a new parent can be selected.
	 * findPosts.open is called from an "onclick" attribute in the submenu table links
	 */
	smc.findPosts = {
		init: function() {
			// Firefox and Safari don't support innerText; they use textContent.
			// smc.settings.useInnerText = typeof document.getElementsByTagName("body")[0].innerText !== 'undefined';
			
			// Send findPosts selected parent
			$( '#smc-posts-modal-submit' ).click( function( event ) {
				if ( ! $( '#smc-posts-modal-response-div input[type="radio"]:checked' ).length )
					event.preventDefault();
			});
			
			// Send findPosts parent keywords for filtering
			$( '#smc-posts-modal-search' ).click( function ( event ) {
				$( '#smc-set-parent-paged' ).val( 1 );
				smc.findPosts.send();
			});
			
			$( '#smc-posts-modal-search-div :input' ).keypress( function( event ) {
				if ( 13 == event.which ) {
					smc.findPosts.send();
					return false;
				}
			});
			
			// Send post type(s) for filtering
			$( '#smc-set-parent-post-type' ).change( function ( event ) {
				$( '#smc-set-parent-paged' ).val( 1 );
				smc.findPosts.send();
			});
			
			// Pagination controls
			$( '#smc-set-parent-previous' ).click( function ( event ) {
				var paged = + $( '#smc-set-parent-paged' ).val();
				
				if ( paged > 1 ) {
					$( '#smc-set-parent-paged' ).val( paged - 1 );
				} else {
					$( '#smc-set-parent-paged' ).val( 1 );
				}
				
				smc.findPosts.send();
			});
			
			$( '#smc-set-parent-next' ).click( function ( event ) {
				var count = + $( '#smc-set-parent-count' ).val(),
					paged = + $( '#smc-set-parent-paged' ).val(),
					found = + $( '#smc-set-parent-found' ).val();
				
				if ( found < count ) {
					$( '#smc-set-parent-paged' ).val( 1 );
				} else {
					$( '#smc-set-parent-paged' ).val( paged + 1 );
				}
				
				smc.findPosts.send();
			});
			
			// Close the findPosts pop-up
			$( '#smc-posts-modal-close-div' ).click( smc.findPosts.close );
			
			$( '#smc-posts-modal-cancel' ).click( function ( event ) {
				event.preventDefault();
				return smc.findPosts.close();
			});

			// Enable whole row to be clicked
			$( '#smc-posts-modal-inside-div' ).on( 'click', 'tr', function() {
				$( this ).find( '.found-radio input' ).prop( 'checked', true );
			});
		},

		open: function( affectedParent, affectedChild, affectedTitles ) {
			var overlay = $( '#smc-find-overlay' );

			if ( overlay.length === 0 ) {
				$( 'body' ).append( '<div id="smc-find-overlay"></div>' );
				smc.findPosts.overlay();
			}

			overlay.show();

			if ( affectedParent && affectedChild ) {
				$( '#smc-posts-modal-parent' ).val( affectedParent );
				$( '#smc-posts-modal-children' ).val( affectedChild );
			}

			if ( affectedTitles ) {
				$( '#smc-posts-modal-titles' ).html( affectedTitles );
			}

			if ( smc.settings.useDashicons ) {
				$( '#smc-posts-modal-close-div' ).addClass("smc-posts-modal-close-div-dashicons");
			} else {
				$( '#smc-posts-modal-close-div' ).html( 'x' );
			}
			
			$( '#smc-posts-modal-div' ).show();

			$( '#smc-posts-modal-input ' ).focus().keyup( function( event ){
				if ( event.which == 27 ) {
					smc.findPosts.close();
				} // close on Escape
			});

			// Pull some results up by default
			smc.findPosts.send();

			return false;
		},

		close: function() {
			$( '#smc-posts-modal-response-div' ).html('');
			$( '#smc-posts-modal-div' ).hide();
			$( '#smc-find-overlay' ).hide();
		},

		overlay: function() {
			$( '#smc-find-overlay' ).on( 'click', function () {
				smc.findPosts.close();
			});
		},

		send: function() {
			var post = {
					smc_set_parent_search_text: $( '#smc-posts-modal-input' ).val(),
					smc_set_parent_post_type: $( '#smc-set-parent-post-type' ).val(),
					smc_set_parent_count: $( '#smc-set-parent-count' ).val(),
					smc_set_parent_paged: $( '#smc-set-parent-paged' ).val(),
					action: 'smc_find_posts',
					_ajax_nonce: $('#smc-posts-modal-ajax-nonce').val()
				},
				spinner = $( '#smc-posts-modal-search-div .spinner' ),
				ajaxResponse = null;

			spinner.show();

			$.ajax( ajaxurl, {
				type: 'POST',
				data: post,
				dataType: 'json'
			}).always( function() {
				spinner.hide();
			}).done( function( response ) {
				var responseData = 'no response.data', id = 0;
				
				if ( ! response.success ) {
					if ( response.responseData ) {
						responseData = response.data;
					}
						
					$( '#smc-posts-modal-response-div' ).text( smc.settings.ajaxDoneError + ' (' + responseData + ')' );
				} else {
					/*
					 * Add the (Unattached) row, then the post/page list
					 */
					$( '#smc-posts-modal-response-div' ).html( response.data );
					$( '#smc-posts-modal-response-div table tbody tr:eq(0)' ).before( $( '#found-0-row' ).clone() );

					/*
					 * See if we can "check" the current parent
					 */
					id = $( '#smc-posts-modal-parent' ).val();
					$( '#smc-posts-modal-response-div #found-' + id ).each(function( index, element ){
						$( this ).prop( 'checked', true );
					});
				}
			}).fail( function( jqXHR, status ) {
				if ( 200 == jqXHR.status ) {
					$( '#smc-posts-modal-response-div' ).text( '(' + status + ') ' + jqXHR.responseText + ' ' +  JSON.stringify( post ) );
				} else {
					$( '#smc-posts-modal-response-div' ).text( smc.settings.ajaxFailError + ' (' + status + '), jqXHR( ' + jqXHR.status + ', ' + jqXHR.statusText + ', ' + jqXHR.responseText + ') ' +  JSON.stringify( post ) );
				}
			});
		}
	}; // smc.findPosts
	
	$( document ).ready( function() {
		// Initialize the findPosts module
		smc.findPosts.init();
	});
})( jQuery );
