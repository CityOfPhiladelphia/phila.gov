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
      'title'    => 'Additional authors',
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
          //'hidden' => true,
          'field_type'  => 'select_advanced',
          'placeholder' => 'Select more authors',
          'desc'  => 'The author of this post will be listed first, with authors chosen here appearing after, in the order listed.'
      ),
        array(
          'id'  => 'phila_exclude_author',
          'type'  => 'checkbox',
          'name'  => 'Exclude primary author from list?'
        ),
        array(
          'id'  => 'phila_author_group',
          'type' => 'group',
          'desc'  => "Use this feature to manually enter an author's name or display multiple authors.",
          'clone'  => true,
          'sort_clone' => true,
          'add_button' => '+ Add another author',
          'fields' => array(
            array(
              'name' => 'Author name',
              'id'   => 'phila_additional_author',
              'type' => 'text',
              'desc'  => "Enter the author's full name. For example: William Penn",
              'size' => 50
            ),
          )
        )
    )
  );

  $meta_boxes[] = array(
      'title'    => 'Social media share pre-filled text',
      'pages'    => array( 'post' ),
      'context'  => 'after_title',
      'fields'  => array(
        array(
          'type' => 'textarea',
          'required'  => false,
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
          array('phila_template_select', '=', 'post'),
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
      'title'    => 'Language options',
      'pages'    => array( 'post' ),
      'context'  => 'side',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '!=', 'action_guide'),
        ),
      ),
      'fields' => array(
        Phila_Gov_Standard_Metaboxes::phila_language_selector()
      )
    );

    $meta_boxes[] = array (
      'title'    => 'Updates and archiving',
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
          'name'  => 'Add last updated component?',
          'id'    => 'is_last_updated',
          'desc'  =>  'Use this option to add an editorial note and describe important changes to the post.',
          'type'  => 'switch',
          'std'=> '0',
          'on_label'  => 'Yes',
          'off_label' => 'No',
        ),
        array(
          'name'  => 'Date for last updated/Archived date',
          'id'    => 'last_updated_date',
          'type'  => 'date',
          'required' => true,
          'js_options'=> array(
            'dateFormat'  => 'mm/dd/yy',
            'maxDate'     => 0
          ),
          'visible' => array(
                'when' => array(
                    array('is_last_updated', '=', '1'),
                    array('phila_archive_post', '=', 'archive_now'),
                ),
                'relation' => 'or', 
            ),
        ),
        array(
          'name'  => 'Note for last updated',
          'id'    => 'last_updated_text',
          'type'  => 'textarea',
          'visible' => array(
            'when' => array(
              array('is_last_updated', '=', '1'),
            ),
          ),
        ),
        array(
          'name'    => 'Post archiving options',
          'id'      => 'phila_archive_post',
          'type'    => 'radio',
          'inline'  => false,
          'desc'  =>  'Posts are marked "Archived" two years after publication.',
          'visible' => array(
            'when' => array(
              array('phila_template_select', '=', 'post'),
            ),
          ),
          'options' =>  array(
            'archive_now'     => 'Archive this post now',
            'do_not_archive'  => 'Never archive this post',
            'default'         => 'Follow archive schedule (Default)',
          ),
          'admin_columns' => array(
            'position' => 'after date',
            'title'    => __( 'Archive' ),
            'sort'     => true,
          ),
          'std'     => 'default'
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
      'title'    => 'Page content',
      'pages'    => array( 'post' ),
      'priority' => 'high',
      'revision' => true,
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'action_guide_2'),
        ),
      ),
      'fields' => array(
        array(
          'id' => 'phila_tabbed_content',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'add_button'  => '+ Add tab',
          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Tab label', 'tab_label', true, 'Navigation text for tabbed content'),
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Tab icon', 'tab_icon', true, 'Example: fas fa-icon-name. You can find icons on <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Fontawesome.io</a>.'),
            Phila_Gov_Row_Metaboxes::phila_tabbed_metabox_grid_row(),
          ),
        )
      )
    );

    $meta_boxes[] = array(
      'title'    => 'Full width call to action',
      'pages'    => array( 'post' ),
      'context'  => 'normal',
      'priority' => 'high',
      'visible' => array(
        'when' => array(
          array('phila_template_select', '=', 'action_guide_2'),
        ),
      ),
      'fields' => array(
        array(
          'id'       => 'phila_v2_cta_full',
          'context'  => 'normal',
          'priority' => 'default',
          'type'  => 'group',
          'clone' => false,
          'fields' =>
            Phila_Gov_Standard_Metaboxes::phila_meta_var_full_width_cta()
        ),
      ),
    );

    return $meta_boxes;
  }

}
