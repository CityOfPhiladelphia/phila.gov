<?php

if ( class_exists("Phila_Gov_Post" ) ){
  $phila_post = new Phila_Gov_Post();
}

class Phila_Gov_Post {


  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'register_meta_boxes_posts' ), 10 );

  }
  
  function register_meta_boxes_posts($meta_boxes){
    $meta_boxes[] = array(
      'title'    => 'Multiple authors',
      'pages'    => array( 'post' ),
      'context'  => 'after_title',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'post'),
        ),
      ),
      'fields'  => array(
        array(
          'id'  => 'phila_author',
          'type' => 'user',
          'multiple' => true,
          'field_type'  => 'select_advanced',
          'placeholder' => 'Select more authors',
          'desc'  => 'The author of this post will be listed first, with authors chosen here appearing after, in the order listed.'
      ),
    )
  );

  $meta_boxes[] = array(
      'title'    => 'Social media share pre-filled text',
      'pages'    => array( 'post' ),
      'context'  => 'after_title',
      'fields'  => array(
        array(
          'type' => 'textarea',
          'required'  => true,
          'id'  => 'phila_social_intent',
          'limit' => 256,
          'desc'  => 'Curate Tweet sharing text. Required. 256 character limit.  A link to this page will be automatically added. <br /> E.g.: Now through Sept. 25, #WelcomingWeek has free events citywide to support Philly being welcoming and inclusive',
        )
      ),
    );

    $meta_boxes[] = array(
      'title'    => 'Elevate to feature',
      'pages'    => array( 'post' ),
      'context'  => 'side',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '!=', 'action_guide'),
        ),
      ),
      'include' => array(
        'user_role'  => array( 'administrator', 'phila_master_homepage_editor', 'editor' ),
        'relation' => 'or',
      ),
      'fields' => array(
        array(
          'name'  => 'Feature this item in the latest and on the homepage?',
          'id'    => 'phila_is_feature',
          'type'  => 'switch',
          'std'=> '0',
          'on_label'  => 'Yes',
          'off_label' => 'No',
        ),
      )
    );

    $meta_boxes[] = array (
      'title'    => 'Post options',
      'pages'    => array( 'post' ),
      'context'  => 'side',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '!=', 'action_guide'),
        ),
      ),
      'fields' => array(
        array(
          'name'  => 'Select the language of this post',
          'id'    => 'phila_select_language',
          'type'  => 'select',
          'options'         => array(
            'english'   => 'English', 
            'spanish'   => 'Spanish', 
            'french'    => 'French', 
            'chinese'   => 'Chinese (simplifed)',
            'vietnamese'    => 'Vietnamese',
            'russian'     => 'Russian',
            'arabic'    => 'Arabic'
          ),
        ),
        array(
          'name'  => 'Include last updated?',
          'id'    => 'is_last_updated',
          'type'  => 'switch',
          'std'=> '0',
          'on_label'  => 'Yes',
          'off_label' => 'No',
        ),
        array(
          'name'  => 'Last updated date',
          'id'    => 'last_updated_date',
          'type'  => 'date',
          'js_options'=> array(
            'dateFormat'  => 'mm/dd/yy',
            'maxDate'     => 0
          ),
          'visible' => array(
            'when' => array(
              array('is_last_updated', '=', '1'),
            ),
          ),
        ),
        array(
          'name'  => 'Last updated explaination',
          'id'    => 'last_updated_text',
          'type'  => 'textarea',
          'desc'  =>  'A short explanation of the changes.',
          'visible' => array(
            'when' => array(
              array('is_last_updated', '=', '1'),
            ),
          ),
        ),
      )
    );

    $meta_boxes[] = array(
      'title'    => 'End of post call to action. Where should users go now?',
      'pages'    => array( 'post' ),
      'context'  => 'normal',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'post'),
        ),
      ),
      'fields' => array(
        array(
          'id' => 'post_read_cta',
          'type' => 'group',
          'clone' => true,
          'sort'  => true,
          'max_clone' => 2,
          'desc'  => 'Blogs engage readers by asking them to take action after reading a post. Use this area to encourage your readers to do something next.',
          'fields' => array(
            array(
              'type'=> 'custom_html',
            ),
              Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('', 'phila_post_links'),
              array(
                'type'  => 'heading',
                'name'  => 'Link description',
              ),
          array(
            'id' => 'phila_link_desc',
            'type'  => 'textarea',
          ),
        )
      )
    )
  );

    /* Action guide specific metaboxes */

    $meta_boxes[] = array(
      'title'    => 'Get informed',
      'pages'    => array( 'post' ),
      'context'  => 'normal',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'action_guide'),
        ),
      ),
      'fields' => array(
        array(
          'id' => 'phila_action_get_informed',
          'type' => 'wysiwyg',
        )
      )
    );

    $meta_boxes[] = array(
      'title'    => 'Know the facts',
      'pages'    => array( 'post' ),
      'context'  => 'normal',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'action_guide'),
        ),
      ),
      'fields' => array(
        array(
          'id' => 'phila_action_facts',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg(),
          ),
        )
      )
    );

    $meta_boxes[] = array(
      'title' => 'Take action',
      'pages' => array('post'),
      'context' => 'normal',
      'priority'  => 'high',

      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'action_guide'),
        ),
      ),

      'fields' => array(
        array(
          'type'  => 'heading',
          'name' => 'Take action intro',
        ),
        array(
          'id'  => 'phila_action_intro',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        ),
        array(
          'id'  => 'phila_take_action',
          'type' => 'group',
          'clone'  => true,
          'max_clone' => 4,
          'sort_clone' => true,

          'fields' => array(
            array(
              'id'  => 'phila_select_action',
              'type'  => 'select',
              'std' => 'share',
              'options' => array(
                'share' => 'Share',
                'contact' => 'Contact',
                'give_back' => 'Give back',
                'attend'  => 'Attend'
              ),
            ),
              array(
                'id'  => 'phila_action_content',
                'type'  => 'wysiwyg',
                'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
              )
          ),
        ),
      ),
    );

    $meta_boxes[] = array(
      'title'    => 'Step 1',
      'pages'    => array( 'post' ),
      'context'  => 'normal',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'action_guide_v2'),
        ),
      ),
      'fields' => array(
        array(
          'name' => 'Step 1 Label',
          'id'   => 'step_1_label',
          'type' => 'text',
          'required' => true,
        ),
        array(
          'id' => 'step_1_content',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg(),
          ),
        )
      )
    );

    $meta_boxes[] = array(
      'title'    => 'Step 2',
      'pages'    => array( 'post' ),
      'context'  => 'normal',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'action_guide_v2'),
        ),
      ),
      'fields' => array(
        array(
          'name' => 'Step 2 Label',
          'id'   => 'step_2_label',
          'type' => 'text',
          'required' => true,
        ),
        array(
          'id' => 'step_2_content_before_steps',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg(),
          ),
        ),
        array(
          'id' => 'phila_stepped_content_step_2',
          'type' => 'group',
          'fields'  => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content(),
          )
        ),
        array(
          'id' => 'step_2_content_after_steps',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg(),
          ),
        )
      )
    );

    $meta_boxes[] = array(
      'title'    => 'Step 3',
      'pages'    => array( 'post' ),
      'context'  => 'normal',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'action_guide_v2'),
        ),
      ),
      'fields' => array(
        array(
          'name' => 'Step 3 Label',
          'id'   => 'step_3_label',
          'type' => 'text',
          'required' => true,
        ),
        array(
          'id' => 'step_3_content_before_steps',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg(),
          ),
        ),
        array(
          'id' => 'phila_stepped_content_step_3',
          'type' => 'group',
          'fields'  => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content(),
          )
        ),
        array(
          'id' => 'step_3_content_after_steps',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg(),
          ),
        )
      )
    );

    return $meta_boxes;
  }

}
