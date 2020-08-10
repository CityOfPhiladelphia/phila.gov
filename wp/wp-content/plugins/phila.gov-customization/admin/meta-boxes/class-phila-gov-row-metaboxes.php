<?php
/* Group Metabox row options */

if ( class_exists('Phila_Gov_Row_Metaboxes' ) ){
  $phila_standard_metaboxes_load = new Phila_Gov_Row_Metaboxes();
}

class Phila_Gov_Row_Metaboxes {

  public static function phila_metabox_grid_row (){
    return array(
    'id'    => 'phila_row',
    'class' => 'phila_row',
    'type'  => 'group',
    'clone' => true,
    'sort_clone' => true,
    'add_button'  => '+ Add row',
    'fields' => array(
      Phila_Gov_Row_Select_Options::phila_metabox_grid_options(),
      Phila_Gov_Row_Metaboxes::phila_metabox_full_options(),
      Phila_Gov_Row_Select_Options::phila_metabox_thirds_options(),
      Phila_Gov_Row_Select_Options::phila_metabox_half_options(),
      ),
    );
  }

  public static function phila_tabbed_metabox_grid_row (){
    return array(
    'id'    => 'phila_row',
    'class' => 'phila_row',
    'type'  => 'group',
    'clone' => true,
    'sort_clone' => true,
    'add_button'  => '+ Add row',
    'fields' => array(
      Phila_Gov_Row_Metaboxes::phila_metabox_tabbed_options(),
      ),
    );
  }


  public static function phila_metabox_tabbed_options( ){
    return array(
    'name' => 'Tabbed options',
    'id'   => 'phila_tabbed_options',
    'type' => 'group',
    'fields' => array(
      Phila_Gov_Row_Select_Options::phila_metabox_tabbed_select(),
      array(
        'id' => 'phila_vue_template',
        'type'  => 'group',
        'visible' => array('phila_tabbed_select', '=', 'phila_metabox_tabbed_single_title'),
        'fields' => Phila_Gov_Row_Select_Options::phila_metabox_tabbed_single_title()
      ),
      array(
        'id' => 'phila_vue_template',
        'type'  => 'group',
        'visible' => array('phila_tabbed_select', '=', 'phila_metabox_tabbed_single_wysiwyg'),
        'fields' => Phila_Gov_Row_Select_Options::phila_metabox_tabbed_single_wysiwyg()
      ),
      array(
        'id' => 'phila_vue_template',
        'type'  => 'group',
        'visible' => array('phila_tabbed_select', '=', 'phila_metabox_tabbed_repeater_wysiwyg'),
        'fields' => Phila_Gov_Row_Select_Options::phila_metabox_tabbed_repeater_wysiwyg()
      ),
      array(
        'id' => 'phila_vue_template',
        'type'  => 'group',
        'visible' => array('phila_tabbed_select', '=', 'phila_metabox_tabbed_stepped_content'),
        'fields' => Phila_Gov_Row_Select_Options::phila_metabox_tabbed_stepped_content()
      ),
    ),
  );
}

