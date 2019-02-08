<?php 

add_filter( 'mb_settings_pages', 'phila_options_page' );

function phila_options_page( $settings_pages ) {
    $settings_pages[] = array(
        'id'          => 'phila-gov',
        'option_name' => 'phila_featured_jobs',
        'menu_title'  => 'Featured Jobs',
        'parent'      => 'options-general.php',
    );
    return $settings_pages;
}

add_filter( 'rwmb_meta_boxes', 'prefix_options_meta_boxes' );

function prefix_options_meta_boxes( $meta_boxes ) {
    $meta_boxes[] = array(
        'id'             => 'general',
        'title'          => 'General',
        'settings_pages' => 'phila-gov',
        'fields'         => array(
            array(
                'name' => 'Logo',
                'id'   => 'logo',
                'type' => 'file_input',
            ),
            array(
                'name'    => 'Layout',
                'id'      => 'layout',
                'type'    => 'image_select',
                'options' => array(
                    'sidebar-left'  => 'https://i.imgur.com/Y2sxQ2R.png',
                    'sidebar-right' => 'https://i.imgur.com/h7ONxhz.png',
                    'no-sidebar'    => 'https://i.imgur.com/m7oQKvk.png',
                ),
            ),
        ),
    );
    $meta_boxes[] = array(
        'id'             => 'colors',
        'title'          => 'Colors',
        'settings_pages' => 'phila-gov',
        'fields'         => array(
            array(
                'name' => 'Heading Color',
                'id'   => 'heading-color',
                'type' => 'color',
            ),
            array(
                'name' => 'Text Color',
                'id'   => 'text-color',
                'type' => 'color',
            ),
        ),
    );

    $meta_boxes[] = array(
        'id'             => 'info',
        'title'          => 'Theme Info',
        'context'        => 'side',
        'settings_pages' => 'phila-gov',
        'fields'         => array(
            array(
                'type' => 'custom_html',
                'std'  => 'A responsive theme for businesses and agencies.',
            )
        ),
    );
    return $meta_boxes;
}