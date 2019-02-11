<?php
/**
 * Register templates for use on the front-end
 *
 */

if ( class_exists( "Phila_Gov_Register_Service_Templates" ) ){
  $program_templates = new Phila_Gov_Register_Service_Templates();
}

class Phila_Gov_Register_Service_Templates {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array( $this, 'register_template_selection_metabox_services'), 10, 1 );

  }

  function register_template_selection_metabox_services( $meta_boxes ){

    $meta_boxes[] = array(
      'title' => 'Before you start',
      'pages' => array('service_page'),
      'revision' => true,
      'priority'  => 'high',
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'start_process' ),
          array( 'phila_template_select', '=', 'default_v2' ),
        ),
        'relation' => 'or',
      ),
      'fields' => array(
        array(
          'id' => 'phila_start_process',
          'type'  => 'group',
          'clone' => false,
    
          'fields' => array(
            array(
              'id'  => 'phila_wysiwyg_process_content',
              'type'  => 'wysiwyg',
              'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
            ),
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('Button details', 'phila_start_button'),
          )
        )
      )
    );
    

    $meta_boxes[] = array(
      'id'       => 'service_questions',
      'title'    => 'Default service content',
      'pages' => array( 'service_page' ),
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'default_v2' ),
        ),
        'relation'  => 'or'
      ),
      'fields' => array(
        array(
          'type' => 'heading',
          'name'  => 'Who is this service for?',
        ),
        array(
          'id' => 'service_who',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'type' => 'heading',
          'name'  => 'What are the requirements for this service?',
        ),
        array(
          'id' => 'service_requirements',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'type' => 'heading',
          'name'  => 'Where is this located and when is it availble?',
        ),
        array(
          'id' => 'service_where_when',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'name'  => 'Include contact information?',
          'id'  => 'service_where_when_address_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'id' => 'service_where_when_std_address',
          'type' => 'group',
          'hidden' => array('phila_address_select', false),

          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields(),
            array(
              'id' => 'phila_connect_general',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'type' => 'heading',
                  'name' => 'Email, fax, etc.',
                ),
                array(
                  'name' => 'Email',
                  'id'   => 'phila_connect_email',
                  'type' => 'email',
                  'desc' => 'example@phila.gov',
                ),
                array(
                  'name' => 'Explanation text for email',
                  'id'   => 'phila_connect_email_exp',
                  'type' => 'text',
                  'desc' => 'Ex. For press inquiries contact:',
                ),
                array(
                  'name' => 'Fax',
                  'id'   => 'phila_connect_fax',
                  'type' => 'phone',
                  'desc' => '(###)-###-####',
                ),
                array(
                  'id' => 'phila_connect_social',
                  'type' => 'group',
                  'fields' => array(
                    array(
                      'type' => 'heading',
                      'name' => 'Social',
                    ),
                    array(
                      'name' => 'Facebook URL',
                      'id'   => 'phila_connect_social_facebook',
                      'type' => 'url',
                      'desc' => 'Example: https://www.facebook.com/PhiladelphiaCityGovernment/',
                    ),
                    array(
                      'name' => 'Twitter URL',
                      'id'   => 'phila_connect_social_twitter',
                      'type' => 'url',
                      'desc' => 'Example: https://twitter.com/PhiladelphiaGov'
                    ),
                    array(
                      'name' => 'Instagram URL',
                      'id'   => 'phila_connect_social_instagram',
                      'type' => 'url',
                      'desc' => 'Example: https://www.instagram.com/cityofphiladelphia/'
                    ),
                    array(
                      'name' => 'YouTube URL',
                      'id'   => 'phila_connect_social_youtube',
                      'type' => 'url',
                      'desc' => 'Example: https://www.youtube.com/user/philly311center'
                    ),
                    array(
                      'name' => 'Flickr URL',
                      'id'   => 'phila_connect_social_flickr',
                      'type' => 'url',
                      'desc' => 'Example: https://www.flickr.com/photos/philly_cityrep/'
                    ),
                  ),
                )
              )
            )
          ),
      ),
        array(
          'type' => 'heading',
          'name'  => 'Are there any costs associated with this service?',
        ),
        array(
          'id' => 'service_cost',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'type' => 'heading',
          'name'  => 'How can someone get this service?',
        ),
        array(
          'id' => 'service_how',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'name'  => 'Add stepped content?',
          'id'  => 'service_how_stepped_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'id' => 'phila_stepped_content',
          'type' => 'group',
          'visible' => array('service_how_stepped_select', true),
          'fields'  => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content(),
          )
        ),
        array(
          'type' => 'heading',
          'name'  => 'Are there renewal requirements for this service?',
        ),
        array(
          'id' => 'service_renewal_requirements',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
      )
    );
    return $meta_boxes;
  }

}
