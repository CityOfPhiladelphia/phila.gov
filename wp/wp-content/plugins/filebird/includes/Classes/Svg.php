<?php
namespace FileBird\Classes;

use enshrined\svgSanitize\Sanitizer;

class Svg {
    public function __construct() {
        if( get_option( 'njt_fbv_allow_svg_upload' ) !== '1' ) {
            return;
        }
        add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
        add_filter( 'wp_check_filetype_and_ext', array( $this, 'wp_check_filetype_and_ext' ), 10, 4 );
        add_filter( 'wp_handle_upload_prefilter', array( $this, 'wp_handle_upload_prefilter' ) );
    }

    public function upload_mimes( $mimes ) {
        $mimes['svg']  = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
    
        return $mimes;
    }

    public function wp_check_filetype_and_ext( $data, $file, $filename, $mimes ) {
        global $wp_version;
        if ( $wp_version !== '4.7.1' ) {
            return $data;
        }
    
        $filetype = wp_check_filetype( $filename, $mimes );
    
        return [
            'ext'             => $filetype['ext'],
            'type'            => $filetype['type'],
            'proper_filename' => $data['proper_filename']
        ];
    }
    public function wp_handle_upload_prefilter( $file ) {
        if ( ! isset( $file['tmp_name'] ) ) {
            return $file;
        }
    
        $file_name   = isset( $file['name'] ) ? $file['name'] : '';
        $wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file_name );
        $type        = ! empty( $wp_filetype['type'] ) ? $wp_filetype['type'] : '';
    
        if( 'image/svg+xml' !== $type ) {
            return $file;
        }
        
        $sanitizer = new Sanitizer();
        $dirtySVG = file_get_contents( $file['tmp_name'] );
        $cleanSVG = $sanitizer->sanitize( $dirtySVG );
    
        if ( $cleanSVG ) {
            file_put_contents( $file['tmp_name'], $cleanSVG );
        } else {
            $file['error'] = __( 'This file couldn\'t be uploaded.', 'filebird' );
        }
    
        return $file;
    }
}
