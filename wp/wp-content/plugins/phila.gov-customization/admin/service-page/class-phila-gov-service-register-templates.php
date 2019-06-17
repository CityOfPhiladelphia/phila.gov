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
      'class' => 'hide-on-load',
      'priority'  => 'high',
      'visible' => array(
        'when' => array(
          array( 'phila_template_select', '=', 'start_process' ),
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
    'title' => 'Before you begin',
    'pages' => array('service_page'),
    'revision' => true,
    'class' => 'hide-on-load',
    'priority'  => 'high',
    'visible' => array(
      'when' => array(
        array( 'phila_template_select', '=', 'default_v2' ),
      ),
      'relation' => 'or',
    ),
    'fields' => array(
      array(
        'id'  => 'service_before_you_begin',
        'type'  => 'wysiwyg',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
      ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('Button details', 'phila_start_button'),
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
          'name'  => 'Add prerequisite approvals?',
          'id'  => 'service_accordion_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No',
          'columns'   => '6'
          ),
        array(
          'id' => 'accordion_row',
          'type' => 'group',
          'visible' => array(
            'when' => array(
              array('service_accordion_select', '=', true),
            ),
          ),
          'fields' => array(
            array(
              'name' => ('Prerequisite row title'),
              'id'   => 'accordion_row_title',
              'type' => 'text',
              'required' => true,
              'class' => 'percent-100'
            ),
            array(
              'id'   => 'accordion_group',
              'type' => 'group',
              'clone'  => true,
              'sort_clone' => true,
              'add_button' => '+ Add accordion',
              'fields' => array(
                Phila_Gov_Standard_Metaboxes::phila_metabox_double_wysiwyg($section_name = 'Accordion title', $wysiwyg_desc = 'Accordion content', $columns = 12, $clone = true),
              )
            )
          ),
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
          'hidden' => array('service_where_when_address_select', false),

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
          'name'  => 'Add cost callout?',
          'id'  => 'service_cost_callout_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'id' => 'service_cost_callout',
          'type' => 'group',
          'visible' => array('service_cost_callout_select', true),
          'fields'  => array(
            array(
              'id'  => 'cost_callout',
              'type'  => 'group',
              'clone' => true,
              'max_clone' => 3,
              'fields'  => array(
                array(
                  'type' => 'text',
                  'id' => 'heading',
                  'name' => 'Cost type',
                  'desc'  => 'E.g. License cost'
                ),
                array(
                  'type' => 'number',
                  'id' => 'amount',
                  'name' => 'Cost amount, in dollars',
                  'desc'  => 'E.g. 20.00'
                ),
                array(
                  'id' => 'description',
                  'type'  => 'wysiwyg',
                  'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
                ),
              )
            )
          )
        ),
        array(
          'id' => 'service_cost',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          //Name/id doesn't match because reqirements we not defined before this made it to production
          'name'  => 'Add modal information?',
          'id'  => 'service_payment_info_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'visible' => array('service_payment_info_select', true),
          'id'  => 'service_modal_info_link_text',
          'name'  => 'Clickable link text',
          'type'  => 'text',
        ),
        array(
          'visible' => array('service_payment_info_select', true),
          'id' => 'service_payment_info',
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
          'name'  => 'Use this section for introduction content. Not required.',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'name'  => 'Add single stepped content?',
          'id'  => 'service_how_stepped_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No',
          'columns'   => '6'
        ),
        array(
          'name'  => 'Add mutiple stepped content groups?',
          'id'  => 'service_how_stepped_select_multi',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No',
          'columns'   => '6'
        ),
        array(
          'id' => 'service_how_stepped_content',
          'type' => 'group',
          'visible' => array('service_how_stepped_select', true),
          'fields'  => array(
            array(
              'id' => 'service_how_stepped_content_intro',
              'type'  => 'wysiwyg',
              'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
            ),
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content(),
          )
        ),
        array(
          'id' => 'service_how_stepped_content_multi',
          'type' => 'group',
          'clone' => true,
          'add_button'  => '+ Add a step group',
          'visible' => array('service_how_stepped_select_multi', true),
          'fields'  => array(
            array(
              'id' => 'service_how_stepped_content_intro',
              'type'  => 'text',
              'name' => 'Step group heading',
              'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
            ),
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content(),
          )
        ),
        array(
          'id' => 'service_how_ending_content',
          'type'  => 'wysiwyg',
          'visible' => array(
            'when' => array(
              array('service_how_stepped_select', true ),
              array('service_how_stepped_select_multi', true),
            ),
            'relation'  => 'or'
          ),
          'name'  => 'Display more content after all steps.',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
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
    //v2 service related content, simplifies groupins to make compatible with gather content importer
    $meta_boxes[] = array(
      'id'       => 'service_related',
      'title'    => 'Addtional content',
      'pages' => array( 'service_page' ),
      'visible' => array(
        'when' => array(
            array( 'phila_template_select', '=', 'default_v2' ),
          ),
        'relation'  => 'or'
      ),
      'fields' => array(
        array(
          'name'  => 'Forms & Instructions',
          'type'  => 'heading'
        ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_document_page_selector(),
        array(
          'name'  => 'Related Content',
          'type'  => 'heading'
        ),
        array(
          'id'  => 'service_related_content_picker',
          'type'  => 'post',
          'post_type' => array('department_page', 'programs', 'post', 'service_page', 'document'),
          'placeholder' => 'Select pages',
          'query_args'  => array(
              'post_status'    => 'any',
              'posts_per_page' => - 1,
            ),
          'multiple'  => true,
        ),
        array(
          'id'  => 'service_related_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        ),
        array(
          'name'  => 'Did You Know?',
          'type'  => 'heading'
        ),
        array(
          'id'  => 'service_did_you_know_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        ),

        array(
          'name'  => 'Questions about this content?',
          'type'  => 'heading'
        ),
        array(
          'id'  => 'service_questions_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        ),
        array(
          'name'  => 'Disclaimer',
          'type'  => 'heading'
        ),
        array(
          'id'  => 'service_disclaimer_content',
          'type'  => 'wysiwyg',
          'desc'  => 'Enter disclaimer content, or a [text block] with disclaimer shortcode',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        ),
      )
    );
    return $meta_boxes;
  }

}
