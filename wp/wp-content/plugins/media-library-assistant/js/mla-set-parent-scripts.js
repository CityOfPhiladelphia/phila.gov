/* global ajaxurl, mla  */

var jQuery;

/*
 * This script requires the global "mla" object to be defined and include the following:
 *
 * properties:
 *     mla.settings.useDashicons
 *     mla.settings.ajaxDoneError
 *     mla.settings.ajaxFailError
 *
 * components:
 *     mla.setParent
 */

( function( $ ) {
	/**
	 * setParent displays a popup modal window with a post/page list
	 * from which a new parent can be selected.
	 * setParent.open is called from an "onclick" attribute in the submenu table links
	 */
	mla.setParent = {
		init: function() {
			// Send setParent selected parent
			$( '#mla-set-parent-submit' ).click( function( event ) {
				if ( ! $( '#mla-set-parent-response-div input[type="radio"]:checked' ).length )
					event.preventDefault();
			});

			// Send setParent parent keywords for filtering
			$( '#mla-set-parent-search' ).click( function () {
				$( '#mla-set-parent-paged' ).val( 1 );
				mla.setParent.send();
			});

			$( '#mla-set-parent-search-div :input' ).keypress( function() {
				if ( 13 == event.which ) {
					mla.setParent.send();
					return false;
				}
			});

			// Send post type(s) for filtering
			$( '#mla-set-parent-post-type' ).change( function () {
				$( '#mla-set-parent-paged' ).val( 1 );
				mla.setParent.send();
			});

			// Pagination controls
			$( '#mla-set-parent-previous' ).click( function () {
				var paged = + $( '#mla-set-parent-paged' ).val();

				if ( paged > 1 ) {
					$( '#mla-set-parent-paged' ).val( paged - 1 );
				} else {
					$( '#mla-set-parent-paged' ).val( 1 );
				}

				mla.setParent.send();
			});

			$( '#mla-set-parent-next' ).click( function () {
				var count = + $( '#mla-set-parent-count' ).val(),
					paged = + $( '#mla-set-parent-paged' ).val(),
					found = + $( '#mla-set-parent-found' ).val();

				if ( found < count ) {
					$( '#mla-set-parent-paged' ).val( 1 );
				} else {
					$( '#mla-set-parent-paged' ).val( paged + 1 );
				}

				mla.setParent.send();
			});

			// Close the setParent pop-up
			$( '#mla-set-parent-close-div' ).click( mla.setParent.close );

			$( '#mla-set-parent-cancel' ).click( function ( event ) {
				event.preventDefault();
				return mla.setParent.close();
			});

			// Enable whole row to be clicked
			$( '#mla-set-parent-inside-div' ).on( 'click', 'tr', function() {
				$( this ).find( '.found-radio input' ).prop( 'checked', true );
			});
		},

		open: function( affectedParent, affectedChild, affectedTitles ) {
			var overlay = $( '#mla-set-parent-overlay' );

			if ( overlay.length === 0 ) {
				$( 'body' ).append( '<div id="mla-set-parent-overlay"></div>' );
				mla.setParent.overlay();
			}

			overlay.show();

			if ( affectedParent && affectedChild ) {
				$( '#mla-set-parent-parent' ).val( affectedParent );
				$( '#mla-set-parent-children' ).val( affectedChild );
			}

			if ( affectedTitles ) {
				$( '#mla-set-parent-titles' ).html( affectedTitles );
			}

			if ( mla.settings.useDashicons ) {
				$( '#mla-set-parent-close-div' ).addClass("mla-set-parent-close-div-dashicons");
			} else {
				$( '#mla-set-parent-close-div' ).html( 'x' );
			}

			$( '#mla-set-parent-div' ).show();

			$( '#mla-set-parent-input ' ).focus().keyup( function( event ){
				if ( event.which == 27 ) {
					mla.setParent.close();
				} // close on Escape
			});

			// Pull some results up by default
			mla.setParent.send();

			return false;
		},

		close: function() {
			$( '#mla-set-parent-input' ).val('');
			$( '#mla-set-parent-post-type' ).val('all');
			$( '#mla-set-parent-response-div' ).html('');
			$( '#mla-set-parent-div' ).hide();
			$( '#mla-set-parent-overlay' ).hide();
		},

		overlay: function() {
			$( '#mla-set-parent-overlay' ).on( 'click', function () {
				mla.setParent.close();
			});
		},

		send: function() {
			var post = {
					mla_set_parent_search_text: $( '#mla-set-parent-input' ).val(),
					mla_set_parent_post_type: $( '#mla-set-parent-post-type' ).val(),
					mla_set_parent_count: $( '#mla-set-parent-count' ).val(),
					mla_set_parent_paged: $( '#mla-set-parent-paged' ).val(),
					action: 'mla_find_posts',
					mla_admin_nonce: $('#mla-set-parent-ajax-nonce').val()
				},
				spinner = $( '#mla-set-parent-search-div .spinner' );

			if ( mla.settings.useSpinnerClass ) {
				spinner.addClass("is-active");
			} else {
				spinner.show();
			}

			$.ajax( ajaxurl, {
				type: 'POST',
				data: post,
				dataType: 'json'
			}).always( function() {
				if ( mla.settings.useSpinnerClass ) {
					spinner.removeClass("is-active");
				} else {
					spinner.hide();
				}
			}).done( function( response ) {
				var responseData = 'no response.data', id = 0;

				if ( ! response.success ) {
					if ( response.responseData ) {
						responseData = response.data;
					}

					$( '#mla-set-parent-response-div' ).text( mla.settings.ajaxDoneError + ' (' + responseData + ')' );
				} else {
					/*
					 * Add the (Unattached) row, then the post/page list
					 */
					$( '#mla-set-parent-response-div' ).html( response.data );
					$( '#mla-set-parent-response-div table tbody tr:eq(0)' ).before( $( '#found-0-row' ).clone() );

					/*
					 * See if we can "check" the current parent
					 */
					id = $( '#mla-set-parent-parent' ).val();
					$( '#mla-set-parent-response-div #found-' + id ).each(function(){
						$( this ).prop( 'checked', true );
					});
				}
			}).fail( function( jqXHR, status ) {
				if ( 200 == jqXHR.status ) {
					$( '#mla-set-parent-response-div' ).text( '(' + status + ') ' + jqXHR.responseText );
				} else {
					$( '#mla-set-parent-response-div' ).text( mla.settings.ajaxFailError + ' (' + status + '), jqXHR( ' + jqXHR.status + ', ' + jqXHR.statusText + ', ' + jqXHR.responseText + ')' );
				}
			});
		}
	}; // mla.setParent

	$( document ).ready( function() {
		mla.setParent.init();
	});
})( jQuery );
