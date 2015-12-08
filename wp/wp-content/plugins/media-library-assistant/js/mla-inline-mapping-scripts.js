/* global ajaxurl */

var jQuery,
	mla_inline_mapping_vars,
	mla = {
		// Properties
		settings: {},
		bulkMap: {
			inProcess: false,
			doCancel: false
		},

		// Utility functions
		utility: {
		},

		// Components
		inlineMapAttachment: null
	};

( function( $ ) {
	/**
	 * Localized settings and strings
	 */
	mla.settings = typeof mla_inline_mapping_vars === 'undefined' ? {} : mla_inline_mapping_vars;
	mla_inline_mapping_vars = void 0; // delete won't work on Globals

	mla.inlineMapAttachment = {
		init : function(){
			var progressDiv = $( '#mla-progress-div' );

			$('#mla-progress-cancel', progressDiv).off( 'click' );
			$('#mla-progress-cancel', progressDiv).click( function(){
				if ( mla.bulkMap.inProcess ) {
					mla.bulkMap.doCancel = true;
					return false;
				} else {
					return mla.inlineMapAttachment.revert();
				}
			});

			$('#mla-progress-resume', progressDiv).off( 'click' );
			$('#mla-progress-resume', progressDiv).click( function(){
				var totalItems = +mla.settings.totalItems, newOffset = + $( '#mla-progress-offset' ).val();

				if ( totalItems < newOffset ) {
					newOffset = totalItems;
				} else {
					if ( 0 > newOffset ) {
						newOffset = 0;
					}
				}

				if ( mla.bulkMap.inProcess ) {
					mla.bulkMap.doCancel = true;
					return false;
				} else {
					return mla.inlineMapAttachment.bulkMap( mla.bulkMap.targetName, newOffset );
				}
			});

			// Clicking "Refresh" submits the form, refreshing the page
			$( '#mla-progress-refresh', progressDiv ).off( 'click' );
			$( '#mla-progress-refresh', progressDiv ).click( function(){
				$( '#mla-progress-refresh' ).prop( 'disabled', true );
				$( '#mla-progress-refresh' ).css( 'opacity', '0.5' );
			});

			$('#mla-progress-close', progressDiv).off( 'click' );
			$('#mla-progress-close', progressDiv).click( function( e ){
				if ( mla.bulkMap.inProcess ) {
					return false;
				}

				return mla.inlineMapAttachment.revert();
			});

			// add event handler to the Map All links
			$( 'input[type="submit"].mla-mapping' ).click(function( e ){
				e.preventDefault();
				return mla.inlineMapAttachment.bulkMap( e.target.name, 0 );
			});
		},

		bulkMap : function( action, initialOffset ) {
			var oldComplete = 0, oldUnchanged = 0, oldSuccess = 0, oldSkip = 0, oldRedone = 0;

			initialOffset = +initialOffset;

			if ( 0 < initialOffset ) {
				oldComplete = typeof mla.bulkMap.complete === 'undefined' ? 0 : mla.bulkMap.complete;
				oldUnchanged = typeof mla.bulkMap.unchanged === 'undefined' ? 0 : mla.bulkMap.unchanged;
				oldSuccess = typeof mla.bulkMap.success === 'undefined' ? 0 : mla.bulkMap.success;
				oldSkip = typeof mla.bulkMap.skip === 'undefined' ? 0 : mla.bulkMap.skip;
				oldRedone = typeof mla.bulkMap.redone === 'undefined' ? 0 : mla.bulkMap.redone;
			}

			// See if we're skipping or re-processing any items
			if ( oldComplete < initialOffset ) {
				oldSkip += initialOffset - oldComplete;
			} else {
				if ( oldComplete > initialOffset ) {
					oldRedone += oldComplete - initialOffset;
				}
			}

			mla.bulkMap = {
				inProcess: false,
				doCancel: false,
				chunkSize: +mla.settings.bulkChunkSize,
				targetName: action,
				fields: $( mla.settings.fieldsId + ' :input').serialize(),
				offset: initialOffset,
				waiting: mla.settings.totalItems - initialOffset,
				running: 0,
				complete: initialOffset,
				unchanged: oldUnchanged,
				success: oldSuccess,
				skip: oldSkip,
				redone: oldRedone,
				refresh: false
			};

			mla.inlineMapAttachment.progressOpen();
			mla.inlineMapAttachment.bulkPost();
			return false;
		},

		progressOpen : function(){
			this.revert();

			$( '#mla-progress-meter' ).css( 'width', '0%' );
			$( '#mla-progress-meter' ).html('0%');
			$( '#mla-progress-message' ).html('');
			$( '#mla-progress-error' ).html('');
			$( '#mla-progress-div' ).show();

			// Disable "Close" until the bulk mapping is complete
			$( '#mla-progress-cancel' ).prop( 'disabled', false ).css( 'opacity', '1.0' );
			$( '#mla-progress-resume' ).hide();
			$( '#mla-progress-offset' ).hide();
			$( '#mla-progress-refresh' ).hide();
			$( '#mla-progress-close' ).prop( 'disabled', true ).css( 'opacity', '0.5' ).show();
			$( 'html, body' ).animate( { scrollTop: 0 }, 'fast' );
		},

		bulkPost : function() {
			var params, chunk, statusMessage = '',
				spinner = $('#mla-progress-div p.inline-edit-save .spinner'),
				message = $( '#mla-progress-message' ),
				error = $( '#mla-progress-error' );

			// Find the number of items to process
			if ( mla.bulkMap.waiting < mla.bulkMap.chunkSize ) {
				chunk = mla.bulkMap.waiting;
			} else {
				chunk = mla.bulkMap.chunkSize;
			}

			mla.bulkMap.waiting -= chunk;
			mla.bulkMap.running = chunk;

			params = {
				page: mla.settings.page,
				mla_tab: mla.settings.mla_tab,
				screen: mla.settings.screen,
				action: mla.settings.ajax_action,
				mla_admin_nonce: mla.settings.ajax_nonce,
				bulk_action: mla.bulkMap.targetName,
				offset: mla.bulkMap.complete,
				length: chunk
			};

			params = $.param( params ) + '&' + mla.bulkMap.fields;

			// make ajax request
			mla.bulkMap.inProcess = true;

			percentComplete = Math.floor( ( 100 * mla.bulkMap.complete ) / mla.settings.totalItems ) + '%';
			$( '#mla-progress-meter' ).css( 'width', percentComplete );
			$( '#mla-progress-meter' ).html( percentComplete );

			if ( 0 < mla.bulkMap.skip ) {
				statusMessage += ', ' + mla.settings.bulkSkip + ': ' + mla.bulkMap.skip;
			}

			if ( 0 < mla.bulkMap.redone ) {
				statusMessage += ', ' + mla.settings.bulkRedone + ': ' + mla.bulkMap.redone;
			}

			if ( mla.settings.useSpinnerClass ) {
				spinner.addClass("is-active");
			} else {
				spinner.show();
			}

			statusMessage = mla.settings.bulkWaiting + ': ' + mla.bulkMap.waiting
				+ ', ' + mla.settings.bulkRunning + ': ' + mla.bulkMap.running
				+ ', ' + mla.settings.bulkComplete + ': ' + mla.bulkMap.complete
				+ statusMessage // skip and redone
				+ ', ' + mla.settings.bulkUnchanged + ': ' + mla.bulkMap.unchanged
				+ ', ' + mla.settings.bulkSuccess + ': ' + mla.bulkMap.success;
			message.html( statusMessage ).show();

			$.ajax( ajaxurl, {
				type: 'POST',
				data: params,
				dataType: 'json'
			}).always( function() {
				if ( mla.settings.useSpinnerClass ) {
					spinner.removeClass("is-active");
				} else {
					spinner.hide();
				}
			}).done( function( response, status ) {
					var responseData = 'no response.data', responseMessage = '';

					if ( response ) {
						if ( ! response.success ) {
							if ( response.responseData ) {
								responseData = response.data;
							}

							error.html( JSON.stringify( response ) );
							mla.bulkMap.waiting = 0; // Stop
						} else {
							if ( 0 == response.data.processed ) {
								// Something went wrong; we're done
								responseMessage = response.data.message;
								mla.bulkMap.waiting = 0; // Stop
							} else {
								// Move the items from Running to Complete
								mla.bulkMap.complete += response.data.processed;
								mla.bulkMap.running = 0;
								mla.bulkMap.unchanged += response.data.unchanged;
								mla.bulkMap.success += response.data.success;

								if ( 'undefined' !== typeof response.data.refresh ) {
									mla.bulkMap.refresh = response.data.refresh;
								}

								percentComplete = Math.floor( ( 100 * mla.bulkMap.complete ) / mla.settings.totalItems ) + '%';
								$( '#mla-progress-meter' ).css( 'width', percentComplete );
								$( '#mla-progress-meter' ).html( percentComplete );

								if ( 0 < mla.bulkMap.skip ) {
									responseMessage += ', ' + mla.settings.bulkSkip + ': ' + mla.bulkMap.skip;
								}

								if ( 0 < mla.bulkMap.redone ) {
									responseMessage += ', ' + mla.settings.bulkRedone + ': ' + mla.bulkMap.redone;
								}

								responseMessage = mla.settings.bulkWaiting + ': ' + mla.bulkMap.waiting
									+ ', ' + mla.settings.bulkComplete + ': ' + mla.bulkMap.complete
									+ responseMessage // skip and redone
									+ ', ' + mla.settings.bulkUnchanged + ': ' + mla.bulkMap.unchanged
									+ ', ' + mla.settings.bulkSuccess + ': ' + mla.bulkMap.success;
							}
							message.html( responseMessage ).show();
						}
					} else {
						error.html( mla.settings.error );
						mla.bulkMap.waiting = 0; // Stop
					}

					if ( mla.bulkMap.doCancel ) {
						message.html( mla.settings.bulkCanceled + '. ' +  responseMessage ).show();
						$( '#mla-progress-resume' ).show();
						$( '#mla-progress-offset' ).val( mla.bulkMap.complete ).show();
					} else {
						if ( mla.bulkMap.waiting ) {
							mla.inlineMapAttachment.bulkPost();
							return;
						}
					}

					if ( mla.bulkMap.refresh ) {
						$( '#mla-progress-close' ).hide();
						$( '#mla-progress-refresh' ).prop( 'disabled', false ).css( 'opacity', '1.0' ).show();
					} else {
						$( '#mla-progress-close' ).prop( 'disabled', false ).css( 'opacity', '1.0' );
					}

					$( '#mla-progress-cancel' ).prop( 'disabled', true ).css( 'opacity', '0.5' );
					mla.bulkMap.inProcess = false;
			}).fail( function( jqXHR, status ) {
				if ( 200 == jqXHR.status ) {
					error.html( '(' + status + ') ' + jqXHR.responseText );
				} else {
					error.html( mla.settings.ajaxFailError + ' (' + status + '), jqXHR( ' + jqXHR.status + ', ' + jqXHR.statusText + ', ' + jqXHR.responseText + ')' );
				}
			});
		},

		revert : function(){
			var progressDiv = $( '#mla-progress-div' );

			if ( progressDiv ) {
				if ( mla.settings.useSpinnerClass ) {
					$('p.inline-edit-save .spinner', progressDiv ).removeClass("is-active");
				} else {
					$('p.inline-edit-save .spinner', progressDiv ).hide();
				}

				// Reset Div content to initial values

				$( progressDiv ).hide();
			}

			return false;
		}
	}; // mla.inlineMapAttachment

	$( document ).ready( function() {
		mla.inlineMapAttachment.init();
	});
})( jQuery );
