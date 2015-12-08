/* global ajaxurl */

var jQuery,
	mla_thumbnail_support_vars,
	mlaThumbnail = {
		// Properties
		// mlaThumbnail.settings.noTitle
		// mlaThumbnail.settings.ntdelTitle
		// mlaThumbnail.settings.fields
		// mlaThumbnail.settings.comma
		// mlaThumbnail.settings.ajax_action
		// mlaThumbnail.settings.ajax_nonce
		// mlaThumbnail.settings.error
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
		inlineThumbnail: null
	};

( function( $ ) {
	/**
	 * Localized settings and strings
	 */
	mlaThumbnail.settings = typeof mla_thumbnail_support_vars === 'undefined' ? {} : mla_thumbnail_support_vars;
	mla_thumbnail_support_vars = void 0; // delete won't work on Globals

	mlaThumbnail.inlineThumbnail = {
		init : function(){
			var t = this, bgRow = $( '#mla-bulk-thumbnail' );

			t.type = 'attachment';
			t.what = '#attachment-';

			// prepare the bulk-generate row
			bgRow.keyup( function( e ){
				if ( e.which == 27 )
					return mlaThumbnail.inlineThumbnail.revert();
			});

			$( 'a.cancel', bgRow ).click( function(){
				return mlaThumbnail.inlineThumbnail.revert();
			});

			$( '#doaction, #doaction2' ).click( function( e ){
				var n = $( this ).attr( 'id' ).substr( 2 );

				if ( $( 'select[name="'+n+'"]' ).val() == 'mla-generate-featured-image' ) {
					e.preventDefault();
					t.openBulkGenerate();
				}
			});

			// Filter button (dates, categories) in top nav bar
			$( '#post-query-submit' ).mousedown( function(){
				t.revert();
				$( 'select[name^="action"]' ).val( '-1' );
			});
		},

		openBulkGenerate : function(){
			var te = '', c = true;
			this.revert();

			// Open up the Bulk Translate area
			$( '#mla-bulk-thumbnail td' ).attr( 'colspan', $( 'th:visible, td:visible', '.widefat:first thead' ).length );
			$( 'table.widefat tbody' ).prepend( $( '#mla-bulk-thumbnail' ) );
			$( '#mla-bulk-thumbnail' ).addClass( 'inline-translator' ).show();

			// Make sure at least one item has been selected
			$( 'tbody th.check-column input[type="checkbox"]' ).each( function(){
				if ( $( this ).prop( 'checked' ) ) {
					c = false;
					var id = $( this ).val(), theTitle;
					theTitle = $( '#inline_'+id+' .post_title' ).text() || mlaThumbnail.settings.noTitle;
					te += '<div id="ttle'+id+'"><a id="_'+id+'" class="ntdelbutton" title="'+mlaThumbnail.settings.ntdelTitle+'">X</a>'+theTitle+'</div>';
				}
			});

			if ( c ) {
				return this.revert();
			}

			// Populate the list of selected items
			$( '#mla-thumbnail-titles' ).html( te );
			$( '#mla-thumbnail-titles a' ).click(function(){
				var id = $( this ).attr( 'id' ).substr( 1 );

				$( 'table.widefat input[value="' + id + '"]' ).prop( 'checked', false );
				$( '#ttle'+id ).remove();
			});

			$( 'html, body' ).animate( { scrollTop: 0 }, 'fast' );
		},

		revert : function(){
			var id = $( 'table.widefat tr.inline-translator ').attr( 'id' );

			if ( id ) {
				if ( mlaThumbnail.settings.useSpinnerClass ) {
					$( 'table.widefat .pll-quick-translate-save .spinner' ).removeClass("is-active");
				} else {
					$( 'table.widefat .pll-quick-translate-save .spinner' ).hide();
				}

				if ( 'mla-bulk-thumbnail' == id ) {
					$( 'table.widefat #mla-bulk-thumbnail ').removeClass( 'inline-translator' ).hide();
					$( '#mla-thumbnail-titles' ).html( '' );
					$( '#pll-inline-translate' ).append( $('#mla-bulk-thumbnail') );
				} else {
					$( '#'+id ).remove();
					id = id.substr( id.lastIndexOf( '-' ) + 1 );
					$( this.what+id ).show();
				}
			}

			return false;
		}
	}; // mlaThumbnail.inlineThumbnail

	$( document ).ready( function() {
		mlaThumbnail.inlineThumbnail.init();
	});
})( jQuery );
