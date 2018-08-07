<?php

/**
* Add Staff Directory custom meta
*
* @link https://github.com/CityOfPhiladelphia/phila.gov-customization
*
* @package phila-gov_customization
*/

if ( class_exists( "Phila_Gov_Staff_Directory" ) ){
  $phila_staff_directory = new Phila_Gov_Staff_Directory();
}

class Phila_Gov_Staff_Directory {

  public function __construct(){
    add_filter( 'rwmb_meta_boxes',  array($this, 'phila_register_meta_boxes') );
    add_filter( 'wp_insert_post_data' , array($this, 'staff_directory_post_title'), 10, 2 );
  }

  // Use staff member's name as the post title
  function staff_directory_post_title( $data , $postarr ) {
    if($data['post_type'] == 'staff_directory' && isset($_POST['phila_first_name']) && isset($_POST['phila_last_name']) ) {
      $staff_member_name = '';
      $staff_member_name .= $_POST['phila_last_name'] . ', ' . $_POST['phila_first_name'];
      // Check if middle name present
      if(isset($_POST['phila_middle_name'])) $staff_member_name .= ' ' . $_POST['phila_middle_name'];
      // Check if name suffix present
      if(isset($_POST['phila_name_suffix']) && $_POST['phila_name_suffix'] != '') $staff_member_name .= ', ' . $_POST['phila_name_suffix'];

      if (isset($staff_member_name) && $staff_member_name != ', ' ){
        $data['post_title'] = $staff_member_name;
      }
    }
    return $data;
  }

  function phila_register_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';

    $summary_toolbar1['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

    $meta_boxes[] = array(
      'id'       => 'staff_directory',
      'title'    => 'Staff Member Details',
      'pages'    => array( 'staff_directory' ),
      'priority' => 'high',
      'context'  => 'normal',

      'fields' => array(
        array(
          'name'  => 'First Name',
          'id'    => $prefix . 'first_name',
          'type'  => 'text',
          'class' => 'first-name',
        ),
        array(
          'name'  => 'Middle Name / Initial',
          'id'    => $prefix . 'middle_name',
          'type'  => 'text',
          'class' => 'middle-name',
        ),
        array(
          'name'  => 'Last Name',
          'id'    => $prefix . 'last_name',
          'type'  => 'text',
          'class' => 'last-name',
        ),
        array(
          'name'  => 'Name Suffix<br/><small>(Optional)</small>',
          'id'    => $prefix . 'name_suffix',
          'type'  => 'select',
          'class' => 'name-suffix',
          'options' => array(
            '' => 'Select One..',
            'B.A.' => 'B.A.',
            'B.S.' => 'B.S.',
            'M.D.' => 'M.D.',
            'M.S.' => 'M.S.',
            'J.D.' => 'J.D.',
            'Jr.' => 'Jr.',
            'LL.D.' => 'LL.D.',
            'Ph.D.' => 'Ph.D.',
            'Sr.' => 'Sr.',
            'II' => 'II',
            'III' => 'III',
            'IV' => 'IV',
          ),
        ),
        array(
          'name'  => 'Job Title',
          'id'    => $prefix . 'job_title',
          'type'  => 'text',
          'class' => 'job-title',
        ),
        array(
          'name'  => 'Email',
          'id'    => $prefix . 'email',
          'type'  => 'email',
          'class' => 'email',
        ),
        array(
          'name'  => 'Phone',
          'id'    => $prefix . 'phone',
          'type'  => 'phone',
          'class' => 'phone',
        ),
        array(
          'id'  => 'units',
          'name'  => 'Units associated with',
          'type' => 'unit',
          //'callback' => $this->phila_get_term_meta( $meta_boxes ),
        ),
        array(
          'id' => $prefix . 'staff_social',
          'type' => 'group',
          // List of sub-fields
          'fields' => array(
            array(
              'type' => 'heading',
              'name' => 'Social',
            ),
            array(
             'name' => 'Facebook URL',
             'id'   => $prefix . 'staff_facebook',
             'type' => 'url',
             'desc' => 'Example: https://www.facebook.com/PhiladelphiaCityGovernment/',
            ),
            array(
             'name' => 'Twitter URL',
             'id'   => $prefix . 'staff_twitter',
             'type' => 'url',
             'desc' => 'Example: https://twitter.com/PhiladelphiaGov'
            ),
            array(
             'name' => 'Instagram URL',
             'id'   => $prefix . 'staff_instagram',
             'type' => 'url',
             'desc' => 'Example: https://www.instagram.com/cityofphiladelphia/'
            ),
            array(
             'name' => 'LinkedIn URL',
             'id'   => $prefix . 'staff_linkedin',
             'type' => 'url',
             'desc' => 'Example: https://www.linkedin.com/company/city-of-philadelphia'
            ),
          ),
        ),
        array(
          'type'  => 'divider',
        ),
        array(
          'desc'  => 'Is this staff member leadership?',
          'id'    => $prefix . 'leadership',
          'type'  => 'checkbox',
          'class' => 'leadership',
        ),
        //
        array(
          'id' => $prefix . 'leadership_options',
          'type' => 'group',
          'hidden' => array( 'phila_leadership', '!=', true ),
          'fields' => array(
            array(
              'name'  => 'Display Order',
              'id'    => $prefix . 'display_order',
              'type'  => 'text',
              'class' => 'display-order',
            ),
            array(
              'name'  => 'Short biography',
              'id'    => $prefix . 'summary',
              'type'  => 'wysiwyg',
              'class' => 'summary',
              'desc'  => '700 character maximum.',
              'options' => array(
                'editor_height' => 25,
                'media_buttons' => false,
                'teeny' => true,
                'dfw' => false,
                'quicktags' => false,
                'tinymce' => $summary_toolbar1,
              ),
            ),
          ),
        ),
      ),
    );
    return $meta_boxes;
  }
}
