var jQuery,
	mla_shortcodes_tab_vars,
	mlaShortcodes = {
		// Properties
		// mlaShortcodes.settings.definitions
		// mlaShortcodes.settings.sectionText
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
		addTemplate: null
	};

( function( $ ) {
	// Localized settings and strings
	mlaShortcodes.settings = typeof mla_shortcodes_tab_vars === 'undefined' ? {} : mla_shortcodes_tab_vars;
	mla_shortcodes_tab_vars = void 0; // delete won't work on Globals
	mlaShortcodes.settings.sectionText = [];

	mlaShortcodes.addTemplate = {
		init : function(){
			var t = this, templateForm = $( '#mla-edit-template' );

			$( '#mla-template-type, #mla-template-shortcode', templateForm ).change( function( e ){
				var type = $( '#mla-template-type', templateForm ).val(),
				    shortcode = $( '#mla-template-shortcode', templateForm ).val();

				e.preventDefault();
				t.fillSections( type, shortcode, templateForm );
			});
		},

		fillSections : function( type, shortcode, templateForm ){
			var oldType = $( '#mla-template-item-type', templateForm ).val(),
			    oldShortcode = $( '#mla-template-item-shortcode', templateForm ).val(),
				oldClass = '.mla_section.mla_' + oldType + '.mla_' + oldShortcode;
				newClass = '.mla_section.mla_' + type + '.mla_' + shortcode;

			$( '#mla-template-item-type', templateForm ).val( type ),
			$( '#mla-template-item-shortcode', templateForm ).val( shortcode ),
				
			// Remove old sections, saving their values for reuse
			$( oldClass, templateForm ).each( function( index ) {
				var id = $('textarea', this).attr('id'), value = $('textarea', this).val(),
				    prefix = 'mla-template-' + oldType + '-' + oldShortcode + '-',
					slug = id.substring( prefix.length );

				mlaShortcodes.settings.sectionText[ slug ] = value;
			}); // oldClass.each
			
			$( '.mla_section', templateForm ).hide();

			if ( type == 'any' || shortcode == 'any'  ) {
				return;
			}
			
			// Fill section rows, with any saved values
			$( newClass, templateForm ).each( function( index ) {
				var id = $('textarea', this).attr('id'),
				    prefix = 'mla-template-' + type + '-' + shortcode + '-',
					slug = id.substring( prefix.length );

				if ( typeof mlaShortcodes.settings.sectionText[ slug ] !== 'undefined' ) {
					$('textarea', this).val( mlaShortcodes.settings.sectionText[ slug ] );
				}
			}); // newClass.each
			
			$( newClass, templateForm ).show();
		},
	}; // mlaShortcodes.addTemplate

	$( document ).ready( function() {
		mlaShortcodes.addTemplate.init();
	});
})( jQuery );
