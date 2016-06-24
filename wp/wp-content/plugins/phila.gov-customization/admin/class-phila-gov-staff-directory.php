<?php

/**
* Add Staff Directory custom post type
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
    add_filter( 'wp_insert_post_data' , array($this, 'staff_directory_post_title') );

  }

  function staff_directory_post_title( $data , $postarr )
  {
    if($data['post_type'] == 'staff_directory') {
      $staff_member_name = get_post_meta( get_the_ID(),'phila_last_name',true) . ', ' . get_post_meta( get_the_ID(),'phila_first_name',true);
      if (isset($staff_member_name) && $staff_member_name != ', ' ){
        $data['post_title'] = $staff_member_name;
      }
    }
    return $data;
  }

  function phila_register_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';

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
          'name'  => 'Last Name',
          'id'    => $prefix . 'last_name',
          'type'  => 'text',
          'class' => 'last-name',
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
          'name'  => 'Highlight Leadership',
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
              'type'  => 'select',
              'class' => 'display-order',
              'options' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10',
              ),
            ),
            array(
              'name'  => 'Summary',
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
              ),
            ),
          ),
        ),
      ),
    );
    return $meta_boxes;
  }
}
