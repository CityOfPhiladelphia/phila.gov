/* global ajaxurl */

var jQuery,
	mla_polylang_support_vars,
	mlaPolylang = {
		// Properties
		// mlaPolylang.settings.noTitle
		// mlaPolylang.settings.ntdelTitle
		// mlaPolylang.settings.fields
		// mlaPolylang.settings.comma
		// mlaPolylang.settings.ajax_action
		// mlaPolylang.settings.ajax_nonce
		// mlaPolylang.settings.error
		settings: {},

		// Utility functions
		utility: {
			getId : function( o ) {
				var id = jQuery( o ).closest( 'tr' ).attr( 'id' ),
					parts = id.split( '-' );
				return parts[ parts.length - 1 ];
			}
		},

		// Components
		inlineTranslate: null
	};

( function( $ ) {
	/**
	 * Localized settings and strings
	 */
	mlaPolylang.settings = typeof mla_polylang_support_vars === 'undefined' ? {} : mla_polylang_support_vars;
	mla_polylang_support_vars = void 0; // delete won't work on Globals

	mlaPolylang.inlineTranslate = {
		init : function(){
			var t = this, qtRow = $( '#pll-quick-translate' ), btRow = $( '#pll-bulk-translate' );

			t.type = 'attachment';
			t.what = '#attachment-';

			// prepare the quick-translate row
			qtRow.keyup( function( e ){
				if ( e.which == 27 )
					return mlaPolylang.inlineTranslate.revert();
			});

			$( 'a.cancel', qtRow ).click( function(){
				return mlaPolylang.inlineTranslate.revert();
			});

			$( 'a.save', qtRow ).click( function(){
				return mlaPolylang.inlineTranslate.save( this );
			});

			$( 'td', qtRow ).keydown( function(e){
				if ( e.which == 13 )
					return mlaPolylang.inlineTranslate.save( this );
			});

			// add event to the Quick Translate links
			$( '#the-list' ).on( 'click', 'a.inlineTranslate', function(){
				mlaPolylang.inlineTranslate.edit( this );
				return false;
			});

			/*
			 * Add event to the Quick Edit links. This click function runs before
			 * the Quick Edit row is created; the focusin() event delays action until
			 * everything is ready.
			 */
			$( '#the-list' ).on( 'click', 'a.editinline', function(){
				$( '.quick-edit-row' ).one( 'focusin', function() {
					mlaPolylang.inlineTranslate.openQuickEdit( this );
					return false;
				});

				return false;
			});

			// prepare the bulk-translate row
			btRow.keyup( function( e ){
				if ( e.which == 27 )
					return mlaPolylang.inlineTranslate.revert();
			});

			$( 'a.cancel', btRow ).click( function(){
				return mlaPolylang.inlineTranslate.revert();
			});

			$( '#doaction, #doaction2' ).click( function( e ){
				var n = $( this ).attr( 'id' ).substr( 2 );

				if ( $( 'select[name="'+n+'"]' ).val() == 'pll-translate' ) {
					e.preventDefault();
					t.openBulkTranslate();
				} else if ( $( 'form#posts-filter tr.inline-translator' ).length > 0 ) {
					t.revert();
				}
			});

			// Filter button (dates, categories) in top nav bar
			$( '#post-query-submit' ).mousedown( function(){
				t.revert();
				$( 'select[name^="action"]' ).val( '-1' );
			});
		},

		toggle : function( el ){
			var t = this;

			if ( 'none' == $( t.what + mlaPolylang.utility.getId( el ) ).css( 'display' ) ) {
				t.revert();
			} else {
				t.edit( el );
			}
		},

		openBulkTranslate : function(){
			var te = '', c = true;
			this.revert();

			// Open up the Bulk Translate area
			$( '#pll-bulk-translate td' ).attr( 'colspan', $( 'th:visible, td:visible', '.widefat:first thead' ).length );
			$( 'table.widefat tbody' ).prepend( $( '#pll-bulk-translate' ) );
			$( '#pll-bulk-translate' ).addClass( 'inline-translator' ).show();

			// Make sure at least one item has been selected
			$( 'tbody th.check-column input[type="checkbox"]' ).each( function(){
				if ( $( this ).prop( 'checked' ) ) {
					c = false;
					var id = $( this ).val(), theTitle;
					theTitle = $( '#inline_'+id+' .post_title' ).text() || mlaPolylang.settings.noTitle;
					te += '<div id="ttle'+id+'"><a id="_'+id+'" class="ntdelbutton" title="'+mlaPolylang.settings.ntdelTitle+'">X</a>'+theTitle+'</div>';
				}
			});

			if ( c ) {
				return this.revert();
			}

			// Populate the list of selected items
			$( '#pll-bulk-titles' ).html( te );
			$( '#pll-bulk-titles a' ).click(function(){
				var id = $( this ).attr( 'id' ).substr( 1 );

				$( 'table.widefat input[value="' + id + '"]' ).prop( 'checked', false );
				$( '#ttle'+id ).remove();
			});

			// Capture Language links
			$( '#pll-bulk-translate .pll-media-action-table tr' ).each( function ( idx ){
				//$( '.pll-media-action-column a', this ).off( 'click' );
				$( '.pll-media-action-column a', this ).click( function( e ){
					var bulkLanguage = $( this ).attr( 'pll_bulk_language' );

					$( ':input[name="pll_bulk_language"]', '#pll-bulk-translate' ).val( bulkLanguage );
					$( '#pll-bulk-translate .pll-media-action-table a' ).prop( 'disabled', true );
					$( '#pll-bulk-translate' ).css( 'opacity', '0.5' );
					$( this ).hide();
					return $( '#pll-bulk-translate-submit', '#pll-bulk-translate' ).click();
				});
			});

			$( 'html, body' ).animate( { scrollTop: 0 }, 'fast' );
		},

		openQuickEdit : function( id ) {
			var translateRow, translations, currentLanguage;

			if ( typeof( id ) == 'object' )
				id = mlaPolylang.utility.getId( id );

			// find the Quick Edit row
			translateRow = $( '#edit-' + id );

			// set up the quick translate entries
			currentLanguage = $( ':input[name="old_lang"]', translateRow ).val();
			translations = JSON.parse( $( ':input[name="inline_translations"]', translateRow ).val() );

			$( '.pll-media-action-table tr', translateRow ).each( function ( idx ){
				var parts = $( this ).attr( 'class' ).split( '-' ), slug = parts[ parts.length - 1 ];

				if ( slug === currentLanguage ) {
					$( '.pll-media-action-column input', this ).val( translations[ slug ] );
					$( this ).hide();
				} else if ( 'undefined' != typeof translations[ slug ] ) {
					$( '.pll-media-action-column input', this ).val( translations[ slug ] );
					$( '.pll-media-action-column a', this ).addClass( 'pll_icon_edit' );
					$( '.pll-media-action-column a', this ).attr( 'title', mlaPolylang.settings.edit );
					$( '.pll-media-action-column a', this ).attr( 'pll_quick_language', slug );
					$( '.pll-media-action-column a', this ).attr( 'pll_quick_id', translations[ slug ] );
				} else {
					$( '.pll-media-action-column input', this ).val( 0 );
					$( '.pll-media-action-column a', this ).addClass( 'pll_icon_add' );
					$( '.pll-media-action-column a', this ).attr( 'title', mlaPolylang.settings.addNew );
					$( '.pll-media-action-column a', this ).attr( 'pll_quick_language', slug );
					$( '.pll-media-action-column a', this ).attr( 'pll_quick_id', 0 );
				}

				$( '.pll-media-action-column a', this ).off( 'click' );
				$( '.pll-media-action-column a', this ).click( function( e ){
					var quickLanguage = $( this ).attr( 'pll_quick_language' ), quickId = $( this ).attr( 'pll_quick_id' );

					$( ':input[name="pll_quick_language"]', translateRow ).val( quickLanguage );
					$( ':input[name="pll_quick_id"]', translateRow ).val( quickId );
					e.preventDefault();
					return mlaPolylang.inlineTranslate.save( translateRow );
				});
			});

			return false;
		},

		edit : function( id ) {
			var t = this, fields, translateRow, rowData, fIndex, translations, currentLanguage;
			t.revert();

			if ( typeof( id ) == 'object' )
				id = mlaPolylang.utility.getId( id );

			fields = mlaPolylang.settings.fields;

			// add the new Quick Translate row before its corresponding item
			translateRow = $( '#pll-quick-translate' ).clone( true );
			$( 'td', translateRow ).attr( 'colspan', $( 'th:visible, td:visible', '.widefat:first thead' ).length );

			if ( $( t.what+id ).hasClass( 'alternate' ) )
				$( translateRow ).addClass( 'alternate' );

			$( t.what+id ).before( translateRow );

			// populate the data
			rowData = $( '#inline_'+id );

			for ( fIndex = 0; fIndex < fields.length; fIndex++ ) {
				$( ':input[name="' + fields[fIndex] + '"]', translateRow ).val( $( '.'+fields[fIndex], rowData ).text() );
			}

			// set up the quick translate entries
			currentLanguage = $( ':input[name="old_lang"]', translateRow ).val();
			translations = JSON.parse( $( ':input[name="inline_translations"]', translateRow ).val() );

			$( '.pll-media-action-table tr', translateRow ).each( function ( idx ){
				var parts = $( this ).attr( 'class' ).split( '-' ), slug = parts[ parts.length - 1 ];

				if ( slug === currentLanguage ) {
					$( '.pll-media-action-column input', this ).val( translations[ slug ] );
					$( this ).hide();
				} else if ( 'undefined' != typeof translations[ slug ] ) {
					$( '.pll-media-action-column input', this ).val( translations[ slug ] );
					$( '.pll-media-action-column a', this ).addClass( 'pll_icon_edit' );
					$( '.pll-media-action-column a', this ).attr( 'title', mlaPolylang.settings.edit );
					$( '.pll-media-action-column a', this ).attr( 'pll_quick_language', slug );
					$( '.pll-media-action-column a', this ).attr( 'pll_quick_id', translations[ slug ] );
				} else {
					$( '.pll-media-action-column input', this ).val( 0 );
					$( '.pll-media-action-column a', this ).addClass( 'pll_icon_add' );
					$( '.pll-media-action-column a', this ).attr( 'title', mlaPolylang.settings.addNew );
					$( '.pll-media-action-column a', this ).attr( 'pll_quick_language', slug );
					$( '.pll-media-action-column a', this ).attr( 'pll_quick_id', 0 );
				}

				$( '.pll-media-action-column a', this ).off( 'click' );
				$( '.pll-media-action-column a', this ).click( function( e ){
					var quickLanguage = $( this ).attr( 'pll_quick_language' ), quickId = $( this ).attr( 'pll_quick_id' );

					$( ':input[name="pll_quick_language"]', translateRow ).val( quickLanguage );
					$( ':input[name="pll_quick_id"]', translateRow ).val( quickId );
					return mlaPolylang.inlineTranslate.save( translateRow );
				});
			});

			rowData = $( translateRow ).attr( 'id', 'edit-'+id ).addClass( 'inline-translator' ).show().position().top;
			$( 'html, body' ).animate( { scrollTop: rowData }, 'fast' );

			return false;
		},

		save : function( id ) {
			var params, fields, page = $( '.post_status_page' ).val() || '';

			if ( typeof( id ) == 'object' )
				id = mlaPolylang.utility.getId( id );

			if ( mla.settings.useSpinnerClass ) {
				$( 'table.widefat .pll-quick-translate-save .spinner' ).addClass("is-active");
			} else {
				$( 'table.widefat .pll-quick-translate-save .spinner' ).show();
			}

			params = {
				action: mlaPolylang.settings.ajax_action,
				mla_admin_nonce: mlaPolylang.settings.ajax_nonce,
				post_type: 'attachment',
				post_ID: id,
				edit_date: 'true',
				post_status: page
			};

			fields = $( '#edit-' + id + ' :input' ).serialize();
			params = fields + '&' + $.param( params );

			// make ajax request
			$.post( ajaxurl, params,
				function( response ) {
					var newId, oldRow, rowId, rows, rIndex;

					if ( mla.settings.useSpinnerClass ) {
						$( 'table.widefat .pll-quick-translate-save .spinner' ).removeClass("is-active");
					} else {
						$( 'table.widefat .pll-quick-translate-save .spinner' ).hide();
					}

					if ( response ) {
						if ( -1 != response.indexOf( '<tr id="attachment' ) ) {
							// Find all the rows in the response
							rows = $( response ).closest( 'tr' );

							// Remove the selected item
							newId = mlaPolylang.utility.getId( rows[ 0 ] );
							oldRow = $( mlaPolylang.inlineTranslate.what + newId )
							if ( 'undefined' != typeof oldRow ) {
								oldRow.remove();
							}

							// Replace the Quick Translate area with the selected item
							$( '#edit-' + id ).before( rows[ 0 ] ).remove();
							$( mlaPolylang.inlineTranslate.what + newId ).hide().fadeIn();

							// Update any other translations in the table
							if ( 1 < rows.length ) { 
								for ( rIndex = 1; rIndex < rows.length; rIndex++ ) {
									rowId = mlaPolylang.utility.getId( rows[ rIndex ] );
									oldRow = $( mlaPolylang.inlineTranslate.what + rowId )

									if ( 'undefined' != typeof oldRow ) {
										oldRow.before( rows[ rIndex ] ).remove();
										oldRow = $( mlaPolylang.inlineTranslate.what + rowId )
										oldRow.hide().fadeIn();
									}
								}
							} // other translations

							// Quick Edit a new selected item
							if ( newId != id ) {
								$( 'a.editinline', mlaPolylang.inlineTranslate.what + newId ).click();
							}
						} else {
							response = response.replace( /<.[^<>]*?>/g, '' );
							$( '#edit-' + id + ' .pll-quick-translate-save .error' ).html( response ).show();
						}
					} else {
						$( '#edit-' + id + ' .pll-quick-translate-save .error' ).html( mlaPolylang.settings.error ).show();
					}
				}, 'html' );

			return false;
		},

		revert : function(){
			var id = $( 'table.widefat tr.inline-translator ').attr( 'id' );

			if ( id ) {
				if ( mla.settings.useSpinnerClass ) {
					$( 'table.widefat .pll-quick-translate-save .spinner' ).removeClass("is-active");
				} else {
					$( 'table.widefat .pll-quick-translate-save .spinner' ).hide();
				}

				if ( 'pll-bulk-translate' == id ) {
					$( 'table.widefat #pll-bulk-translate ').removeClass( 'inline-translator' ).hide();
					$( '#pll-bulk-titles' ).html( '' );
					$( '#pll-inline-translate' ).append( $('#pll-bulk-translate') );
				} else {
					$( '#'+id ).remove();
					id = id.substr( id.lastIndexOf( '-' ) + 1 );
					$( this.what+id ).show();
				}
			}

			return false;
		}
	}; // mlaPolylang.inlineTranslate

	$( document ).ready( function() {
		mlaPolylang.inlineTranslate.init();
	});
})( jQuery );
