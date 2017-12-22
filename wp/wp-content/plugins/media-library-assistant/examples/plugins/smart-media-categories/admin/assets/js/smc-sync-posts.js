( function( $ ){
	/**
	 * syncPosts displays a popup modal window with taxonomy term assignments of
	 * the current parent post/page. The user can synchronize the terms to the 
	 * children by setting radio buttons below the desired taxonomies. The pop-up also
	 * has dropdown lists for assigning children to a new parent.
	 *
	 * This script depends on the "smc-find-posts.js" script to set up the "smc." global
	 * object and to provide the "Select Parent" popup functionality in "syncPosts.reattach".
	 */
	smc.syncPosts = {
		init : function(){
			var t = this, smcDiv = $( '#smc-sync-div' );
	
			t.type = 'attachment';
			t.what = '#attachment-';
	
			// Close the smc-sync-box pop-up
			smcDiv.keyup( function( event ){
				if ( event.which == 27 )
					return smc.syncPosts.close();
			});
	
			$( '#smc-sync-close' ).click( smc.syncPosts.close );
			
			$( '#smc-sync-cancel' ).click( function ( event ) {
				event.preventDefault();
				return smc.syncPosts.close();
			});

			$( '#smc-sync-update' ).click( function ( event ) {
				return smc.syncPosts.save();
			});

			$( '#smc-sync-reattach' ).click( function ( event ) {
				return smc.syncPosts.reattach( event );
			});

			// hiearchical taxonomies expandable?
			$('span.catshow').click(function(){
				$(this).hide().next().show().parent().next().addClass("cat-hover");
			});
	
			$('span.cathide').click(function(){
				$(this).hide().prev().show().parent().next().removeClass("cat-hover");
			});
	
			$('select[name="_status"] option[value="future"]', smcDiv).remove();
	
		},

		// called from "onclick=" attributes in the "Smart Media" rollover actions
		open: function( affectedParent ) {
			var t = this, overlay = $( '#smc-sync-overlay' ), theCell = $( '#the-list tr.post-' + affectedParent + ' td.smc_children' ),
				syncTitles = '';

			// Block out the underlying All Posts submenu
			if ( overlay.length === 0 ) {
				$( 'body' ).append( '<div id="smc-sync-overlay"></div>' );
				smc.syncPosts.overlay();
			}
			overlay.show();

			// Save the parent ID
			if ( affectedParent ) {
				$( '#smc-posts-modal-parent' ).val( affectedParent );
			}

			// Fill the left column - children list
			$( 'a', theCell ).each( function( index, element ) {
				titleText = ( element.textContent || element.innerText || "" )

				syncClass = $( element ).hasClass( 'smc-sync-true' ) ? 'smc-sync-true' : 'smc-sync-false';
				syncTitles += '<div id="' + element.id + '-div"><input name="children[]" id="smc-sync-children-' + element.id + '" type="hidden" value="' + element.id.replace('smc-child-', '') + '"><a id="' + element.id + '" class="ntdelbutton" title="' + smc.settings.ntDelTitle + '">X</a><span class="' + syncClass + '">' + titleText + '</span></div>';
			});
			
			if ( syncTitles.length ) {
				$( '#smc-sync-children-div' ).html( syncTitles );
				$( '#smc-sync-children-div a' ).click( function(){
					$( '#' + $( this ).attr( 'id' ) + '-div' ).remove();
				});
			}

			// Fill the middle column - hiearachical taxonomies
			$('#smc-sync-category-blocks ul.cat-checklist').each(function(){
				var thisJq = $(this), taxname = thisJq.attr('id').replace('smc-tax-checklist-', ''), prefix = '#in-' + taxname + '-',
					terms = $( '#' + taxname + '-' + affectedParent, theCell ).html().split( ',' );
					
					$( 'input:checked', thisJq ).removeAttr( 'checked' );
					for ( i = 0; i < terms.length; i++ ) {
						$( prefix + terms[ i ], thisJq ).attr( 'checked', 'checked' );
					}
			});
			
			// Fill the right column - flat taxonomies
			$('textarea.smc-tags').each(function(){
				var thisJq = $(this), taxname = thisJq.attr('id').replace('smc-tax-input-', '');

				thisJq.html( $( '#' + taxname + '-' + affectedParent, theCell ).html() );
	
				//link flat taxonomies to auto-suggest function
				thisJq.suggest( ajaxurl + '?action=ajax-tag-search&tax=' + taxname, { delay: 500, minchars: 2, multiple: true, multipleSep: smc.settings.comma + ' ' } );
			});

			// Fill the right column - parent information
			$('#smc-current-parent').each(function(){
				var thisJq = $(this);

				thisJq.html( $( '#post-title-' + affectedParent, theCell ).html() );
			});

			if ( smc.settings.useDashicons ) {
				$( '#smc-sync-close' ).addClass("smc-sync-close-dashicons");
				$( '#smc-sync-children-div div a' ).addClass("smc-sync-children-a-dashicons");
			} else {
				$( '#smc-sync-close' ).html( 'x' );
				$( '#smc-sync-children-div div a' ).addClass("smc-sync-children-a-gif");
			}
			
			$( '#smc-sync-div' ).show();
			$( '#smc-sync-cancel' ).focus();
		},

		close: function() {
			$( '#smc-posts-modal-parent' ).val( '' );
			$( '#smc-posts-modal-children' ).val( '0' );
			$( '#smc-sync-children-div' ).html('');
			$( '#smc-sync-category-blocks ul.cat-checklist' ).each(function(){
				$( 'input:checked', $(this) ).removeAttr( 'checked' );
			});
			$( 'textarea.smc-tags' ).each(function(){
				$(this).html( '' );
			});
			$( '#smc-sync-div' ).hide();
			$( '#smc-sync-overlay' ).hide();
			return false;
		},

		overlay: function() {
			// Click outside the modal window  closes it
			$( '#smc-sync-overlay' ).on( 'click', function ( event ) {
				smc.syncPosts.close();
				event.preventDefault();
				return false;
			});
		},

		reattach: function( event ) {
			var titles = [];
			
			$( '#smc-sync-children-div span' ).each(function(){
				titles[ titles.length ] = ( this.textContent || this.innerText || "" )
			});
			
			if ( titles.length ) {
				titles = titles.join( ', ' );
			} else {
				titles = smc.settings.noChildren;
			}
	
			$( '#smc-sync-category-blocks ul.cat-checklist' ).each(function(){
				$( 'input:checked', $(this) ).removeAttr( 'checked' );
			});

			$( 'textarea.smc-tags' ).each(function(){
				$(this).html( '' );
			});

			$( '#smc-sync-div' ).hide();
			$( '#smc-sync-overlay' ).hide();
			smc.findPosts.open( 0, 0, titles );
			
			event.preventDefault();
			return false;
		},

		save : function(id) {
			var categoryJq = $( '#smc-tax-checklist-category' );
			
			/*
			 * Convert category/post_category checkboxes
			 */
			if ( categoryJq.length ) {
				$( 'input:checked', categoryJq ).each(function(){
					$(this).attr( 'name', 'tax_input[category][]' );
				});
			}

			return true;
		}
	}; // smc.syncPosts
	
	/**
	 * bulkActions adds "Sync All Children" buttons to the Posts/All Posts Bulk Edit area
	 * and on the top Navigation bar above the submenu table.
	 */
	smc.bulkActions = {
		init : function(){
			var t = this, filterDiv = $( '#post-query-submit' ).parent(), bulkButton = $( '#bulk_edit' ),
				syncAllFilter = $( '<input name="sync_all_filter" class="button" id="smc-sync-all-filter" type="submit" value="' + smc.settings.syncAllChildren + '"></input>' ),
				syncAllBulk = $( '<input name="sync_all_bulk" class="button button-secondary alignright" id="smc-sync-all-bulk" type="submit" value="' + smc.settings.syncAllChildren + '"></input>' );
				
			filterDiv.append( syncAllFilter );
			bulkButton.after( syncAllBulk );
			return;
		}
	}; // smc.bulkActions
	
	$( document ).ready( function() {
		// Initialize the modules
		smc.syncPosts.init();
		smc.bulkActions.init();
	});
})( jQuery );
