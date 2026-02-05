<?php

// AGGRESSIVE SOLUTION: Block CSS files that are loaded 400+ times per the HAR analysis
add_action( 'admin_init', 'phila_block_duplicate_css_files', 1 );

function phila_block_duplicate_css_files() {
    // Only run in admin context
    if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
        return;
    }
    
    $screen = get_current_screen();
    if ( $screen && ( $screen->base === 'post' || $screen->base === 'post-new' ) ) {
        
        // Start output buffering to intercept and modify HTML
        add_action( 'admin_head', 'phila_start_css_filtering', -999 );
        add_action( 'admin_footer', 'phila_end_css_filtering', 999 );
    }
}

function phila_start_css_filtering() {
    ob_start( 'phila_filter_duplicate_css' );
}

function phila_end_css_filtering() {
    if ( ob_get_level() ) {
        ob_end_flush();
    }
}

function phila_filter_duplicate_css( $html ) {
    // Track loaded CSS files
    static $loaded_css = array();
    
    // Define the problematic files from HAR analysis (400+ requests each)
    $problematic_files = array(
        '/wp-includes/css/dashicons.min.css',
        '/wp-includes/js/tinymce/skins/wordpress/wp-content.css',
        '/wp-includes/js/tinymce/skins/lightgray/content.min.css'
    );
    
    // Remove duplicate link tags
    foreach ( $problematic_files as $file ) {
        $pattern = '/<link[^>]*href=["\'][^"\']*' . preg_quote( $file, '/' ) . '[^"\']*["\'][^>]*>/i';
        $matches = array();
        preg_match_all( $pattern, $html, $matches );
        
        if ( count( $matches[0] ) > 1 ) {
            // Keep only the first occurrence, remove the rest
            for ( $i = 1; $i < count( $matches[0] ); $i++ ) {
                $html = str_replace( $matches[0][$i], 
                    '<!-- BLOCKED: Duplicate ' . basename( $file ) . ' (#' . ($i + 1) . ') -->', 
                    $html 
                );
            }
            
            error_log( "Blocked " . (count( $matches[0] ) - 1) . " duplicate instances of " . basename( $file ) );
        }
    }
    
    return $html;
}

// TARGETED FIX: Prevent TinyMCE CSS loading after first instance
add_action( 'admin_enqueue_scripts', 'custom_enqueue_tinymce_script' );

function custom_enqueue_tinymce_script() {
    // Only load on pages that have the editor and in admin context
    if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
        return;
    }
    
    $screen = get_current_screen();
    if ( $screen && ( $screen->base === 'post' || $screen->base === 'post-new' || $screen->base === 'edit' ) ) {
        // Use wp_register_script to prevent duplicate loading
        if ( ! wp_script_is( 'phila-custom-tinymce', 'registered' ) ) {
            wp_register_script( 'phila-custom-tinymce', plugins_url('js/tiny-mce.js', __FILE__), array('wp-tinymce'), '1.0.0', true );
        }
        if ( ! wp_script_is( 'phila-custom-tinymce', 'enqueued' ) ) {
            wp_enqueue_script( 'phila-custom-tinymce' );
        }
    }
}

// CRITICAL FIX: Optimize TinyMCE CSS loading to prevent thousands of duplicate loads
add_filter( 'tiny_mce_before_init', 'phila_optimize_tinymce_css_loading', 10, 2 );

function phila_optimize_tinymce_css_loading( $mceInit, $editor_id ) {
    // Prevent duplicate content CSS loading by using a shared cache key
    static $css_cache_added = false;
    
    if ( ! $css_cache_added ) {
        // Only load the CSS once across all editors
        $mceInit['cache_suffix'] = 'phila-shared-' . get_current_blog_id();
        $css_cache_added = true;
    } else {
        // For subsequent editors, use the same cache suffix to prevent reloading
        $mceInit['cache_suffix'] = 'phila-shared-' . get_current_blog_id();
        // Prevent additional CSS loads
        $mceInit['content_css'] = false;
    }
    
    return $mceInit;
}

// Optimize Meta Box TinyMCE instances specifically
add_filter( 'rwmb_wysiwyg_settings', 'phila_optimize_metabox_tinymce', 10, 1 );

