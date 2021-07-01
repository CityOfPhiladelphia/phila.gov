jQuery( function ( $ ) {
	'use strict';

	var Limiter = function( $el ) {
		this.$el = $el;
	}

	Limiter.prototype = {
		// Initialize everything.
		init: function () {
			this.initElements();
			this.addListeners();
			this.$input.trigger( 'input' );
		},

		// Initialize elements.
		initElements: function () {
			this.$input = this.$el.siblings( '.rwmb-text' );
			if ( !this.$input.length ) {
				this.$input = this.$el.siblings( '.rwmb-textarea' );
			}
			this.$counter = this.$el.find( '.counter' );

			this.type = this.$el.data( 'limit-type' );
			this.max = parseInt( this.$el.find( '.maximum' ).text() );
		},

		// Add event listeners for 'input'.
		addListeners: function () {
			var that = this;

			this.$input.on( 'input', function () {
				var value = this.value,
					length = that.count( value, that.type );

				if ( length > that.max ) {
					value = that.subStr( value, 0, that.max, that.type );
					length = that.max;
					this.value = value;
				}

				that.$counter.html( length );
			} );
		},

		// Count for text.
		count: function ( val, type ) {
			if ( $.trim( val ) == '' ) {
				return 0;
			}

			return 'word' === type ? val.match( /\S+/g ).length : val.length;
		},

		// Get subString for text by word or characters.
		subStr: function ( val, start, len, type ) {
			if ( 'word' !== type ) {
				return val.substr( start, len );
			}

			var lastIndexSpace = val.lastIndexOf( ' ' );

			return val.substr( start, lastIndexSpace );
		}
	}

	function update() {
		$( '.text-limiter' ).each( function () {
			var $this = $( this ),
				controller = $this.data( 'limiterController' );
			if ( controller ) {
				return;
			}

			controller = new Limiter( $this );
			controller.init();
			$this.data( 'limiterController', controller );
		} );
	}

	update();
	$( '.rwmb-input' ).on( 'clone', update );
} );
