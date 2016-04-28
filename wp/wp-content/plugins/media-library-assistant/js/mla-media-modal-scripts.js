// uses Global ajaxurl

var wp, wpAjax, ajaxurl, jQuery, _,
	getUserSetting, setUserSetting, deleteUserSetting,
	mlaTaxonomy,
	mlaModal = {
		// Properties
		strings: {},
		settings: {},
		initialHTML: {},
		uploading: false,
		cid: null,

		// Utility functions
		utility: {
			originalMediaAjax: null,
			mlaAttachmentsBrowser: null,
			parseTermsOptions: null,
			arrayCleanup: null,
			parseTaxonomyId: null,
			hookCompatTaxonomies: null,
			fillCompatTaxonomies: null,
			supportCompatTaxonomies: null
		},

		// Components
		tagBox: null
	};

(function($){
	var mlaDrop = wp.media.view.AttachmentsBrowser;
	
/*	for debug : trace every event triggered in the MediaFrame controller * /
	var originalMediaFrameTrigger = wp.media.view.MediaFrame.prototype.trigger;
	wp.media.view.MediaFrame.prototype.trigger = function(){
		console.log('MediaFrame Event: ', arguments[0]);
		originalMediaFrameTrigger.apply(this, Array.prototype.slice.call(arguments));
	} // */

/*	for debug : trace every event triggered in the view.Attachment controller * /
	var originalAttachmentTrigger = wp.media.view.Attachment.prototype.trigger;
	wp.media.view.Attachment.prototype.trigger = function(){
		console.log('view.Attachment Event: ', arguments[0]);
		originalAttachmentTrigger.apply(this, Array.prototype.slice.call(arguments));
	} // */

/*	for debug : trace every event triggered in the model.Attachment controller * /
	var originalModelAttachmentTrigger = wp.media.model.Attachment.prototype.trigger;
	wp.media.model.Attachment.prototype.trigger = function(){
		console.log('model.Attachment Event: ', arguments[0]);
		originalModelAttachmentTrigger.apply(this, Array.prototype.slice.call(arguments));
	} // */

/*	for debug : trace every event triggered in the view.AttachmentCompat controller * /
	var originalAttachmentCompatTrigger = wp.media.view.AttachmentCompat.prototype.trigger;
	wp.media.view.AttachmentCompat.prototype.trigger = function(){
		console.log('view.AttachmentCompat Event: ', arguments[0]);

		originalAttachmentCompatTrigger.apply(this, Array.prototype.slice.call(arguments));
	} // */

/*	for debug : trace every event triggered in the model.Selection controller * /
	var originalModelSelectionTrigger = wp.media.model.Selection.prototype.trigger;
	wp.media.model.Selection.prototype.trigger = function(){
		console.log('model.Selection Event: ', arguments[0]);

		originalModelSelectionTrigger.apply(this, Array.prototype.slice.call(arguments));
	} // */

/*	for debug : trace every invocation of the media.post method * /
	var originalMediaPost = media.post;
	media.post = function( action, data ) {
		//console.log('media.post action: ', action );
		//console.log('media.post data: ', JSON.stringify( data ) );

		return originalMediaPost.apply(this, Array.prototype.slice.call(arguments));
	}; // */

/*	for debug : trace every invocation of the wp.ajax.send function * /
	var originalWpAjaxSend = wp.ajax.send;
	wp.ajax.send = function( action, data ) {
		//console.log('wp.ajax.send action: ', JSON.stringify( action ) );
		//console.log('wp.ajax.send data: ', JSON.stringify( data ) );

		return originalWpAjaxSend.apply(this, Array.prototype.slice.call(arguments));
	}; // */

	/**
	 * Localized settings and strings
	 */
	mlaModal.strings = typeof wp.media.view.l10n.mla_strings === 'undefined' ? {} : wp.media.view.l10n.mla_strings;
	delete wp.media.view.l10n.mla_strings;

	mlaModal.settings = typeof wp.media.view.settings.mla_settings === 'undefined' ? { screen: 'unknown', enableMediaGrid: false,  enableMediaModal: false } : wp.media.view.settings.mla_settings;
	delete wp.media.view.settings.mla_settings;

	/*
	 * Do not proceed unless there's positive indication that the MLA enhancements
	 * have been enabled!
	 */
	if ( ! ( mlaModal.settings.enableMediaGrid || mlaModal.settings.enableMediaModal ) ) {
		return;
	}

	if ( ( 'grid' === mlaModal.settings.screen ) && false === mlaModal.settings.enableMediaGrid ) {
		return;
	}

	if ( ( 'modal' === mlaModal.settings.screen ) && false === mlaModal.settings.enableMediaModal ) {
		return;
	}

	/*
	 * Parse outgoing Ajax requests, look for the 'query-attachments' action and stuff
	 * our arguments into the "s" field because MMMW only monitors that one field.
	 */
	mlaModal.utility.originalMediaAjax = wp.media.ajax;
	wp.media.ajax = function( action, options ) {
		var state = mlaModal.settings.state, query, stype, s, searchValues;

		if ( _.isObject( action ) ) {
			options = action;
		} else {
			options = options || {};
			options.data = _.extend( options.data || {}, { action: action });
		}

		if ( 'query-attachments' == options.data.action ) {
			query = options.data.query;
			stype = typeof query.s;
			if ( 'object' == stype )
				s = query.s;
			else if ( 'string' == stype )
					s = { 'mla_search_value': query.s };
				else
					s = { 'mla_search_value': '' };

			if ( 'undefined' != typeof query.post_mime_type )
				mlaModal.settings.query[state].filterMime = query.post_mime_type;
			else
				mlaModal.settings.query[state].filterMime = 'all';

			if ( 'undefined' != typeof s.mla_filter_month ){
				mlaModal.settings.query[state].filterMonth = s.mla_filter_month;
			} else {
				if ( 'undefined' != typeof query.year ) {
					// Media Grid native dateFilter
					mlaModal.settings.query[state].filterMonth = ( 100 * query.year ) + ( 1 * query.monthnum ) ;
				} else {
					// mlaModal.settings.query[state].filterMonth = 0;
				}
			}

			if ( 'undefined' != typeof s.mla_filter_term )
				mlaModal.settings.query[state].filterTerm = s.mla_filter_term;

			if ( 'undefined' != typeof s.mla_search_value )
				mlaModal.settings.query[state].searchValue = s.mla_search_value;

			searchValues = {
				'mla_filter_month': mlaModal.settings.query[state].filterMonth,
				'mla_filter_term': mlaModal.settings.query[state].filterTerm,
				'mla_terms_search': mlaModal.settings.query[state].termsSearch,
				'mla_search_clicks': mlaModal.settings.query[state].searchClicks,
				'mla_search_value': mlaModal.settings.query[state].searchValue,
				'mla_search_fields': mlaModal.settings.query[state].searchFields,
				'mla_search_connector': mlaModal.settings.query[state].searchConnector };

			//Terms Search is not sticky
			mlaModal.settings.query[state].termsSearch = '';
			$( '#mla-terms-search-input' ).html( '' ).val( '' );

			options.data.query.s = searchValues;
		}

		return mlaModal.utility.originalMediaAjax.call(this, options );
	}; // wp.media.ajax

	/**
	 * Extended Filters dropdown with more Mime Types
	 */
	if ( mlaModal.settings.enableMimeTypes ) {
		wp.media.view.AttachmentFilters.Mla = wp.media.view.AttachmentFilters.extend({
			createFilters: function() {
				var state = this.controller._state,
					filters = {};

				_.each( mlaModal.settings.allMimeTypes || {}, function( text, key ) {
					if ( ( 'grid' === mlaModal.settings.screen ) || ( 'trash' !== key ) ) {
						filters[ key ] = {
							text: text,
							props: {
								type:    key,
								uploadedTo: null,
								orderby: 'date',
								order:   'DESC'
							}
						};
					}
				});

				filters.all = {
					text:  wp.media.view.l10n.allMediaItems,
					props: {
						type:    null,
						uploadedTo: null,
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				};

				if ( wp.media.view.settings.post.id ) {
					filters.uploaded = {
						text:  wp.media.view.l10n.uploadedToThisPost,
						props: {
							type:    null,
							uploadedTo: wp.media.view.settings.post.id,
							orderby: 'menuOrder',
							order:   'ASC'
						},
						priority: 20
					};
				}

				this.filters = filters;
				if ( 'undefined' === typeof filters[ mlaModal.settings.query[state].filterMime ] ) {
					mlaModal.settings.query[state].filterMime = 'all';
				}

				if ( mlaModal.settings.query[state].filterMime != 'all' ) {
					this.model.set( filters[ mlaModal.settings.query[state].filterMime ].props, { silent: false } );
				}
			},

			select: function() {
				var state = this.controller._state, 
					model = this.model,
					value = mlaModal.settings.query[state].filterMime,
					props = model.toJSON();

				if ( false === mlaModal.settings.enableSearchBox ) {
					if ( 'string' == typeof props.search ) {
						mlaModal.settings.query[state].searchValue = props.search;
					} else {
						mlaModal.settings.query[state].searchValue = '';
					}
				}

				_.find( this.filters, function( filter, id ) {
					var equal = _.all( filter.props, function( prop, key ) {
						return prop === ( _.isUndefined( props[ key ] ) ? null : props[ key ] );
					});

					if ( equal ) {
						return value = id;
					}
				});

				this.$el.val( value );
			},

			change: function() {
				var toolbar = $( this.el ).closest( 'div.media-toolbar' ), 
					filter = this.filters[ this.el.value ];

				if ( filter ) {
					// silent because we must change the "s" prop before triggering an update
					this.model.set( filter.props, { silent: true } );
					$( '#mla-search-submit', toolbar ).click();
				}
			}
		});

		wp.media.view.AttachmentFilters.MlaUploaded = wp.media.view.AttachmentFilters.extend({
			createFilters: function() {
				var type = this.model.get('type'),
					types = wp.media.view.settings.mimeTypes,
					text,
					state = this.controller._state,
					filters = {};

				if ( types && type ) {
					text = types[ type ];
				}

				_.each( mlaModal.settings.uploadMimeTypes || {}, function( text, key ) {
					if ( ( 'grid' === mlaModal.settings.screen ) || ( 'trash' !== key ) ) {
						filters[ key ] = {
							text: text,
							props: {
								type:    key,
								uploadedTo: null,
								orderby: 'date',
								order:   'DESC'
							}
						};
					}
				});

				filters.all = {
					text:  text || wp.media.view.l10n.allMediaItems,
					props: {
						uploadedTo: null,
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				};

				filters.uploaded = {
					text:  wp.media.view.l10n.uploadedToThisPost,
					props: {
						type:    null,
						uploadedTo: wp.media.view.settings.post.id,
						orderby: 'menuOrder',
						order:   'ASC'
					},
					priority: 20
				};

				filters.unattached = {
					text:  wp.media.view.l10n.unattached,
					props: {
						uploadedTo: 0,
						orderby: 'menuOrder',
						order:   'ASC'
					},
					priority: 50
				};

				this.filters = filters;
				if ( 'undefined' === typeof filters[ mlaModal.settings.query[state].filterUploaded ] ) {
					mlaModal.settings.query[state].filterUploaded = 'all';
				}

				if ( mlaModal.settings.query[state].filterUploaded != 'all' ) {
					this.model.set( filters[ mlaModal.settings.query[state].filterUploaded ].props, { silent: false } );
				}
			},

			select: function() {
				var state = this.controller._state, 
					model = this.model,
					value = mlaModal.settings.query[state].filterMime,
					props = model.toJSON();

				if ( false === mlaModal.settings.enableSearchBox ) {
					if ( 'string' == typeof props.search ) {
						mlaModal.settings.query[state].searchValue = props.search;
					} else {
						mlaModal.settings.query[state].searchValue = '';
					}
				}

				_.find( this.filters, function( filter, id ) {
					var equal = _.all( filter.props, function( prop, key ) {
						return prop === ( _.isUndefined( props[ key ] ) ? null : props[ key ] );
					});

					if ( equal ) {
						return value = id;
					}
				});

				this.$el.val( value );
			},

			change: function() {
				var toolbar = $( this.el ).closest( 'div.media-toolbar' ), 
					filter = this.filters[ this.el.value ];

				if ( filter ) {
					// silent because we must change the "s" prop before triggering an update
					this.model.set( filter.props, { silent: true } );
					$( '#mla-search-submit', toolbar ).click();
				}
			}
		});
	}

	/**
	 * Extended Filters dropdown with month and year selection values
	 */
	if ( mlaModal.settings.enableMonthsDropdown ) {
		wp.media.view.AttachmentFilters.MlaMonths = wp.media.view.AttachmentFilters.extend({
			className: 'attachment-filters',
			id:        'media-attachment-date-filters',

			createFilters: function() {
				var state = this.controller._state, 
					filters = {};

				_.each( mlaModal.settings.months || {}, function( text, key ) {
					filters[ key ] = {
						text: text,
						props: { s: { 'mla_filter_month': key }	}
					};
				});

				this.filters = filters;
				if ( 'undefined' === typeof filters[ mlaModal.settings.query[state].filterMonth ] ) {
					mlaModal.settings.query[state].filterMonth = 0;
				}

				if ( mlaModal.settings.query[state].filterMonth > 0 )
					this.model.set( filters[ mlaModal.settings.query[state].filterMonth ].props, { silent: false } );
			},

			select: function() {
				var state = this.controller._state, 
					model = this.model,
					value = mlaModal.settings.query[state].filterMonth,
					props = model.toJSON();

				if ( _.isUndefined( props.s ) )
					props.s = {};

				if ( false === mlaModal.settings.enableSearchBox ) {
					if ( 'string' == typeof props.search ) {
						mlaModal.settings.query[state].searchValue = props.search;
					} else {
						mlaModal.settings.query[state].searchValue = '';
					}
				}

				if (_.isUndefined( props.s.mla_filter_month ) )
					props.s.mla_filter_month = mlaModal.settings.query[state].filterMonth;
				else
					mlaModal.settings.query[state].filterMonth =  props.s.mla_filter_month;

				_.find( this.filters, function( filter, id ) {
						var equal = _.all( filter.props, function( prop ) {
							return prop.mla_filter_month == mlaModal.settings.query[state].filterMonth;
						});

					if ( equal )
						return value = id;
				});

				this.$el.val( value );
			},

			change: function() {
				var filter = this.filters[ this.el.value ], newProps;
				if ( filter ) {
					newProps = { s: { 'mla_filter_month': filter.props.s.mla_filter_month }	};
					this.model.set( newProps );
				}
			}
		});
	}

	/**
	 * Extended Filters dropdown with taxonomy term selection values
	 */
	if ( mlaModal.settings.enableTermsDropdown ) {
		wp.media.view.AttachmentFilters.MlaTerms = wp.media.view.AttachmentFilters.extend({
			className: 'attachment-filters',
			id:        'media-attachment-term-filters',

			createFilters: function() {
				var state = this.controller._state, index,
					filters = {};

				_.each( mlaModal.settings.termsText || {}, function( text, key ) {
					filters[ key ] = {
						text: text,
						props: { s: { 'mla_filter_term': parseInt( mlaModal.settings.termsValue[ key ] ) } }
					};
				});

				this.filters = filters;
				// need to translate from termsValue to the index value
				index = _.indexOf( mlaModal.settings.termsValue, mlaModal.settings.query[state].filterTerm );
				if ( index > 0 )
					this.model.set( filters[ index ].props, { silent: false } );
			},

			select: function() {
				var state = this.controller._state, 
					model = this.model,
					value = mlaModal.settings.query[state].filterTerm,
					props = model.toJSON();

				if ( _.isUndefined( props.s ) )
					props.s = {};

				if ( false === mlaModal.settings.enableSearchBox ) {
					if ( 'string' == typeof props.search ) {
						mlaModal.settings.query[state].searchValue = props.search;
					} else {
						mlaModal.settings.query[state].searchValue = '';
					}
				}

				if (_.isUndefined( props.s.mla_filter_term ) )
					props.s.mla_filter_term = mlaModal.settings.query[state].filterTerm;
				else
					mlaModal.settings.query[state].filterTerm =  props.s.mla_filter_term;

				_.find( this.filters, function( filter, id ) {
					var equal = _.all( filter.props, function( prop ) {
						return prop.mla_filter_term == mlaModal.settings.query[state].filterTerm;
					});

					if ( equal )
						return value = id;
				});

				this.$el.val( value );
			},

			change: function() {
				var filter = this.filters[ this.el.value ], newProps;

				if ( filter ) {
					newProps = { s: { 'mla_filter_term': filter.props.s.mla_filter_term }	};
					this.model.set( newProps );
				}
			}
		});
	}

	/**
	 * Extended Terms Search activation button
	 */
	if ( mlaModal.settings.enableTermsSearch ) {
		wp.media.view.MlaTermsSearch = wp.media.View.extend({
			tagName:   'span',
			className: 'mla-terms-search',
			template: wp.media.template('mla-terms-search-button'),

			attributes: {
				type: 'mla-terms-search-button'
			},

			events: {
				'change': 'termsSearchOpen',
				'click': 'termsSearchOpen',
			},

			render: function() {
				this.$el.html( this.template( mlaModal.strings ) );
				return this;
			},

			termsSearchOpen: function( event ) {
				var toolbar = $( this.el ).closest( 'div.media-toolbar' );

				if ( ( 'click' == event.type ) && ( 'mla_terms_search' === event.target.name ) ) {
					mlaTaxonomy.termsSearch.open();

					$( '#mla-terms-search-form' ).off( 'submit' ); // only fire once per open
					$( '#mla-terms-search-form' ).submit( function( e ){
						var inputs, inputIndex, termsSearch = { phrases: '', taxonomies: [] };

						e.preventDefault();

						inputs = $( '#mla-terms-search-form' ).serializeArray();
						for( inputIndex = 0; inputIndex < inputs.length; inputIndex++ ) {
							switch ( inputs[ inputIndex ].name ) {
								case 'mla_terms_search[phrases]':
									termsSearch.phrases = inputs[ inputIndex ].value;
									break;
								case 'mla_terms_search[radio_phrases]':
									termsSearch.radio_phrases = inputs[ inputIndex ].value;
									break;
								case 'mla_terms_search[radio_terms]':
									termsSearch.radio_terms = inputs[ inputIndex ].value;
									break;
								case 'mla_terms_search[taxonomies][]':
									termsSearch.taxonomies[ termsSearch.taxonomies.length ] = inputs[ inputIndex ].value;
									break;
							}
						}

						mlaModal.settings.query[mlaModal.settings.state].termsSearch = termsSearch;
						$( '#mla-search-submit', toolbar ).click();
						return false;
					});

					$( '#mla-terms-search-input' ).keypress( function( e ){
						if ( 13 == e.which ) {
							e.preventDefault();
							$( '#mla-terms-search-submit' ).click();
						}
					});
				}
			}
		}); // wp.media.view.MlaTermsSearch
	} // mlaModal.settings.enableTermsSearch

/*	for debug : trace every event triggered in the wp.media.view.MlaSearch * /
function MlaSearchOn( eventName ) {
	console.log('wp.media.view.MlaSearch Event: ', eventName);
} // */

	/**
	 * Extended wp.media.view.Search
	 */
	if ( mlaModal.settings.enableSearchBox ) {
		wp.media.view.MlaSearch = wp.media.View.extend({
			tagName:   'div',
			className: 'mla-search-box',
			template: wp.media.template('mla-search-box'),

			attributes: {
				type: 'mla-search-box'
			},

			events: {
				'input': 'search',
				'change': 'search',
				'click': 'search',
				'search': 'search',
				'MlaSearch': 'search'
			},

			initialize: function() {
				var state = this.controller._state;
/*	for debug : trace every event triggered in the wp.media.view.MlaSearch * /
wp.media.view.MlaSearch.off( 'all', MlaSearchOn );
wp.media.view.MlaSearch.on( 'all', MlaSearchOn );
// */

				if ( 'undefined' === typeof mlaModal.settings.query[ state ] ) {
					mlaModal.settings.query[ state ] = _.clone( mlaModal.settings.query.initial );
					mlaModal.settings.query[ state ].searchFields = _.clone( mlaModal.settings.query.initial.searchFields );
				}
			},

			render: function() {
				var state = this.controller._state,
					data = _.extend( mlaModal.strings, mlaModal.settings.query[ state ] );
				this.$el.html( this.template( data ) );
				return this;
			},

			search: function( event ) {
				var state = this.controller._state, searchValues, searchFields, index;

				if ( ( 'input' == event.type ) && ( 's[mla_search_value]' == event.target.name ) ) {
					mlaModal.settings.query[ state ].searchValue = event.target.value;
					return;
				}

				if ( ( 'click' == event.type ) && ( 'mla_search_submit' != event.target.name ) ) {
					return;
				}

				switch ( event.target.name ) {
					case 's[mla_search_value]':
						mlaModal.settings.query[ state ].searchValue = event.target.value;
						break;
					case 'mla_search_submit':
						searchValues = {
							'mla_filter_month': mlaModal.settings.query[ state ].filterMonth,
							'mla_filter_term': mlaModal.settings.query[ state ].filterTerm,
							'mla_terms_search': mlaModal.settings.query[ state ].termsSearch,
							'mla_search_clicks': mlaModal.settings.query[ state ].searchClicks++,
							'mla_search_value': mlaModal.settings.query[ state ].searchValue,
							'mla_search_fields': mlaModal.settings.query[ state ].searchFields,
							'mla_search_connector': mlaModal.settings.query[ state ].searchConnector };
						this.model.set({ 's': searchValues });
						break;
					case 's[mla_search_connector]':
						mlaModal.settings.query[ state ].searchConnector = event.target.value;
						break;
					case 's[mla_search_title]':
						searchFields = mlaModal.settings.query[ state ].searchFields;
						index = searchFields.indexOf( 'title' );
						if ( -1 == index )
							searchFields.push( 'title' );
						else
							searchFields.splice( index, 1 );

						mlaModal.settings.query[ state ].searchFields = searchFields;
						break;
					case 's[mla_search_name]':
						index = mlaModal.settings.query[ state ].searchFields.indexOf( 'name' );
						if ( -1 == index )
							mlaModal.settings.query[ state ].searchFields.push( 'name' );
						else
							mlaModal.settings.query[ state ].searchFields.splice( index, 1 );
						break;
					case 's[mla_search_alt_text]':
						index = mlaModal.settings.query[ state ].searchFields.indexOf( 'alt-text' );
						if ( -1 == index )
							mlaModal.settings.query[ state ].searchFields.push( 'alt-text' );
						else
							mlaModal.settings.query[ state ].searchFields.splice( index, 1 );
						break;
					case 's[mla_search_excerpt]':
						index = mlaModal.settings.query[ state ].searchFields.indexOf( 'excerpt' );
						if ( -1 == index )
							mlaModal.settings.query[ state ].searchFields.push( 'excerpt' );
						else
							mlaModal.settings.query[ state ].searchFields.splice( index, 1 );
						break;
					case 's[mla_search_content]':
						index = mlaModal.settings.query[ state ].searchFields.indexOf( 'content' );
						if ( -1 == index )
							mlaModal.settings.query[ state ].searchFields.push( 'content' );
						else
							mlaModal.settings.query[ state ].searchFields.splice( index, 1 );
						break;
					case 's[mla_search_terms]':
						index = mlaModal.settings.query[ state ].searchFields.indexOf( 'terms' );
						if ( -1 == index )
							mlaModal.settings.query[ state ].searchFields.push( 'terms' );
						else
							mlaModal.settings.query[ state ].searchFields.splice( index, 1 );
						break;
				}
			}
		}); // wp.media.view.MlaSearch
	} else {
		wp.media.view.MlaSearch = wp.media.View.extend({
			tagName:   'span',
			className: 'mla-simulate-search-button',
			template: wp.media.template('mla-simulate-search-button'),

			attributes: {
				type: 'mla-simulate-search-button'
			},

			events: {
				'click': 'simulateSearch',
			},

			render: function() {
				this.$el.html( this.template( mlaModal.strings ) );
				return this;
			},

			simulateSearch: function() {
				var state = this.controller._state, searchValues = {
					'mla_filter_month': mlaModal.settings.query[ state ].filterMonth,
					'mla_filter_term': mlaModal.settings.query[ state ].filterTerm,
					'mla_terms_search': mlaModal.settings.query[ state ].termsSearch,
					'mla_search_clicks': mlaModal.settings.query[ state ].searchClicks++,
					'mla_search_value': mlaModal.settings.query[ state ].searchValue,
					'mla_search_fields': mlaModal.settings.query[ state ].searchFields,
					'mla_search_connector': mlaModal.settings.query[ state ].searchConnector };
				this.model.set({ 's': searchValues });
			}
		}); // Simulated search
	} // mlaModal.settings.enableSearchBox

	/**
	 * Add/replace media-toolbar controls with our own
	 */
	if ( mlaModal.settings.enableMimeTypes || mlaModal.settings.enableMonthsDropdown || mlaModal.settings.enableTermsDropdown || mlaModal.settings.enableTermsSearch || mlaModal.settings.enableSearchBox ) {
		wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
/*	for debug : trace every event triggered in this.controller * /
			toolbarEvent: function( eventName ) {
				var id = this.model.attributes.id;
				console.log('toolbarEvent this.model.attributes: ', JSON.stringify( this.model.attributes ));
				console.log('toolbarEvent( ', id, ' ) Event: ', eventName);
			}, // */

			createToolbar: function() {
				var filters, state = this.controller._state;
	
				mlaModal.settings.state = state;
				mlaModal.settings.$el = this.controller.$el;
				if ( 'undefined' === typeof mlaModal.settings.query[ state ] ) {
					mlaModal.settings.query[ state ] = _.clone( mlaModal.settings.query.initial );
					mlaModal.settings.query[ state ].searchFields = _.clone( mlaModal.settings.query.initial.searchFields );
				}

/*	for debug : trace every event triggered in the this.controller * /
this.stopListening( this.controller );
this.listenTo( this.controller, 'all', this.toolbarEvent );
// */

				// Apply the original method to create the toolbar
				//wp.media.view.AttachmentsBrowser.__super__.createToolbar.apply( this, arguments );
				mlaDrop.prototype.createToolbar.apply( this, arguments );
				mlaModal.utility.mlaAttachmentsBrowser = this;
				filters = this.options.filters;

				// Enhanced Media Library (eml) plugin has CSS styles that require this patch
				if ( typeof window.eml !== "undefined" ) {
					$( '.media-toolbar', this.$el ).css( 'overflow', 'hidden' );
				}
				
				if ( ( 'all' === filters ) && mlaModal.settings.enableMimeTypes ) {
					this.toolbar.unset( 'filters', { silent: true } );
					this.toolbar.set( 'filters', new wp.media.view.AttachmentFilters.Mla({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -80
					}).render() );
				}

				if ( ( 'uploaded' === filters ) && mlaModal.settings.enableMimeTypes ) {
					this.toolbar.unset( 'filters', { silent: true } );
					this.toolbar.set( 'filters', new wp.media.view.AttachmentFilters.MlaUploaded({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -80
					}).render() );
				}

				if ( this.options.search && mlaModal.settings.enableMonthsDropdown ) {
					this.toolbar.unset( 'dateFilter', { silent: true } );
					this.toolbar.set( 'dateFilter', new wp.media.view.AttachmentFilters.MlaMonths({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -75
					}).render() );
				}

				if ( this.options.search && mlaModal.settings.enableTermsDropdown ) {
					this.toolbar.set( 'terms', new wp.media.view.AttachmentFilters.MlaTerms({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -50
					}).render() );
				}

				if ( this.options.search && mlaModal.settings.enableTermsSearch ) {
					this.toolbar.set( 'termsSearch', new wp.media.view.MlaTermsSearch({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -50
					}).render() );
				}

				if ( this.options.search ) {
					if ( mlaModal.settings.enableSearchBox ) {
						this.controller.on( 'content:activate', this.hideDefaultSearch );
						this.controller.on( 'router:render', this.hideDefaultSearch );
						this.controller.on( 'uploader:ready', this.hideDefaultSearch );
						this.controller.on( 'edit:activate', this.hideDefaultSearch );

						this.toolbar.set( 'MlaSearch', new wp.media.view.MlaSearch({
							controller: this.controller,
							model:      this.collection.props,
							priority:   60
						}).render() );
					} else {
						this.toolbar.set( 'MlaSearch', new wp.media.view.MlaSearch({
							controller: this.controller,
							model:      this.collection.props,
							priority:   70
						}).render() );
					}
				}
			},

			hideDefaultSearch: function() {
				var defaultSearch = $( '#media-search-input', this.el );
				
				if ( 0 === defaultSearch.length ) {
					defaultSearch = $( 'div.media-toolbar-primary > input.search', this.el )
				}
				
				defaultSearch.hide();
			},

			updateFilters: function( taxonomy, selectMarkup ) {
				var newOptions = {};

				if ( this.options.search && mlaModal.settings.enableTermsDropdown && mlaModal.settings.termsTaxonomy == taxonomy ) {
					newOptions = mlaModal.utility.parseTermsOptions( selectMarkup );
					mlaModal.settings.termsClass = newOptions.termsClass;
					mlaModal.settings.termsText = newOptions.termsText;
					mlaModal.settings.termsValue = newOptions.termsValue;

					this.toolbar.unset( 'terms', { silent: true } );
					this.toolbar.set( 'terms', new wp.media.view.AttachmentFilters.MlaTerms({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -80
					}).render() );
				}
			}
		});
	} // one or more MLA options enabled

	/**
	 * extract value and text elements from Dropdown HTML option tags
	 */
	mlaModal.utility.parseTermsOptions = function ( selectMarkup ) {
		var termsOptions = {
			'termsClass': [ mlaModal.settings.termsClass[0], mlaModal.settings.termsClass[1] ],
			'termsText': [ mlaModal.settings.termsText[0], mlaModal.settings.termsText[1] ],
			'termsValue': [ mlaModal.settings.termsValue[0], mlaModal.settings.termsValue[1] ]
		}, termsCount = 2, termsIndex, termId,
		regEx = /\<option(( class=\"([^\"]+)\" )|( ))value=((\'([^\']+)\')|(\"([^\"]+)\"))([^\>]*)\>([^\<]*)\<.*/g,
		results = [];

		// Check for flat taxonomy updates
		if ( 'object' === typeof selectMarkup ) {
			termsCount = mlaModal.settings.termsValue.length;

			/*
			 * Create a sortable array of the existing terms, and
			 * remove existing terms from the selectMarkup array
			 */
			for ( termsIndex = 2; termsIndex < termsCount; termsIndex++ ) {
				results[ termsIndex ] = {
					'termsClass': mlaModal.settings.termsClass[ termsIndex ],
					'termsText': mlaModal.settings.termsText[ termsIndex ],
					'termsValue': mlaModal.settings.termsValue[ termsIndex ]
				};

				if ( 'undefined' !== typeof selectMarkup[ mlaModal.settings.termsValue[ termsIndex ] ] ) {
					delete selectMarkup[ mlaModal.settings.termsValue[ termsIndex ] ];
				}
			}

			// Add surviving terms, if any, to the sortable array
			for ( termId in selectMarkup ) {
				results[ termsIndex++ ] = {
					'termsClass': 'level-0',
					'termsText': selectMarkup[ termId ],
					'termsValue': termId.toString()
				};
			}

			if ( termsCount === termsIndex ) {
				// no changes
				return {
					'termsClass': mlaModal.settings.termsClass,
					'termsText': mlaModal.settings.termsText,
					'termsValue': mlaModal.settings.termsValue
				};
			}

			// Something was added; sort the array and re-build the filter arrays
			results.sort( function ( a, b ) {
				if ( a.termsText > b.termsText ) {
					return 1;
				} else {
					if ( a.termsText < b.termsText ) {
						return -1;
					} else {
						return 0;
					}
				}
			} );

			termsIndex = 2;
			for ( termId in results ) {
				termsOptions.termsClass[ termsIndex ] = results[ termId ].termsClass;
				termsOptions.termsText[ termsIndex ] = results[ termId ].termsText;
				termsOptions.termsValue[ termsIndex++ ] = results[ termId ].termsValue;
				}

			return termsOptions;
		}

		// Test the contents and skip the first match, the "no parent" placeholder
		results = regEx.exec( selectMarkup );
		while ( null !== ( results = regEx.exec( selectMarkup ) ) ) {
			termsOptions.termsClass[termsCount] = results[3];
			termsOptions.termsValue[termsCount] = ( 'undefined' === typeof results[6] ) ? results[9] : results[7];
			termsOptions.termsText[termsCount++] = results[11].replace( '&nbsp;', mlaModal.settings.termsIndent );
		}

		return termsOptions;
	};

	/**
	 * return a sorted array with any duplicate, whitespace or values removed
	 * Adapted from /wp-admin/js/post.js
	 */
	mlaModal.utility.arrayCleanup = function ( arrayIn ) {
		var arrayOut = [], isString = ( 'string' === typeof arrayIn );

		if( isString ) {
			arrayIn = arrayIn.split( mlaModal.settings.comma );
		}

		jQuery.each( arrayIn, function( key, val ) {
			val = jQuery.trim( val );

			if ( val && jQuery.inArray( val, arrayOut ) == -1 ) {
				arrayOut.push( val );
			}

		});

		arrayOut.sort();

		if( isString ) {
			arrayOut = arrayOut.join( mlaModal.settings.comma );
		}

		return arrayOut;
	};

	/**
	 * Extract the taxonomy name from an HTML id attribute,
	 * removing the 'mla-' and 'taxonomy-' prefixes.
	 */
	mlaModal.utility.parseTaxonomyId = function ( id ) {
		var taxonomyParts = id.split( '-' );

		taxonomyParts.shift(); // 'mla-'
		taxonomyParts.shift(); // 'taxonomy-'
		return taxonomyParts.join('-');
	};

	/**
	 * Support functions for flat taxonomies, e.g. Tags, Att. Tags
	 */
	mlaModal.tagBox = {
		/**
		 * Remove duplicate commas and whitespace from a string containing a tag list
		 */
		cleanTags : function( tags ) {
			var comma = mlaModal.settings.comma;
			if ( ',' !== comma ) {
				tags = tags.replace( new RegExp( comma, 'g' ), ',' );
			}

			tags = tags.replace( /\s*,\s*/g, ',' ).replace( /,+/g, ',' ).replace( /[,\s]+$/, '' ).replace( /^[,\s]+/, '' );

			if ( ',' !== comma ) {
				tags = tags.replace( /,/g, comma );
			}

			return tags;
		},

		/**
		 * Remove a tag from the list when the "X" button is clicked
		 */
		parseTags : function( el ) {
			var id = el.id, num = id.split( '-check-num-' )[1],
				tagsDiv = $( el ).closest( '.tagsdiv' ),
				thetags = tagsDiv.find( '.the-tags' ), comma = mlaModal.settings.comma,
				current_tags = thetags.val().split( comma ), new_tags = [];

			delete current_tags[ num ];

			$.each( current_tags, function( key, val ) {
				val = $.trim( val );
				if ( val ) {
					new_tags.push( val );
				}
			});

			thetags.val( this.cleanTags( new_tags.join( comma ) ) );

			this.quickClicks( tagsDiv );
			return false;
		},

		/**
		 * Build or rebuild the current tag list prefaced with "X" buttons,
		 * using the hidden '.the-tags' textbox field as input
		 */
		quickClicks : function( el ) {
			var thetags = $( '.the-tags', el ),
				tagchecklist = $( '.tagchecklist', el ),
				id = $( el ).attr( 'id' ),
				current_tags, disabled;

			if ( !thetags.length ) {
				return;
			}

			disabled = thetags.prop( 'disabled' );

			current_tags = thetags.val().split( mlaModal.settings.comma );
			tagchecklist.empty();

			$.each( current_tags, function( key, val ) {
				var span, xbutton;

				val = $.trim( val );

				if ( ! val ) {
					return;
				}

				// Create a new span, and ensure the text is properly escaped.
				span = $( '<span />' ).text( val );

				// If tags editing isn't disabled, create the X button.
				if ( ! disabled ) {
					xbutton = $( '<a id="' + id + '-check-num-' + key + '" class="ntdelbutton">X</a>' );
					xbutton.click( function(){ mlaModal.tagBox.parseTags( this ); });
					span.prepend( '&nbsp;' ).prepend( xbutton );
				}

				// Append the span to the tag list.
				tagchecklist.append( span );
			});
		},

		/**
		 * Add one or more tags from the 'input.newtag' text field or from the "a" element
		 */
		flushTags : function( tagsDiv, a, f ) {
			var tagsval, newtags, text,
				tags = $( '.the-tags', tagsDiv ),
				newtag = $( 'input.newtag', tagsDiv ),
				comma = mlaModal.settings.comma;

			a = a || false;

			text = a ? $( a ).text() : newtag.val();
			tagsval = tags.val();
			newtags = tagsval ? tagsval + comma + text : text;

			newtags = mlaModal.utility.arrayCleanup( this.cleanTags( newtags ) );
			tags.val( newtags );
			this.quickClicks( tagsDiv );

			if ( !a ) {
				newtag.val( '' );
			}

			if ( 'undefined' == typeof( f ) ) {
				newtag.focus();
			}

			return false;
		},

		/**
		 * Retrieve the tag cloud for this taxonomy
		 */
		getCloud : function( id, taxonomy ) {
			$.post( ajaxurl, {'action':'get-tagcloud', 'tax':taxonomy}, function( r, stat ) {
				if ( 0 === r || 'success' != stat ) {
					r = wpAjax.broken;
				}

				r = $( '<p id="tagcloud-'+taxonomy+'" class="the-tagcloud">'+r+'</p>' );
				$( 'a', r ).click( function(){
					mlaModal.tagBox.flushTags( $( this ).closest( '.mla-taxonomy-field' ).children( '.tagsdiv' ), this );
					return false;
				});

				$( '#'+id ).after( r );
			});
		},

		init : function( attachmentId, taxonomy, context ) {
			var tagsDiv, ajaxTag;
			tagsDiv = $( '#mla-taxonomy-' + taxonomy, context );
			ajaxTag = $( 'div.ajaxtag', tagsDiv );

			mlaModal.tagBox.quickClicks( tagsDiv );

			$( 'input.tagadd', ajaxTag ).click(function(){
				mlaModal.tagBox.flushTags( $(this).closest( '.tagsdiv' ) );
			});

			$( 'input.newtag', ajaxTag ).keyup( function( e ){
				if ( 13 == e.which ) {
					mlaModal.tagBox.flushTags( tagsDiv );
					return false;
				}
			}).keypress( function( e ){
				if ( 13 == e.which ) {
					e.preventDefault();
					return false;
				}
			}).each( function(){
				$( this ).suggest( ajaxurl + '?action=ajax-tag-search&tax=' + taxonomy, { delay: 500, resultsClass: 'mla_ac_results', selectClass: 'mla_ac_over', matchClass: 'mla_ac_match', minchars: 2, multiple: true, multipleSep: mlaModal.settings.comma + ' ' } );
			});

			// get the tag cloud on first click, then toggle visibility
			tagsDiv.siblings( ':first' ).click( function(){
				mlaModal.tagBox.getCloud( $( 'a', this ).attr( 'id' ), taxonomy );
				$( 'a', this ).unbind().click( function(){
					$( this ).siblings( '.the-tagcloud' ).toggle();
					return false;
				});
				return false;
			});

			// Update the taxonomy terms, if changed, on the server when the mouse leaves the tagsdiv area
			$( '.compat-field-' + taxonomy + ' td', context ).on( "mouseleave", function() {
				var query, tableData = this,
					oldTerms = mlaModal.utility.arrayCleanup( $( '.server-tags', tableData ).val() ),
					termList = mlaModal.utility.arrayCleanup( $( '.the-tags', tableData ).val() );

				if ( oldTerms === termList ) {
					return;
				}

				$( tableData ).css( 'opacity', '0.5' );

				/**
				 * wp.ajax.send( [action], [options] )
				 */
				query = {
					id: attachmentId,
				};
				query[ taxonomy ] = termList;

				wp.media.post( mlaModal.settings.ajaxUpdateCompatAction, query ).done( function( results ) {
					var tagsDiv, taxonomy, list;

					for ( taxonomy in results ) {
						if ( 'object' === typeof( results[ taxonomy][ 'object-terms' ] ) ) {
							if ( null !== mlaModal.utility.mlaAttachmentsBrowser ) {
								mlaModal.utility.mlaAttachmentsBrowser.updateFilters( taxonomy, results[ taxonomy][ 'object-terms' ] );
							}

							delete results[ taxonomy][ 'object-terms' ];
						}

						for ( list in results[ taxonomy ] ) {
							$( "#" + list, tableData ).replaceWith( results[ taxonomy ][ list ] );
						}

						tagsDiv = $( '#mla-taxonomy-' + taxonomy, tableData );
						mlaModal.tagBox.quickClicks( tagsDiv );
					}

					$( tableData ).css( 'opacity', '1.0' );
				});
			});

			// Don't let changes propogate to the Backbone model
			tagsDiv.on( 'change', function( event ) {
				event.stopPropagation();
				return false;
			});

			$( '.the-tags, .server-tags .newtag', tagsDiv ).on( 'change', function( event ) {
				event.stopPropagation();
				return false;
			});
		}
	}; // mlaModal.tagBox

	/*
	 * We can extend the AttachmentCompat object because it's not instantiated until
	 * the sidebar is created for a selected attachment.
	 */
	if ( mlaModal.settings.enableDetailsCategory || mlaModal.settings.enableDetailsTag ) {
		wp.media.view.AttachmentCompat = wp.media.view.AttachmentCompat.extend({
			initialize: function() {
				// Call the base method in the super class
				wp.media.view.AttachmentCompat.__super__.initialize.apply( this, arguments );

				// Hook the 'ready' event when the sidebar has been rendered so we can add our enhancements
				this.on( 'ready', function() {
					mlaModal.utility.hookCompatTaxonomies( this.model.get('id'), this.el );
				});
			}
		});
	}

	/*
	 * We can extend the model.Selection object because it's not instantiated until
	 * the sidebar is created for a selected attachment.
	 */
	if ( mlaModal.settings.enableDetailsCategory || mlaModal.settings.enableDetailsTag ) {
		wp.media.model.Selection = wp.media.model.Selection.extend({
/*	for debug : trace every event triggered in the wp.media.model.Selection * /
			selectionEvent: function( eventName, eventModel, eventContext, eventFlags ) {
				var attributes = null, id = 0, cid = 'none';

				if ( 'undefined' != typeof eventModel.cid ) {
					cid = eventModel.cid;
				}

				if ( 'undefined' != typeof eventModel.id ) {
					id = eventModel.id;
				}

				if ( 'undefined' != typeof eventModel.attributes ) {
					attributes = _.clone( eventModel.attributes );
					delete( attributes.file );
					console.log('selectionEvent ', eventName, '( ', cid, ', ', id, ' ) eventModel.attributes: ', JSON.stringify( attributes ) );
				} else {
					console.log('selectionEvent ', eventName, '( ', cid, ', ', id, ' )' );
				}
			}, // */

			initialize: function() {
				// Call the base method in the super class
				wp.media.model.Selection.__super__.initialize.apply( this, arguments );

/*	for debug : trace every event triggered in the wp.media.model.Selection * /
this.stopListening( this );
this.listenTo( this, 'all', this.selectionEvent );
// */

				// Hook the 'selection:reset' event so we can add our enhancements when it's done
				this.on( 'selection:reset', function( /* model */ ) {
					mlaModal.cid = null;
				});

				// Hook the 'selection:unsingle' event so we can add our enhancements when it's done
				this.on( 'selection:unsingle', function( /* model */ ) {
					mlaModal.cid = null;
				});

				// Hook the 'selection:single' event so we can add our enhancements when it's done
				this.on( 'selection:single', function( model ) {
					mlaModal.cid = model.cid;
				});

				// Hook the 'change:uploading' event so we can add our enhancements when it's done
				this.on( 'change:uploading', function( /* model */ ) {
					mlaModal.uploading = true;
				});

				// Hook the 'change' event when the sidebar has been rendered so we can add our enhancements
				this.on( 'change', function( model ) {
					var hookCompat = false, changed;

					if ( mlaModal.uploading && mlaModal.cid === model.cid ) {
						mlaModal.uploading = false;
						hookCompat = true;
					} else {
						// ignore changes during upload
						if ( false === model.attributes.uploading ) {
							// filter out trivial changes
							changed = _.clone( model.changed );
							delete changed.title;
							delete changed.caption;
							delete changed.alt;
							delete changed.description;

							if ( ! _.isEmpty( changed ) ) {
								hookCompat = true;
							}
						}
					}

					if ( true === hookCompat ) {
						mlaModal.utility.hookCompatTaxonomies( model.get('id'), mlaModal.settings.$el );
						//mlaModal.utility.hookCompatTaxonomies( model.get('id'), wp.media.frame.$el );
					}
				});
			}
		});
	}

	/**
	 * Install the "click to expand" handler for MLA Searchable Taxonomy Meta Boxes
	 */
	mlaModal.utility.hookCompatTaxonomies = function( attachmentId, context ) {
		var taxonomy, clickTaxonomy = null;

		$('.mla-taxonomy-field .categorydiv', context ).each( function(){
			taxonomy = mlaModal.utility.parseTaxonomyId( $(this).attr('id') );

			if ( -1 != mlaModal.settings.enhancedTaxonomies.indexOf( taxonomy ) ) {
				// Load the taxonomy checklists on first expansion
				$( '.compat-field-' + taxonomy + ' th', context ).click( { id: attachmentId, currentTaxonomy: taxonomy, el: context }, function( event ) {
					mlaModal.utility.fillCompatTaxonomies( event.data );
				});

				// Delete the default row, show the enhanced row
				$( 'tr.compat-field-' + taxonomy, context ).each( function(){
					if ( $(this).hasClass('mla-taxonomy-row') ) {
						$(this).show();
					} else {
						$(this).remove();
					}
				});

				if ( null === clickTaxonomy ) {
					clickTaxonomy = taxonomy;
				}
				} else {
				// Delete the enhanced row
				$( 'tr.compat-field-' + taxonomy, context ).each( function(){
					if ( $(this).hasClass('mla-taxonomy-row') ) {
						$(this).remove();
					}
				});
			}
		});

		$('.mla-taxonomy-field .tagsdiv', context ).each( function(){
			taxonomy = mlaModal.utility.parseTaxonomyId( $(this).attr('id') );

			if ( -1 != mlaModal.settings.enhancedTaxonomies.indexOf( taxonomy ) ) {
				// Load the taxonomy checklists on first expansion
				$( '.compat-field-' + taxonomy + ' th', context ).click( { id: attachmentId, currentTaxonomy: taxonomy, el: context }, function( event ) {
					mlaModal.utility.fillCompatTaxonomies( event.data );
				});

				// Delete the default row, show the enhanced row
				$( 'tr.compat-field-' + taxonomy, context ).each( function(){
					if ( $(this).hasClass('mla-taxonomy-row') ) {
						$(this).show();
					} else {
						$(this).remove();
					}
				});

				if ( null === clickTaxonomy ) {
					clickTaxonomy = taxonomy;
				}
			} else {
				// Delete the enhanced row
				$( 'tr.compat-field-' + taxonomy, context ).each( function(){
					if ( $(this).hasClass('mla-taxonomy-row') ) {
						$(this).remove();
					}
				});
			}
		});

		if ( mlaModal.settings.enableTermsAutofill && null !== clickTaxonomy ) {
			$( '.compat-field-' + clickTaxonomy + ' th', context ).click();
		}
	};

	/**
	 * Replace the "Loading..." placeholders with the MLA Searchable Taxonomy Meta Boxes
	 */
	mlaModal.utility.fillCompatTaxonomies = function( data ) {
		var context = data.el, query = [], taxonomy, fieldClass;

		$('.mla-taxonomy-field .categorydiv', context ).each( function(){
			taxonomy = mlaModal.utility.parseTaxonomyId( $(this).attr('id') );
			query[ query.length ] = taxonomy;
			fieldClass = '.compat-field-' + taxonomy;

			// Save the initial markup for when we change attachments
			if ( "undefined" === typeof( mlaModal.initialHTML[ taxonomy ] ) ) {
				mlaModal.initialHTML[ taxonomy ] = $( fieldClass, context ).html();
			} else {
				$( fieldClass, context ).html( mlaModal.initialHTML[ taxonomy ] );
			}

			$( fieldClass + ' .categorydiv', context ).html( mlaModal.strings.loadingText );
		});

		$( '.mla-taxonomy-field .tagsdiv', context ).each( function(){
			taxonomy = mlaModal.utility.parseTaxonomyId( $(this).attr('id') );
			query[ query.length ] = taxonomy;
			fieldClass = '.compat-field-' + taxonomy;

			if ( "undefined" === typeof( mlaModal.initialHTML[ taxonomy ] ) ) {
				mlaModal.initialHTML[ taxonomy ] = $( fieldClass, context ).html();
			} else {
				$( fieldClass, context ).html( mlaModal.initialHTML[ taxonomy ] );
			}

			$( fieldClass + ' .tagsdiv', context ).html( mlaModal.strings.loadingText );
		});

		if ( query.length ) {
			/**
			 * wp.ajax.send( [action], [options] )
			 *
			 * Sends a POST request to WordPress.
			 *
			 * @param  {string} action  The slug of the action to fire in WordPress.
			 * @param  {object} options The options passed to jQuery.ajax.
			 * @return {$.promise}      A jQuery promise that represents the request.
			 */
			wp.media.post( mlaModal.settings.ajaxFillCompatAction, {
				// json: true,
				id: data.id,
				query: query,
			}).done( function( results ) {
				var taxonomy, fieldClass;

				for ( taxonomy in results ) {
					fieldClass = '.compat-field-' + taxonomy;

					$( fieldClass, context ).html( results[ taxonomy ] );
				}

				mlaModal.utility.supportCompatTaxonomies( data );
				$( '.compat-field-' + data.currentTaxonomy + ' td', context ).show();
			});
		} // query.length
	};

	/**
	 * Support the MLA Searchable Taxonomy Meta Boxes
	 */
	mlaModal.utility.supportCompatTaxonomies = function( data ) {
		var attachmentId = data.id, context = data.el;

		if ( mlaModal.settings.enableDetailsCategory ) {
			$( '.mla-taxonomy-field .categorydiv', context ).each( function(){
				var thisJQuery = $(this), catAddBefore, catAddAfter, taxonomy, settingName,
					taxonomyIdPrefix, taxonomyNewIdSelector, taxonomySearchIdSelector, taxonomyTermsId;

				taxonomy = mlaModal.utility.parseTaxonomyId( $(this).attr('id') );
				settingName = taxonomy + '_tab';
				taxonomyIdPrefix = '#mla-' + taxonomy;
				taxonomyNewIdSelector = '#mla-new-' + taxonomy;
				taxonomySearchIdSelector = '#mla-search-' + taxonomy;
				taxonomyTermsId = '#mla-attachments-' + attachmentId + '-' + taxonomy;

				if ( taxonomy == 'category' ) {
					settingName = 'cats';
				}

				// override "Media Categories" style sheet
				thisJQuery.find( '.category-tabs' ).show();

				// Expand/collapse the meta box contents
				$( '.compat-field-' + taxonomy + ' th', context ).click( function() {
					$(this).siblings( 'td' ).slideToggle();
				});

				// Update the taxonomy terms, if changed, on the server when the mouse leaves the checklist area
				thisJQuery.on( "mouseleave", function() {
					var query, oldTerms, termList = [], checked =  thisJQuery.find( taxonomyIdPrefix + '-checklist input:checked' );

					checked.each( function() {
						termList[ termList.length ] = $(this).val();
					});

					termList.sort( function( a, b ) { return a - b; } );
					termList = termList.join( ',' );

					oldTerms = thisJQuery.siblings( taxonomyTermsId ).val();
					if ( oldTerms === termList ) {
						return;
					}

					thisJQuery.siblings( taxonomyTermsId ).val( termList );
					thisJQuery.prop( 'disabled', true );

					/**
					 * wp.ajax.send( [action], [options] )
					 */
					query = {
						id: attachmentId,
					};
					query[ taxonomy ] = termList;

					wp.media.post( mlaModal.settings.ajaxUpdateCompatAction, 
						query ).done( function( results ) {
						var taxonomy, list;

						for ( taxonomy in results ) {
							for ( list in results[ taxonomy ] ) {
								thisJQuery.find( "#" + list ).html( results[ taxonomy ][ list ] );
							}
						}

					thisJQuery.find( taxonomySearchIdSelector ).val( '' );
					thisJQuery.find( taxonomyIdPrefix + '-searcher' ).addClass( 'mla-hidden-children' );
					thisJQuery.prop( 'disabled', false );
					});
				});

				// Don't let checkbox changes propogate to the Backbone model
				thisJQuery.on( 'change input[type="checkbox"]', function( event ) {
					event.stopPropagation();
					return false;
				});

				/*
				 * Taxonomy meta box code from /wp-admin/js/post.js
				 */

				// Switch between "All ..." and "Most Used"
				thisJQuery.find( taxonomyIdPrefix + '-tabs a' ).click( function(){
					var t = $(this).attr('href');
					$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
					thisJQuery.find( taxonomyIdPrefix + '-tabs' ).siblings('.tabs-panel').hide();
					thisJQuery.find( t ).show();
					$(this).focus();

					// Store the "all/most used" setting in a cookie
					if ( "#mla-" + taxonomy + '-all' == t ) {
						deleteUserSetting( settingName );
					} else {
						setUserSetting( settingName, 'pop' );
					}

					return false;
				});

				// Reflect tab selection remembered in cookie
				if ( getUserSetting( settingName ) ) {
					thisJQuery.find( taxonomyIdPrefix + '-tabs a[href="#mla-' + taxonomy + '-pop"]' ).click();
				}

				// Toggle the "Add New ..." sub panel
				thisJQuery.find( taxonomyIdPrefix + '-add-toggle' ).click( function() {
					thisJQuery.find( taxonomyIdPrefix + '-searcher' ).addClass( 'mla-hidden-children' );
					thisJQuery.find( taxonomyIdPrefix + '-adder' ).toggleClass( 'mla-hidden-children' );
					thisJQuery.find( taxonomyIdPrefix + '-tabs a[href="#mla-' + taxonomy + '-all"]' ).click();

					thisJQuery.find( taxonomyIdPrefix + '-checklist li' ).show();
					thisJQuery.find( taxonomyIdPrefix + '-checklist-pop li' ).show();

					if ( false === thisJQuery.find( taxonomyIdPrefix + '-adder' ).hasClass( 'mla-hidden-children' ) ) {
						thisJQuery.find( taxonomyNewIdSelector ).val( '' ).removeClass( 'form-input-tip' );
						thisJQuery.find( taxonomyNewIdSelector ).focus();
					}
					return false;
				});

				// Convert "Enter" key to a click
				thisJQuery.find( taxonomyNewIdSelector ).keypress( function(event){
					if( 13 === event.keyCode ) {
						event.preventDefault();
						thisJQuery.find( taxonomyIdPrefix + '-add-submit' ).click();
					}
				});

				thisJQuery.find( taxonomyIdPrefix + '-add-submit' ).click( function(){
					thisJQuery.find( taxonomyNewIdSelector ).focus();
				});

				catAddBefore = function( s ) {
					if ( ! thisJQuery.find( taxonomyNewIdSelector ).val() )
						return false;

					s.data += '&' + thisJQuery.find( taxonomyIdPrefix + '-checklist :checked' ).serialize();
					thisJQuery.prop( 'disabled', true );
					return s;
				};

				catAddAfter = function( r, s ) {
					var sup, drop = thisJQuery.find( '#new' + taxonomy + '_parent' );

					thisJQuery.prop( 'disabled', false );
					if ( 'undefined' != s.parsed.responses[0] && ( sup = s.parsed.responses[0].supplemental.newcat_parent ) ) {
						drop.before( sup );
						drop.remove();
						if ( null !== mlaModal.utility.mlaAttachmentsBrowser ) {
							mlaModal.utility.mlaAttachmentsBrowser.updateFilters( taxonomy, sup );
						}
					}
				};

				// wpList is in /wp-includes/js/wp-lists.js
				// handled in /wp-admin/includes/ajax-actions.php function _wp_ajax_add_hierarchical_term()
				thisJQuery.find( taxonomyIdPrefix + '-checklist' ).wpList({
					alt: '',
					response: 'mla-' + taxonomy + '-ajax-response',
					addBefore: catAddBefore,
					addAfter: catAddAfter
				});

				// Synchronize checkbox changes between "All ..." and "Most Used" panels
				thisJQuery.find( taxonomyIdPrefix + '-checklist, ' + taxonomyIdPrefix + '-checklist-pop' ).on( 'click', 'li.popular-category > label input[type="checkbox"]', function() {
					var t = $(this), c = t.is(':checked'), id = t.val();

					if ( id && t.parents( '#mla-taxonomy-'+ taxonomy ).length ) {
						$('#in-' + taxonomy + '-' + id + ', #in-popular-' + taxonomy + '-' + id).prop( 'checked', c );
					}
				});

				/*
				 * Searchable meta box code from mla-edit-media-scripts.js
				 */
				$.extend( $.expr[":"], {
					"matchTerms": function( elem, i, match, array ) {
						return ( elem.textContent || elem.innerText || "" ).toLowerCase().indexOf( ( match[3] || "" ).toLowerCase() ) >= 0;
					}
				});

				thisJQuery.find( taxonomySearchIdSelector ).keypress( function( event ){
					// Enter key cancels the filter and closes the search field
					if( 13 === event.keyCode ) {
						event.preventDefault();
						thisJQuery.find( taxonomySearchIdSelector ).val( '' );
						thisJQuery.find( taxonomyIdPrefix + '-searcher' ).addClass( 'mla-hidden-children' );

						thisJQuery.find( taxonomyIdPrefix + '-checklist li' ).show();
						thisJQuery.find( taxonomyIdPrefix + '-checklist-pop li' ).show();
						return;
					}

				} );

				thisJQuery.find( taxonomySearchIdSelector ).keyup( function( event ){
					var searchValue, termList, termListPopular, matchingTerms, matchingTermsPopular;

					// keyup happens after keypress; change the focus if the text box has been closed
					if( 13 === event.keyCode ) {
						event.preventDefault();
						thisJQuery.find( taxonomyIdPrefix + '-search-toggle' ).focus();
						return;
					}

					searchValue = thisJQuery.find( taxonomySearchIdSelector ).val();
					termList = thisJQuery.find( taxonomyIdPrefix + '-checklist li' );
					termListPopular = thisJQuery.find( taxonomyIdPrefix + '-checklist-pop li' );

					if ( 0 < searchValue.length ) {
						termList.hide();
						termListPopular.hide();
					} else {
						termList.show();
						termListPopular.show();
					}

					matchingTerms = thisJQuery.find( taxonomyIdPrefix + "-checklist label:matchTerms('" + searchValue + "')");
					matchingTerms.closest( 'li' ).find( 'li' ).andSelf().show();
					matchingTerms.parents( taxonomyIdPrefix + '-checklist li' ).show();

					matchingTermsPopular = thisJQuery.find( taxonomyIdPrefix + "-checklist-pop label:matchTerms('" + searchValue + "')");
					matchingTermsPopular.closest( 'li' ).find( 'li' ).andSelf().show();
					matchingTermsPopular.parents( taxonomyIdPrefix + '-checklist li' ).show();
				} );

				// Toggle the "Search" sub panel
				thisJQuery.find( taxonomyIdPrefix + '-search-toggle' ).click( function() {
					thisJQuery.find( taxonomyIdPrefix + '-adder ').addClass( 'mla-hidden-children' );
					thisJQuery.find( taxonomyIdPrefix + '-searcher' ).toggleClass( 'mla-hidden-children' );
					thisJQuery.find( taxonomyIdPrefix + '-tabs a[href="#mla-' + taxonomy + '-all"]' ).click();

					thisJQuery.find( taxonomyIdPrefix + '-checklist li' ).show();
					thisJQuery.find( taxonomyIdPrefix + '-checklist-pop li' ).show();

					if ( false === thisJQuery.find( taxonomyIdPrefix + '-searcher' ).hasClass( 'mla-hidden-children' ) ) {
						thisJQuery.find( taxonomySearchIdSelector ).val( '' ).removeClass( 'form-input-tip' );
						thisJQuery.find( taxonomySearchIdSelector ).focus();
					}

					return false;
				});
			}); // .categorydiv.each
		} // mlaModal.settings.enableDetailsCategory

		if ( mlaModal.settings.enableDetailsTag ) {
			$('.mla-taxonomy-field .tagsdiv', context ).each( function(){
				var taxonomy = mlaModal.utility.parseTaxonomyId( $(this).attr('id') );

				// Expand/collapse the meta box contents
				$( '.compat-field-' + taxonomy + ' th', context ).click( function() {
					$(this).siblings( 'td' ).slideToggle();
				});

				// Install support for flat taxonomies
				mlaModal.tagBox.init( attachmentId, taxonomy, context );
			}); // .tagsdiv.each
		} // mlaModal.settings.enableDetailsTag
	}; // mlaModal.utility.supportCompatTaxonomies
}( jQuery ) );