function phila_optimize_metabox_tinymce( $settings, $field = null ) {
    // Use shared TinyMCE settings to prevent duplicate CSS loading
    static $editor_count = 0;
    $editor_count++;
    
    // Only the first editor gets to load CSS
    if ( $editor_count > 1 ) {
        // Remove all content CSS for subsequent editors
        $settings['content_css'] = false;
        $settings['content_style'] = '';
        
        // Also remove editor CSS
        if ( isset( $settings['tinymce'] ) ) {
            $settings['tinymce']['content_css'] = false;
            $settings['tinymce']['skin'] = false; // Prevent skin loading
        }
        
        error_log( "TinyMCE editor #{$editor_count} - CSS loading disabled" );
    } else {
        error_log( "TinyMCE editor #{$editor_count} - CSS loading allowed" );
    }
    
    return $settings;
}

// AGGRESSIVE FIX: Prevent duplicate CSS loads at the TinyMCE level
add_action( 'admin_head', 'phila_prevent_tinymce_css_duplicates', 1 );

function phila_prevent_tinymce_css_duplicates() {
    // Only run in admin context
    if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
        return;
    }
    
    $screen = get_current_screen();
    if ( $screen && ( $screen->base === 'post' || $screen->base === 'post-new' ) ) {
        echo '<script type="text/javascript">
        // NUCLEAR OPTION: Block specific files that appear 400+ times in HAR
        (function() {
            var blockedFiles = [
                "dashicons.min.css",
                "wp-content.css", 
                "content.min.css"
            ];
            var loadedFiles = new Set();
            var blockedCount = {};
            
            // Initialize counters
            blockedFiles.forEach(function(file) {
                blockedCount[file] = 0;
            });
            
            // Override document.createElement for link elements
            var originalCreateElement = document.createElement;
            document.createElement = function(tagName) {
                var element = originalCreateElement.apply(this, arguments);
                
                if (tagName.toLowerCase() === "link" && element.tagName === "LINK") {
                    var originalSetAttribute = element.setAttribute;
                    element.setAttribute = function(name, value) {
                        if (name === "href" && value) {
                            for (var i = 0; i < blockedFiles.length; i++) {
                                var blockedFile = blockedFiles[i];
                                if (value.includes(blockedFile)) {
                                    if (loadedFiles.has(blockedFile)) {
                                        blockedCount[blockedFile]++;
                                        console.warn("BLOCKED CSS #" + blockedCount[blockedFile] + ":", blockedFile);
                                        return; // Block by not setting href
                                    } else {
                                        loadedFiles.add(blockedFile);
                                        console.log("ALLOWED first load of:", blockedFile);
                                    }
                                }
                            }
                        }
                        return originalSetAttribute.call(this, name, value);
                    };
                }
                return element;
            };
            
            // Also intercept appendChild to catch dynamically added links
            var originalAppendChild = Node.prototype.appendChild;
            Node.prototype.appendChild = function(child) {
                if (child && child.tagName === "LINK" && child.href) {
                    for (var i = 0; i < blockedFiles.length; i++) {
                        var blockedFile = blockedFiles[i];
                        if (child.href.includes(blockedFile)) {
                            if (loadedFiles.has(blockedFile)) {
                                blockedCount[blockedFile]++;
                                console.warn("BLOCKED appendChild CSS #" + blockedCount[blockedFile] + ":", blockedFile);
                                return child; // Return without appending
                            } else {
                                loadedFiles.add(blockedFile);
                                console.log("ALLOWED first appendChild of:", blockedFile);
                            }
                        }
                    }
                }
                return originalAppendChild.call(this, child);
            };
        })();
        </script>';
    }
}

// Block Meta Box from enqueueing duplicate TinyMCE assets
add_action( 'wp_print_styles', 'phila_remove_duplicate_tinymce_styles', 999 );

function phila_remove_duplicate_tinymce_styles() {
    // Only run in admin and ensure get_current_screen() is available
    if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
        return;
    }
    
    $screen = get_current_screen();
    if ( $screen && ( $screen->base === 'post' || $screen->base === 'post-new' ) ) {
        global $wp_styles;
        
        // Track which TinyMCE related styles we've seen
        static $tinymce_styles_seen = array();
        
        foreach( $wp_styles->queue as $key => $style ) {
            if ( strpos( $style, 'tinymce' ) !== false || 
                 strpos( $style, 'dashicons' ) !== false ||
                 strpos( $style, 'editor' ) !== false ) {
                
                if ( in_array( $style, $tinymce_styles_seen ) ) {
                    // Remove duplicate
                    unset( $wp_styles->queue[$key] );
                    error_log( 'Removed duplicate style: ' . $style );
                } else {
                    $tinymce_styles_seen[] = $style;
                }
            }
        }
    }
}

