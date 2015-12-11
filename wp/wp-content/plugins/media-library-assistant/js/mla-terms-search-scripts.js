/* global ajaxurl */

var jQuery, wpAjax,
	mla_terms_search_vars,
	mlaTaxonomy = {
		// Properties
		settings: {},

		// Utility functions
		utility: {
		},

		// Components
		termsSearch: null
	};

( function( $ ) {
	/**
	 * Localized settings and strings
	 */
	mlaTaxonomy.settings = typeof mla_terms_search_vars === 'undefined' ? {} : mla_terms_search_vars;
	mla_terms_search_vars = void 0; // delete won't work on Globals

	/**
	 * termsSearch displays a popup modal window with text box, options and taxonomy list.
	 * termsSearch.open is called from an "onclick" attribute in the Media/Assistant submenu table.
	 */
	mlaTaxonomy.termsSearch = {
		init: function() {
			// Suppress form "submit" action for the "Terms Search" button
			$( '#mla-terms-search-open' ).click( function( event ) {
				event.preventDefault();
			});

			$( '#mla-terms-search-submit' ).click( function() {
				mlaTaxonomy.termsSearch.close();
			});

			// Close the termsSearch pop-up
			$( '#mla-terms-search-close-div' ).click( mlaTaxonomy.termsSearch.close );

		},

		open: function() {
			var overlay = $( '#mla-terms-search-overlay' );

			if ( overlay.length === 0 ) {
				$( 'body' ).append( '<div id="mla-terms-search-overlay"></div>' );
				mlaTaxonomy.termsSearch.overlay();
			}

			overlay.show();

			if ( mlaTaxonomy.settings.useDashicons ) {
				$( '#mla-terms-search-close-div' ).addClass("mla-terms-search-close-div-dashicons");
			} else {
				$( '#mla-terms-search-close-div' ).html( 'x' );
			}
			/* if ( ! mlaTaxonomy.settings.useDashicons ) {
				$( '#mla-terms-search-close-div' ).html( 'x' );
			} */

			$( '#mla-terms-search-div' ).show();

			$( '#mla-terms-search-input ' ).focus().keyup( function( event ){
				if ( event.which == 27 ) {
					mlaTaxonomy.termsSearch.close();
				} // close on Escape
			});

			return false;
		},

		close: function() {
			$( '#mla-terms-search-response-div' ).html('');
			$( '#mla-terms-search-div' ).hide();
			$( '#mla-terms-search-overlay' ).hide();
		},

		overlay: function() {
			$( '#mla-terms-search-overlay' ).on( 'click', function () {
				mlaTaxonomy.termsSearch.close();
			});
		},

		send: function() {
			var post = {
					ps: $( '#mla-terms-search-input' ).val(),
					action: 'find_posts',
					_ajax_nonce: $('#mla-terms-search-ajax-nonce').val()
				},
				spinner = $( '#mla-terms-search-search-div .spinner' ),
				ajaxResponse = null;

			if ( mla.settings.useSpinnerClass ) {
				spinner.addClass("is-active");
			} else {
				spinner.show();
			}

			$.ajax( ajaxurl, {
				type: 'POST',
				data: post,
				dataType: mlaTaxonomy.settings.termsSearchDataType
			}).always( function() {
				if ( mla.settings.useSpinnerClass ) {
					spinner.removeClass("is-active");
				} else {
					spinner.hide();
				}
			}).done( function( response ) {
				var responseData = 'no response.data', id = 0;

				if ( 'xml' === mlaTaxonomy.settings.termsSearchDataType ) {
					if ( 'string' === typeof( response ) ) {
						response = { 'success': false, data: response };
					} else {
						ajaxResponse = wpAjax.parseAjaxResponse( response );

						if ( ajaxResponse.errors ) {
							response = { 'success': false, data: wpAjax.broken };
						} else {
							response = { 'success': true, data: ajaxResponse.responses[0].data };
						}
					}
				}

				if ( ! response.success ) {
					if ( response.responseData ) {
						responseData = response.data;
					}

					$( '#mla-terms-search-response-div' ).text( mlaTaxonomy.settings.ajaxDoneError + ' (' + responseData + ')' );
				} else {
					/*
					 * Add the (Unattached) row, then the post/page list
					 */
					$( '#mla-terms-search-response-div' ).html( response.data );
					$( '#mla-terms-search-response-div table tbody tr:eq(0)' ).before( $( '#found-0-row' ).clone() );

					/*
					 * See if we can "check" the current parent
					 */
					id = $( '#mla-terms-search-parent' ).val();
					$( '#mla-terms-search-response-div #found-' + id ).each(function(){
						$( this ).prop( 'checked', true );
					});
				}
			}).fail( function( jqXHR, status ) {
				if ( 200 == jqXHR.status ) {
					$( '#mla-terms-search-response-div' ).text( '(' + status + ') ' + jqXHR.responseText );
				} else {
					$( '#mla-terms-search-response-div' ).text( mlaTaxonomy.settings.ajaxFailError + ' (' + status + '), jqXHR( ' + jqXHR.status + ', ' + jqXHR.statusText + ', ' + jqXHR.responseText + ')' );
				}
			});
		}
	}; // mlaTaxonomy.termsSearch

	$( document ).ready( function() {
		mlaTaxonomy.termsSearch.init();
	});
})( jQuery );