  public static function phila_metabox_full_options( ){
    return array(
    'name' => '',
    'id'   => 'phila_full_options',
    'type' => 'group',
    'visible' => array(
      'phila_grid_options',
      '=',
      'phila_grid_options_full'
    ),
    'fields' => array(
      Phila_Gov_Row_Select_Options::phila_metabox_full_options_select(),
      array(
        'visible' => array('phila_full_options_select', '=', 'phila_blog_posts'),
        'id'  => 'phila_get_post_cats',
        'type' => 'group',
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select new owner', 'phila_post_category', 'Display posts from these owners.' ),
          array(
            'name'  => 'Filter by a tag',
            'id'  => 'tag',
            'type' => 'taxonomy_advanced',
            'taxonomy'  => 'post_tag',
            'field_type' => 'select_advanced',
            'desc'  => 'Display posts using this tag. "See all" will pre-filter on these terms.'
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_url('See all link override', 'override_url', '', 12 ),
        ),
      ),
      array(
        'id' => 'phila_full_width_calendar',
        'type' => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_full_width_calendar'),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_calendar_full(),
      ),
      array(
        'id'   => 'phila_callout',
        'type' => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_callout'),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_callout(),
      ),
      array(
        'id'   => 'phila_custom_text',
        'type' => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_custom_text'),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
      ),
      array(
        'id'   => 'phila_custom_text_multi_full',
        'type' => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_custom_text_multi'),
        'revision'  => true,
        'fields' =>   Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea_multi(),
      ),
      array(
        'id'  => 'phila_call_to_action_multi',
        'type' => 'group',
        'visible' => array(
          'when' => array(
            array('phila_full_options_select', '=', 'phila_get_involved'),
            array('phila_full_options_select', '=', 'phila_resource_list'),
          ),
          'relation' => 'or',
        ),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_call_to_action_multi(),
      ),
      array(
      'id' => 'phila_full_width_cta',
      'type' => 'group',
      'visible' => array('phila_full_options_select', '=', 'phila_full_cta'),
      'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_full_width_cta()
      ),
      array(
      'id'   => 'phila_list_items',
      'type' => 'group',
      'visible' => array('phila_full_options_select', '=', 'phila_list_items'),
      'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_list_items(),
      ),
      array(
        'id'       => 'phila_image_list',
        'type'     => 'group',
        'visible'  => array('phila_full_options_select', '=', 'phila_image_list'),
        'fields'   => array(
          array(
            'name'  => 'Image list heading (optional)',
            'id'    => 'title',
            'type'  => 'text',
          ),
          array(
            'name'  => 'Image list subheading (optional)',
            'id'    => 'sub_title',
            'type'  => 'text',
          ),
          array(
            'name'  => 'List of images',
            'id'    => 'phila_image_list',
            'type'  => 'image_advanced'
          ),
          array(
            'id'       => 'phila_image_list_extended',
            'type'     => 'group',
            'clone'    => true,
            'max_clone' => 2,
            'add_button'  => '+ Add secondary list',
            'fields'   => array(
              array(
                'name'  => 'Secondary list heading',
                'id'    => 'secondary_title',
                'type'  => 'text',
              ),
              array(
                'name'  => 'Secondary list content',
                'id'    => 'secondary_list_content',
                'type'  => 'wysiwyg',
                'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
              ),
            ),
          ),
        ),
      ),
      array(
        'id'  => 'full_width_press_releases',
        'type' => 'group',
        'visible'  => array('phila_full_options_select', '=', 'phila_full_width_press_releases'),
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select owners', 'phila_press_release_category', 'Display press releases from these owners.' ),
          array(
            'name'  => 'Filter by a tag',
            'id'  => 'tag',
            'type' => 'taxonomy_advanced',
            'taxonomy'  => 'post_tag',
            'field_type' => 'select_advanced',
            'desc'  => 'Display press releases using this tag. "See all" will pre-filter on these terms.'
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_url('See all link override', 'override_url', '', 12 ),
        ),
      ),
      array(
        'id'      => 'phila_registration',
        'type'    => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_registration'),
        'fields'  => Phila_Gov_Standard_Metaboxes::phila_meta_registration(),
      ),
      array(
        'id'       => 'phila_staff_directory_listing',
        'title'    => 'Staff Directory Listing',
        'context'  => 'normal',
        'priority' => 'default',
        'type'    => 'group',
    
        'visible' => array(
          'when' => array(
            array( 'phila_full_options_select', '=', 'phila_staff_table'),
          ),
        ),
    
        'fields' => array(
          array(
            'id'   => 'phila_staff_directory_selected',
            'name'  => 'Display a staff directory list?',
            'type' => 'switch',
            'on_label'  => 'Yes',
            'off_label' => 'No',
            'after' => '<p class="description">Enter at least one staff member in the <a href="/wp-admin/edit.php?post_type=staff_directory">Staff Members</a> section.</p>',
          ),
          array(
            'id'  => 'phila_get_staff_cats',
            'type' => 'group',
            'visible' => array(
              'when' => array(
                array( 'phila_staff_directory_selected', '=', 1 ),
              ),
            ),
            'fields' => array(
              Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select new owner', 'phila_staff_category', 'Display staff members from these owners. This will override page ownership selection entirely.' ),
            ),
          ),
          array(
            'id'  => 'hide_units',
            'class' => 'hide-on-load',
            'name'  => 'Hide staff assigned to units?',
            'desc'  => 'By selecting this option, staff assigned to a unit will not appear on this department homepage.',
            'type' => 'switch',
            'on_label'  => 'Yes',
            'off_label' => 'No',
            'visible' => array(
              'phila_template_select', 'in', ['homepage_v2']
            )
          ),
        ),
      ),
      array(
        'id' => 'phila_heading_groups',
        'type'  => 'group',
        'clone' => false,
        'visible' => array('phila_full_options_select', '=', 'phila_stepped_content'),

        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields_unique(),
        )
      ),
      array(
        'id' => 'phila_programs',
        'type'  => 'group',
        'clone' => false,
        'visible' => array('phila_full_options_select', '=', 'phila_programs'),
        'fields'  => array(
          Phila_Gov_Standard_Metaboxes::phila_program_page_selector($multiple = true)
        )
      ),
      array(
        'id'  => 'phila_location_list',
        'type'  => 'group',
        'clone' => false,
        'visible' => array('phila_full_options_select', '=', 'phila_location_list'),
        'fields'  => array(
          array(
            'name'  => 'Row title',
            'type'  => 'text',
            'id'  =>  'row_title'
          ),
          array(
            'id'  => 'group',
            'type'  => 'group',
            'clone' => 'true',
            'add_button' => '+ Add another group',
            'fields'  => array(
              array(
                'id'  => 'group_title',
                'name' => 'Group title',
                'type'  => 'text'
              ),
              array(
                'name' => 'Location list',
                'id' => 'location_list',
                'type' => 'post',
                'post_type' => 'programs',
                'multiple'  => true,
                'field_type'  => 'select_advanced',
                'query_args'  => array(
                  'post_status'    => 'any',
                  'posts_per_page' => - 1,
                ),
              )
            ),
          )
        ),
      ),
      array(
        'id'  => 'commission_members',
        'type'  => 'group',
        'clone' => false,
        'visible' => array('phila_full_options_select', '=', 'phila_board_commission'),
        'fields'  => array(
          array(
            'id'  => 'section_title',
            'name'  => 'Section title',
            'type'  => 'text',
            'desc'  => 'Use this section to create an accordion-style list of people who don\'t formally work for the City of Philadelphia. List will appear in the order below.',
          ),
          array(
            'id'  => 'table_head_title',
            'name'  => 'Rename table title cell',
            'type'  => 'text',
            'desc'  => 'The staff table column label defaults to "title". Use this to change it.'
          ),
          Phila_Gov_Standard_Metaboxes::phila_meta_var_commission_members()
        ),
      ),

      array (
        'visible' => array('phila_full_options_select', '=', 'phila_vue_app'),
        'id'  => 'vue_app_title',
        'name'  => 'Optional title',
        'type'=> 'text'
      ),
      array(
        'id' => 'phila_vue_template',
        'type'  => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_vue_app'),
        'fields' => Phila_Vue_App_Files::phila_vue_metaboxes()
      ),
      array(
        'id' => 'photo_callout',
        'type'  => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_photo_callout'),
        'fields' => Phila_Gov_Row_Metaboxes::phila_metabox_photo_callout()
      ),
      array(
        'id' => 'faq',
        'type'  => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_faq'),
        'fields' => Phila_Gov_Row_Metaboxes::phila_metabox_faq()
      ),
      array(
        'id' => 'phila_content_heading_group',
        'type'  => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_content_heading_group'),
        'fields' => Phila_Gov_Row_Metaboxes::phila_metabox_heading_group()
      ),
      array(
        'id' => 'phila_prereq',
        'type'  => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_prereq'),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_prereq_row('Prerequisite row title')
      ),
      array(
        'id' => 'phila_content_additional_content',
        'type'  => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_content_additional_content'),
        'fields' =>   Phila_Gov_Standard_Metaboxes::phila_meta_var_addtional_content()
      ),
      array(
        'id' => 'phila_service_update_page',
        'type'  => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_service_updates'),
        'fields'  => Phila_Gov_Standard_Metaboxes::phila_get_service_updates(),
      ),
      array(
        'id' => 'phila_timeline_picker',
        'type'  => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_homepage_timeline'),
        'fields'  => array(
            Phila_Gov_Standard_Metaboxes::phila_timeline_page_selector(),
            array(
              'name' => 'Timeline item count',
              'id'   => 'homepage_timeline_item_count',
              'desc'  => 'Select the number of items from the timeline to display',
              'type' => 'number'
          ),
        )
      ),
    ),
  );
}

  /**
  *
  * Begin 2/3 x 1/3 Column MetaBox content
  *
  **/

  // 2/3 x 1/3: Column 1 Options
  public static function phila_metabox_thirds_option_one( ){
    return array(
      'id' => 'phila_two_thirds_col',
      'type' => 'group',
      'fields' => array(
          array(
            'name' => 'Column 1 <br/><small>(2/3 width column)</small>',
            'id'   => 'phila_two_thirds_col_option',
            'desc'  => 'Choose to display recent blog posts or custom markup text.',
            'type' => 'select',
            'placeholder' => 'Select...',
            'options' => array(
              'phila_custom_text' => 'Custom Text',
              'phila_custom_text_multi' => 'Custom Text (multi)',
              ),
          ),
          array(
            'id'   => 'phila_custom_text',
            'type' => 'group',
            'visible' => array('phila_two_thirds_col_option', '=', 'phila_custom_text'),
            'revision' => true,
            'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
          ),
          array(
            'id'   => 'phila_custom_text_multi',
            'type' => 'group',
            'visible' => array('phila_two_thirds_col_option', '=', 'phila_custom_text_multi'),
            'revision'  => true,
            'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea_multi(),
          ),
        ),
      );
    }

   // 2/3 x 1/3: Column 2 Options
    public static function phila_metabox_thirds_option_two( ){

    return array(
      'id' => 'phila_one_third_col',
      'type' => 'group',
      'fields' => array(
          array(
          'name' => 'Column 2 <br/><small>(1/3 width column)</small>',
          'id'   => 'phila_one_third_col_option',
          'desc'  => 'Choose to display recent blog posts or custom markup text.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            'phila_connect_panel' => 'Connect Panel',
            'phila_custom_text' => 'Custom Text',
            ),
          ),
          array(
            'id' => 'phila_connect_panel',
            'type' => 'group',
            'hidden' => array('phila_one_third_col_option', '!=', 'phila_connect_panel'),
            'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_connect(),
          ),
          array(
            'id'   => 'phila_custom_text',
            'type' => 'group',
            'visible' => array('phila_one_third_col_option', '=', 'phila_custom_text'),
            'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
          ),
        ),
      );
    }

   // 1/2 x 1/2: Column 1 Options
  public static function phila_metabox_half_option_one( ){
    return array(
    'id' => 'phila_half_col_1',
    'type' => 'group',
    'fields' => array(
        array(
          'name' => 'Column 1 <br/><small>(1/2 width column)</small>',
          'id'   => 'phila_half_col_1_option',
          'desc'  => 'Choose to display recent blog posts or custom markup text.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            'phila_custom_text' => 'Custom Text',
            'phila_pullquote' => 'Pullquote',
            ),
        ),
        array(
          'id'   => 'phila_custom_text',
          'type' => 'group',
          'visible' => array('phila_half_col_1_option', '=', 'phila_custom_text'),
          'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
        ),
        array(
          'id'   => 'phila_pullquote',
          'type' => 'group',
          'visible' => array('phila_half_col_1_option', '=', 'phila_pullquote'),
          'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_pullquote(),
        ),
      ),
    );
  }
   // 1/2 x 1/2: Column 1 Options
    public static function phila_metabox_half_option_two( ){
    return array(
      'id' => 'phila_half_col_2',
      'type' => 'group',
      'fields' => array(
        array(
          'name' => 'Column 2 <br/><small>(1/2 width column)</small>',
          'id'   => 'phila_half_col_2_option',
          'desc'  => 'Choose to display recent blog posts or custom markup text.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            'phila_custom_text' => 'Custom Text',
            'phila_pullquote' => 'Pullquote',
            ),
        ),
        array(
          'id'   => 'phila_custom_text',
          'type' => 'group',
          'visible' => array('phila_half_col_2_option', '=', 'phila_custom_text'),
          'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
        ),
        array(
          'id'   => 'phila_pullquote',
          'type' => 'group',
          'visible' => array('phila_half_col_2_option', '=', 'phila_pullquote'),
          'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_pullquote(),
        ),
      ),
    );
  }

  public static function phila_metabox_photo_callout( ){
      return  array(
        array(
          'name' => 'Display with Image?',
          'id'   => 'phila_v2_photo_callout_block__image_toggle',
          'type' => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No',
        ),
        array(
          'id' => 'phila_v2_photo_callout_block__txt-header',
          'type' => 'text',
          'name' => 'Header',
          'columns' => 12
        ),
        array(
          'id' => 'phila_v2_photo_callout_block__txt-sub-header',
          'type' => 'text',
          'name' => 'Sub-header',
          'columns' => 12,

          'visible' => array(
            'when' => array(
              array( 'phila_v2_photo_callout_block__image_toggle', '=', 1 ),
            ),
          ),
        ),
        array(
          'id'   => 'phila_v2_photo_callout_block__link',
          'type' => 'url',
          'name' => 'Button URL',
          'columns' => 12
        ),
        array(
          'id' => 'phila_v2_photo-callout-block__txt-btn-label',
          'type' => 'text',
          'name' => 'Button Text',
          'columns' => 12
        ),
        array(
          'id' => 'phila_v2_photo-callout-block__txt-icon',
          'type' => 'text',
          'name' => 'Icon selection',
          'desc'  => 'Choose a <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Font Awesome</a> icon to represent a top-level page. E.g.: fas fa-bell.',
          'columns' => 12
        ),
        array(
            'id' => 'phila_v2_photo-callout-block__desc',
            'type' => 'textarea',
            'name' => 'Description',
            'columns' => 12,

            'visible' => array(
              'when' => array(
                array( 'phila_v2_photo_callout_block__image_toggle', '=', 1 ),
              ),
            ),
        ),
        array(
          'id' => 'phila_v2_photo_callout_block__photo',
          'title' => 'Select image',
          'type' => 'image_advanced',
          'max_file_uploads' => 1,
          'columns' => 12,

          'visible' => array(
            'when' => array(
              array( 'phila_v2_photo_callout_block__image_toggle', '=', 1 ),
            ),
          ),
        ),
      );
  }

  public static function phila_metabox_heading_group( ){

    return array(
      array(
        //TODO: determine way to display step numbers in admin
        'placeholder' => 'Heading',
        'id'  => 'phila_wysiwyg_heading',
        'type'  => 'text',
        'class' => 'percent-95'
      ),
      array(
        'id'  => 'phila_unique_wysiwyg_content',
        'type'  => 'wysiwyg',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
      ),
      array(
        'name'  => 'Expand/collapse this section?',
        'id'  => 'phila_expand_collapse',
        'type'  => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No'
      ),
      array(
        'name'  => 'Include contact information?',
        'id'  => 'phila_address_select',
        'type'  => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
      ),
      array(
        'id' => 'phila_std_address',
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
        'name'  => 'Add stepped content?',
        'id'  => 'phila_stepped_select',
        'type'  => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No'
      ),
      array(
        'id' => 'phila_stepped_content',
        'type' => 'group',
        'visible' => array('phila_stepped_select', true),

        'fields'  => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content(),
          
        )
      ),
    );
  }

  public static function phila_metabox_faq( ){
    return  array(
      array(
        'name' => 'Section title',
        'id'   => 'accordion_row_title',
        'type' => 'text',
        'class' => 'percent-90',
      ),
      array(
        'id'   => 'accordion_group',
        'type' => 'group',
        'clone'  => true,
        'sort_clone' => true,
        'add_button' => '+ Add FAQ',
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_double_wysiwyg(
            $section_name = 'FAQ title', 
            $wysiwyg_desc = 'FAQ content', 
            $columns = 12, 
            $clone = true ),
        )
      )
    );
  }
}