// Legacy function - keeping for backward compatibility but no longer hooked
function custom_after_wp_tiny_mce() {
    // This function is no longer used but kept for compatibility
}

/**
 * Add in a core button that's disabled by default
 */
add_filter( 'mce_buttons_2', 'phila_mce_2_addons' );

function phila_mce_2_addons( $buttons ) {
  $buttons[] = 'superscript';
  $buttons[] = 'subscript';

  return $buttons;
}

// PERFORMANCE OPTIMIZATION: Defer heavy plugin assets that are slowing page load
add_action( 'admin_enqueue_scripts', 'phila_defer_heavy_assets', 999 );

function phila_defer_heavy_assets() {
    if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
        return;
    }
    
    $screen = get_current_screen();
    if ( $screen && ( $screen->base === 'post' || $screen->base === 'post-new' ) ) {
        global $wp_scripts, $wp_styles;
        
        // Defer heavy scripts identified from HAR analysis
        $defer_scripts = array(
            'jquery-validation',
            'thickbox',
            'media-upload',
            'wpfront-user-role-editor'
        );
        
        foreach ( $defer_scripts as $script ) {
            if ( isset( $wp_scripts->registered[$script] ) ) {
                $wp_scripts->registered[$script]->extra['defer'] = true;
            }
        }
        
        // Remove non-essential CSS for post edit pages
        $remove_styles = array(
            'thickbox',
            'media-views',
            'imgareaselect'
        );
        
        foreach ( $remove_styles as $style ) {
            wp_dequeue_style( $style );
        }
    }
}

// PERFORMANCE: Optimize TinyMCE loading priority
add_filter( 'tiny_mce_before_init', 'phila_optimize_tinymce_performance', 10, 2 );

function phila_optimize_tinymce_performance( $mceInit, $editor_id ) {
    // Disable non-essential TinyMCE features that cause delay
    $mceInit['cleanup'] = false;
    $mceInit['verify_html'] = false;
    $mceInit['gecko_spellcheck'] = false;
    $mceInit['browser_spellcheck'] = false;
    
    // Optimize loading
    $mceInit['convert_urls'] = false;
    $mceInit['relative_urls'] = false;
    $mceInit['remove_script_host'] = false;
    
    return $mceInit;
}

// HEARTBEAT OPTIMIZATION: Reduce admin-ajax requests that contribute to slow page loads
add_action( 'admin_enqueue_scripts', 'phila_optimize_heartbeat_performance' );

function phila_optimize_heartbeat_performance() {
    if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
        return;
    }
    
    $screen = get_current_screen();
    if ( $screen && ( $screen->base === 'post' || $screen->base === 'post-new' ) ) {
        
        // Slow down heartbeat on post edit pages to reduce server load
        wp_enqueue_script( 'heartbeat' );
        wp_localize_script( 'heartbeat', 'heartbeatSettings', array(
            'interval' => 60,        // Increase from 15 to 60 seconds
            'suspension' => 'focus'  // Suspend when page loses focus
        ));
        
        // Log heartbeat optimization for monitoring
        error_log( 'Heartbeat optimized: 60s interval set for post edit page' );
    }
}

// Filter heartbeat received data to optimize response size
add_filter( 'heartbeat_received', 'phila_optimize_heartbeat_response', 10, 2 );

function phila_optimize_heartbeat_response( $response, $data ) {
    // Only send essential data to reduce response size
    if ( isset( $data['wp-refresh-post-lock'] ) ) {
        // Keep post lock data as it's essential
        $response['wp-refresh-post-lock'] = $data['wp-refresh-post-lock'];
    }
    
    if ( isset( $data['wp-auth-check'] ) ) {
        // Keep auth check as it's essential
        $response['wp-auth-check'] = true;
    }
    
    // Remove non-essential data to reduce response size
    unset( $response['wp-refresh-post-nonces'] );
    unset( $response['heartbeat_interval'] );
    
    return $response;
}
