/* global ajaxurl, uploader */

var jQuery,
	mla_add_new_bulk_edit_vars,
	mla = {
		// Properties (for mla-set-parent-scripts, too)
		// mla.settings.uploadTitle
		// mla.settings.toggleClose
		// mla.settings.toggleOpen
		// mla.settings.areaOnTop
		// mla.settings.comma for flat taxonomy suggest
		// mla.settings.ajaxFailError for setParent
		// mla.settings.ajaxDoneError for setParent
		// mla.settings.useDashicons for setParent
		// mla.settings.useSpinnerClass for setParent
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
		addNewBulkEdit: null,
		setParent: null
	};

( function( $ ) {
	/**
	 * Localized settings and strings
	 */
	mla.settings = typeof mla_add_new_bulk_edit_vars === 'undefined' ? {} : mla_add_new_bulk_edit_vars;
	mla_add_new_bulk_edit_vars = void 0; // delete won't work on Globals

	if ( typeof mla.settings.areaOnTop === 'undefined' ) {
		mla.settings.areaOnTop = false;
	};

	mla.addNewBulkEdit = {
		init: function() {
			var toggleButton, resetButton, 
				bypass = $( '.upload-flash-bypass' ), title = $( '#wpbody .wrap' ).children ( 'h2' ),
				uploadContent, uploadDiv = $( '#mla-add-new-bulk-edit-div' ).hide(); // Start with area closed up

			$( '#bulk-edit-set-parent', uploadDiv ).on( 'click', function(){
				return mla.addNewBulkEdit.parentOpen();
			});

			// Move the Open/Close Bulk Edit area toggleButton to save space on the page
			toggleButton = $( '#bulk-edit-toggle', uploadDiv ).detach();
			resetButton = $( '#bulk-edit-reset', uploadDiv ).detach();

			if ( mla.settings.areaOnTop ) {
				toggleButton.appendTo( title );
				resetButton.appendTo( title );
				uploadContent = uploadDiv.detach();
				$( '#file-form' ).before( uploadContent );
			} else {
				toggleButton.appendTo( bypass );
				resetButton.appendTo( bypass );
			};

			// Hook the "browser uploader" link to close the Bulk Edit area when it is in use
			toggleButton.siblings( 'a' ).on( 'click', function(){
				toggleButton.attr( 'title', mla.settings.toggleOpen );
				toggleButton.attr( 'value', mla.settings.toggleOpen );
				resetButton.hide();
				uploadDiv.hide();
			});

			toggleButton.on( 'click', function(){
				return mla.addNewBulkEdit.formToggle();
			});

			resetButton.on( 'click', function(){
				return mla.addNewBulkEdit.doReset();
			});

			//auto-complete/suggested matches for flat taxonomies
			$( 'textarea.mla_tags', uploadDiv ).each(function(){
				var taxname = $(this).attr('name').replace(']', '').replace('tax_input[', '');

				$(this).suggest( ajaxurl + '?action=ajax-tag-search&tax=' + taxname, { delay: 500, minchars: 2, multiple: true, multipleSep: mla.settings.comma + ' ' } );
			});

			uploader.bind( 'BeforeUpload', function( up, file ) {
				var formString = $( '#file-form' ).serialize();

				up.settings.multipart_params.mlaAddNewBulkEditFormString = formString;
			});
		},

		doReset : function(){
			var bulkRow = $('#mla-add-new-bulk-edit-div'),
				blankRow = $('#mla-blank-add-new-bulk-edit-div'),
				blankCategories = $('.inline-edit-categories', blankRow ).html(),
				blankTags = $('.inline-edit-tags', blankRow ).html(),
				blankFields = $('.inline-edit-fields', blankRow ).html();

			$('.inline-edit-categories', bulkRow ).html( blankCategories ),
			$('.inline-edit-tags', bulkRow ).html( blankTags ),
			$('.inline-edit-fields', bulkRow ).html( blankFields );

			$('#bulk-edit-set-parent', bulkRow).on( 'click', function(){
				return mla.addNewBulkEdit.parentOpen();
			});

			return false;
		},

		formToggle : function() {
			var toggleButton = $( '#bulk-edit-toggle' ), resetButton = $( '#bulk-edit-reset' ), 
				area = $( '#mla-add-new-bulk-edit-div' );

			// Expand/collapse the Bulk Edit area
			if ( 'none' === area.css( 'display' ) ) {
				toggleButton.attr( 'title', mla.settings.toggleClose );
				toggleButton.attr( 'value', mla.settings.toggleClose );
				resetButton.show();
			} else {
				toggleButton.attr( 'title', mla.settings.toggleOpen );
				toggleButton.attr( 'value', mla.settings.toggleOpen );
				resetButton.hide();
			}

			area.slideToggle( 'slow' );
		},

		parentOpen : function() {
			var parentId, postId, postTitle;

			postId = -1;
			postTitle = mla.settings.uploadTitle;
			parentId = $( '#mla-add-new-bulk-edit-div :input[name="post_parent"]' ).val() || -1;
			mla.setParent.open( parentId, postId, postTitle );
			/*
			 * Grab the "Update" button
			 */
			$( '#mla-set-parent-submit' ).on( 'click', function( event ){
				event.preventDefault();
				mla.addNewBulkEdit.parentSave();
				return false;
			});
		},

		parentSave : function() {
			var foundRow = $( '#mla-set-parent-response-div input:checked' ).closest( 'tr' ), parentId, newParent;

			if ( foundRow.length ) {
				parentId = $( ':radio', foundRow ).val() || '';
				newParent = $('#mla-add-new-bulk-edit-div :input[name="post_parent"]').clone( true ).val( parentId );
				$('#mla-add-new-bulk-edit-div :input[name="post_parent"]').replaceWith( newParent );
			}

			mla.setParent.close();
			$('#mla-set-parent-submit' ).off( 'click' );
		},

	}; // mla.addNewBulkEdit

	$( document ).ready( function() {
		mla.addNewBulkEdit.init();
	});
})( jQuery );
