jQuery( function ( $ )
{
	'use strict';

	/**
	 * Show color pickers
	 * @return void
	 */
	function initColorPicker()
	{
		var $this = $( this );

		if ( !$this.closest( '.rwmb-group-clone' ).length )
		{
			return;
		}

		var $container = $this.closest( '.rwmb-input' );

		// Clone doesn't have input for color picker, we have to add the input and remove the color picker container
		$this.appendTo( $container ).siblings( '.wp-picker-container' ).remove();

		// Make sure the value is displayed
		if ( !$this.val() )
		{
			$this.val( '#' );
		}

		// Show color picker
		$this.wpColorPicker();
	}

	$( ':input.rwmb-color' ).each( initColorPicker );
	$( '.rwmb-input' ).on( 'clone', 'input.rwmb-color', initColorPicker );
} );
