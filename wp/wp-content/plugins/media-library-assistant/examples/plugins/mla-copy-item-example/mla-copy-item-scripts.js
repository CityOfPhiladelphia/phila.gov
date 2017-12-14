/* global ajaxurl */

var jQuery,
	mla_copy_item_support_vars,
	mlaCopyItem = {
		// Properties
		// mlaCopyItem.settings.noTitle
		// mlaCopyItem.settings.ntdelTitle
		// mlaCopyItem.settings.fields
		// mlaCopyItem.settings.comma
		// mlaCopyItem.settings.useSpinnerClass
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
		inlineCopyItem: null
	};

( function( $ ) {
	/**
	 * Localized settings and strings
	 */
	mlaCopyItem.settings = typeof mla_copy_item_support_vars === 'undefined' ? {} : mla_copy_item_support_vars;
	mla_copy_item_support_vars = void 0; // delete won't work on Globals

	mlaCopyItem.inlineCopyItem = {
		init : function(){
			var t = this, bgRow = $( '#mla-bulk-copy-item' );

			t.type = 'attachment';
			t.what = '#attachment-';

			// prepare the bulk-generate row
			bgRow.keyup( function( e ){
				if ( e.which == 27 )
					return mlaCopyItem.inlineCopyItem.revert();
			});

			$( 'a.cancel', bgRow ).click( function(){
				return mlaCopyItem.inlineCopyItem.revert();
			});

			$( '#doaction, #doaction2' ).click( function( e ){
				var n = $( this ).attr( 'id' ).substr( 2 );

				if ( $( 'select[name="'+n+'"]' ).val() == 'mla-copy-item-example' ) {
					e.preventDefault();
					t.openCopyItem();
				}
			});

			// Filter button (dates, categories) in top nav bar
			$( '#post-query-submit' ).mousedown( function(){
				t.revert();
				$( 'select[name^="action"]' ).val( '-1' );
			});
		},

		openCopyItem : function(){
			var te = '', c = true;
			this.revert();

			// Open up the Bulk Translate area
			$( '#mla-bulk-copy-item td' ).attr( 'colspan', $( 'th:visible, td:visible', '.widefat:first thead' ).length );
			$( 'table.widefat tbody' ).prepend( $( '#mla-bulk-copy-item' ) );
			$( '#mla-bulk-copy-item' ).addClass( 'inline-translator' ).show();

			// Make sure at least one item has been selected
			$( 'tbody th.check-column input[type="checkbox"]' ).each( function(){
				if ( $( this ).prop( 'checked' ) ) {
					c = false;
					var id = $( this ).val(), theTitle;
					theTitle = $( '#inline_'+id+' .post_title' ).text() || mlaCopyItem.settings.noTitle;
					te += '<div id="ttle'+id+'"><a id="_'+id+'" class="ntdelbutton" title="'+mlaCopyItem.settings.ntdelTitle+'">X</a>'+theTitle+'</div>';
				}
			});

			if ( c ) {
				return this.revert();
			}

			// Populate the list of selected items
			$( '#mla-copy-item-titles' ).html( te );
			$( '#mla-copy-item-titles a' ).click(function(){
				var id = $( this ).attr( 'id' ).substr( 1 );

				$( 'table.widefat input[value="' + id + '"]' ).prop( 'checked', false );
				$( '#ttle'+id ).remove();
			});

			$( 'html, body' ).animate( { scrollTop: 0 }, 'fast' );
		},

		revert : function(){
			var id = $( 'table.widefat tr.inline-translator ').attr( 'id' );

			if ( id ) {
				if ( mlaCopyItem.settings.useSpinnerClass ) {
					$( 'table.widefat .pll-quick-translate-save .spinner' ).removeClass("is-active");
				} else {
					$( 'table.widefat .pll-quick-translate-save .spinner' ).hide();
				}

				if ( 'mla-bulk-copy-item' == id ) {
					$( 'table.widefat #mla-bulk-copy-item ').removeClass( 'inline-translator' ).hide();
					$( '#mla-copy-item-titles' ).html( '' );
					$( '#pll-inline-translate' ).append( $('#mla-bulk-copy-item') );
				} else {
					$( '#'+id ).remove();
					id = id.substr( id.lastIndexOf( '-' ) + 1 );
					$( this.what+id ).show();
				}
			}

			return false;
		}
	}; // mlaCopyItem.inlineCopyItem

	$( document ).ready( function() {
		mlaCopyItem.inlineCopyItem.init();
	});
})( jQuery );
