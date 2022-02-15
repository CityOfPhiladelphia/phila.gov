( function($, MAP) {

    $(document).on( 'MOAdminPointers.setup_done', function( e, data ) {
        e.stopImmediatePropagation();
        MAP.setPlugin( data ); // open first popup
    } );

    $(document).on( 'MOAdminPointers.current_ready', function( e ) {
        e.stopImmediatePropagation();
        MAP.openPointer(); // open a popup
    } );

    MAP.js_pointers = {};        // contain js-parsed pointer objects
    MAP.first_pointer = false;   // contain first pointer anchor jQuery object
    MAP.current_pointer = false; // contain current pointer jQuery object
    MAP.last_pointer = false;    // contain last pointer jQuery object
    MAP.visible_pointers = [];   // contain ids of pointers whose anchors are visible

    MAP.hasNext = function( data ) { // check if a given pointer has valid next property
        return typeof data.next === 'string'
            && data.next !== ''
            && typeof MAP.js_pointers[data.next].data !== 'undefined'
            && typeof MAP.js_pointers[data.next].data.id === 'string';
    };

    MAP.isVisible = function( data ) { // check if anchor for given pointer is visible
        return $.inArray( data.id, MAP.visible_pointers ) !== -1;
    };

    // given a pointer object, return its the anchor jQuery object if available
    // otherwise return first available, lookin at next property of subsequent pointers
    MAP.getPointerData = function( data ) {
        var $target = $( data.anchor_id );
        if ( $.inArray(data.id, MAP.visible_pointers) !== -1 ) {
            return { target: $target, data: data };
        }
        $target = false;
        while( MAP.hasNext( data ) && ! MAP.isVisible( data ) ) {
            data = MAP.js_pointers[data.next].data;
            if ( MAP.isVisible( data ) ) {
                $target = $(data.anchor_id);
            }
        }
        return MAP.isVisible( data )
            ? { target: $target, data: data }
            : { target: false, data: false };
    };

    // take pointer data and setup pointer plugin for anchor element
    MAP.setPlugin = function( data ) {
        jQuery('#overlay').show();

        if ( typeof MAP.last_pointer === 'object') {
            MAP.last_pointer.pointer('destroy');
            MAP.last_pointer = false;
        }
        jQuery(data.anchor_id).css('z-index','2');


        MAP.current_pointer = false;
        var pointer_data = MAP.getPointerData( data );
        if ( ! pointer_data.target || ! pointer_data.data ) {
            return;
        }
        $target = pointer_data.target;
        data = pointer_data.data;
        $pointer = $target.pointer({
            content: data.title + data.content,
            position: { edge: data.edge, align: data.align },
            close: function() {
                jQuery(data.anchor_id).css('z-index','0');
                jQuery('#overlay').hide();
                $.post( ajaxurl, { pointer: data.id, action: 'dismiss-wp-pointer' } );
            }
        });
        MAP.current_pointer = { pointer: $pointer, data: data };
        $(document).trigger( 'MOAdminPointers.current_ready' );
    };

    // scroll the page to current pointer then open it
    MAP.openPointer = function() {
        var $pointer = MAP.current_pointer.pointer;
        if ( ! typeof $pointer === 'object' ) {
            return;
        }
        $('html, body').animate({ // scroll page to pointer
            scrollTop: $pointer.offset().top-120
        }, 300, function() { // when scroll complete
            MAP.last_pointer = $pointer;
            var $widget = $pointer.pointer('widget');
            MAP.setNext( $widget, MAP.current_pointer.data );
            $pointer.pointer( 'open' ); // open
        });


    };

    // if there is a next pointer set button label to "Next", to "Close" otherwise
    MAP.setNext = function( $widget, data ) {
        if ( typeof $widget === 'object' ) {
            var $buttons = $widget.find('.wp-pointer-buttons').eq(0);
            var $close = $buttons.find('a.close').eq(0);
            $button = $close.clone(true, true).removeClass('close');
            $close_button = $close.clone(true, true).removeClass('close');
            $buttons.find('a.close').remove();
            $button.addClass('button').addClass('button-primary');
            $close_button.addClass('button').addClass('button-primary');

            has_next = false;
            if ( MAP.hasNext( data ) ) {
                has_next_data = MAP.getPointerData(MAP.js_pointers[data.next].data);
                has_next = has_next_data.target && has_next_data.data;
                $button.html(MAP.next_label).appendTo($buttons);
                $close_button.html(MAP.close_label).appendTo($buttons);
                jQuery($close_button).css('margin-right','10px');

                jQuery($close_button).click(function (e) {
                    jQuery('#overlay').hide();
                    setTimeout(function () {
                        jQuery('#dismiss_pointers').submit();
                    }, 1000);
                });
            }
            else
            {
                var label = has_next ? MAP.next_label : MAP.close_label;
                jQuery($button).css('margin-right','10px');
                $button.html(label).appendTo($buttons);
            }
            jQuery($button).click(function () {
                if(data.isdefault ==='yes')
                {

                    switch(data.anchor_id){
                        case '#mo_saml_idps_grid_div':
                            document.getElementById('sp-setup-tab').className = 'nav-tab';
                            document.getElementById('sp-meta-tab').className = 'nav-tab nav-tab-active';
                            document.getElementById('save_tab').style.display = 'none';
                            document.getElementById('config_tab').style.display='block';
                            break;
                        case '#selected_idp_div':
                            document.getElementById('sp-setup-tab').className = 'nav-tab';
                            document.getElementById('sp-meta-tab').className = 'nav-tab nav-tab-active';
                            document.getElementById('save_tab').style.display = 'none';
                            document.getElementById('config_tab').style.display='block';
                            break;
                        case '#metadata_url':
                            document.getElementById('sp-setup-tab').className = 'nav-tab nav-tab-active';
                            document.getElementById('sp-meta-tab').className = 'nav-tab';
                            document.getElementById('save_tab').style.display = 'block';
                            document.getElementById('config_tab').style.display = 'none';
                            document.getElementById('selected_idp_div').style.zIndex = 0;
                            break;
                        case '#test_config':
                            document.getElementById('sp-setup-tab').className = 'nav-tab';
                            document.getElementById('attr-role-tab').className = 'nav-tab nav-tab-active';
                            document.getElementById('save_tab').style.display = 'none';
                            document.getElementById('opt_tab').style.display = 'block';
                            break;
                        case '#miniorange-role-mapping':
                            document.getElementById('attr-role-tab').className = 'nav-tab';
                            document.getElementById('redir-sso-tab').className = 'nav-tab nav-tab-active';
                            document.getElementById('opt_tab').style.display = 'none';
                            document.getElementById('redir_sso_tab').style.display = 'block';
                            break;
                        case '#minorange-use-widget':
                            document.getElementById('redir-sso-tab').className = 'nav-tab';
                            document.getElementById('addon-tab').className = 'nav-tab nav-tab-active';
                            document.getElementById('redir_sso_tab').style.display = 'none';
                            document.getElementById('addons_tab').style.display = 'block';
                            document.getElementById('support-form').style.display = 'block';
                            break;
                        case '#miniorange-addons':
                            document.getElementById('addon-tab').className = 'nav-tab';
                            document.getElementById('sp-setup-tab').className = 'nav-tab nav-tab-active';
                            document.getElementById('addons_tab').style.display = 'none';
                            document.getElementById('save_tab').style.display = 'block';
                            document.getElementById('support-form').style.display = 'block';
                            break;
                    }
                }

                if ( MAP.hasNext( data ) ) {
                    MAP.setPlugin( MAP.js_pointers[data.next].data );
                }
            });
        }
    };

    $(MAP.pointers).each(function(index, pointer) { // loop pointers data
        if( ! $().pointer ) return;      // do nothing if pointer plugin isn't available
        MAP.js_pointers[pointer.id] = { data: pointer };
        var $target = $(pointer.anchor_id);
        if ( $target.length) { // anchor exists and is visible?
            MAP.visible_pointers.push(pointer.id);
            if ( ! MAP.first_pointer ) {
                MAP.first_pointer = pointer;
            }
        }
        if ( index === ( MAP.pointers.length - 1 ) && MAP.first_pointer ) {
            $(document).trigger( 'MOAdminPointers.setup_done', MAP.first_pointer );
        }
    });

} )(jQuery, MOAdminPointers); // MOAdminPointers is passed by `wp_localize_script`