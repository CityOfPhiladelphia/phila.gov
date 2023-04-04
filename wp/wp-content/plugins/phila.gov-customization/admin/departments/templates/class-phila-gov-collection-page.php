<?php
/* Collection page template */

if ( class_exists('Phila_Gov_Collection_Page' ) ){
  $phila_collection_page_load = new Phila_Gov_Collection_Page();
}

class Phila_Gov_Collection_Page {

  public function __construct(){

    add_action( 'rwmb_meta_boxes', array( $this, 'register_collection_page_metaboxes' ), 10, 1 );

  }

  function register_collection_page_metaboxes( $meta_boxes ){

    $meta_boxes[] = array(
      'id'       => 'phila_collection_page',
      'title'    => 'Collection page rows',
      'pages'    => array( 'department_page', 'programs' ),
      'context'  => 'normal',
      'revision' => true,
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'collection_page_v2'),
        ),
      ),

      'fields' => array(
        array(
          'id'    => 'collection_row',
          'type'  => 'group',
          'clone' => true,
          'sort_clone' => true,
          'add_button'  => '+ Add row',

          'fields' => array(
            array(
              'name' => 'Select row',
              'id'   => 'phila_collection_options',
              'desc'  => 'Choose a collection',
              'type' => 'select',
              'placeholder' => 'Select...',
              'options' => array(
                'phila_callout_v2' => 'Callout',
                'document' => 'Document pages [DEPRECATED]',
                'free_text' => 'Free text area',
                'paragraph_text_with_photo' => 'Paragraph text with photo',
                'post' => 'Posts',
                'press_releases' => 'Press releases',
                'program' => 'Program pages',
                'phila_resource_group' => 'Resource group',
                'service' => 'Service pages',
                'stepped_process' => 'Stepped process'
                ),
              ),
              array(
                'id'    => 'program_pages',
                'type'  => 'group',

                'visible' => array(
                  'when' => array(
                    array('phila_collection_options', '=', 'program'),
                  ),
                ),

              'fields'  =>
                Phila_Gov_Standard_Metaboxes::phila_program_page_selector( $multiple = true ),

            ),
            array(
              'id'    => 'service_pages',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'service'),
                ),
              ),
              'fields'  => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('1/4 Heading', 'phila_custom_text_title'),
                Phila_Gov_Standard_Metaboxes::phila_v2_service_page_selector( $multiple = true ),
              )
            ),
            array(
              'id'    => 'posts',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'post'),
                ),
              ),
              'fields'  => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_wysiwyg_title( $name = '1/4 heading'),
                Phila_Gov_Standard_Metaboxes::phila_post_selector( $multiple = true ),
                array(
                  'id' => 'phila_v2_posts_link',
                  'title' => 'See all posts',
                  'name'  => 'See all posts url',
                  'placeholder' => 'E.g. https://phila.gov/departments/department-of-commerce/all-programs/',
                  'type'  => 'url',
                  'class' => 'metabox-url',
                ),
              )
            ),
            array(
              'id'  => 'press_releases',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'press_releases'),
                ),
              ),
              'fields'  => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_wysiwyg_title($name = '1/4 heading'),
                Phila_Gov_Standard_Metaboxes::phila_post_selector( $multiple = true ),
                array(
                  'id' => 'phila_v2_press_release_link',
                  'title' => 'See all press releases',
                  'name'  => 'URL',
                  'placeholder' => 'E.g. https://phila.gov/departments/department-of-commerce/all-programs/',
                  'type'  => 'url',
                  'class' => 'metabox-url',
                ),
              )
            ),
            array(
              'id'    => 'stepped_process',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'stepped_process'),
                ),
              ),
              'fields' => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_wysiwyg_title( $name = 'Stepped process title'),
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_advanced_small_wysiwyg(),
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content()
              )
            ),
            array(
              'id'    => 'paragraph_text_with_photo',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'paragraph_text_with_photo'),
                ),
              ),
              'fields'  => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_wysiwyg_title( $name = '1/4 heading'),
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_advanced_small_wysiwyg(),
              )
            ),
            array(
              'id'  => 'phila_resource_group',
              'type' => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'phila_resource_group'),
                ),
              ),
              'fields' => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_wysiwyg_title($name = '1/4 heading'),
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_advanced_small_wysiwyg(),
                Phila_Gov_Standard_Metaboxes::phila_resource_list_v2(),
              )
            ),
            array(
              'id'  => 'phila_callout_group',
              'type' => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'phila_callout_v2'),
                ),
              ),
              'fields' => array(
                array(
                  'name'  => 'Callout heading',
                  'id'    => 'phila_callout_heading',
                  'type'  => 'text',
                ),
                array(
                  'type'=> 'wysiwyg',
                  'name'  => 'Before callout copy',
                  'id'  => 'phila_before_callout_copy',
                  'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
                ),
                Phila_Gov_Standard_Metaboxes::phila_callout_v2(),
                array(
                  'type'=> 'wysiwyg',
                  'name'  => 'After callout copy',
                  'id'  => 'phila_after_callout_copy',
                  'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
                )
              )
            ),
            array(
              'id' => 'free_text',
              'type' => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'free_text'),
                ),
              ),
              'clone'  => true,
              'sort_clone' => true,
              'add_button'  => '+ Add section',
              'fields' => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg($section_name = '1/4 Heading'),
                array(
                  'id'  => 'expand_collapse',
                  'name'  => 'Add expand/collapse for long form content?',
                  'type'  => 'switch',
                  'on_label'  => 'Yes',
                  'off_label' => 'No',
                  'desc'  => 'Use this for content that is extremely long to shorten page length.'
                )
              )
            ),
            array(
              'id'    => 'document_pages',
              'type'  => 'group',
              'visible' => array(
                'when' => array(
                  array('phila_collection_options', '=', 'document'),
                ),
              ),
              'fields'  => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('1/4 Heading', 'phila_custom_text_title'),
                array(
                  'id'    => 'document_page_group',
                  'type'  => 'group',
                  'clone' => true,
                  'sort_clone'  => true,
                  'add_button'  => '+ Add document group',
                  'fields' => array(
                    Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg($section_name = 'Document group title'),
                    Phila_Gov_Standard_Metaboxes::phila_metabox_v2_document_page_selector(),
                  )
                ),

              )
            )
          ),
        ),
      )
    );

    return $meta_boxes;
  }

}