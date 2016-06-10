<?php

if ( class_exists("Phila_Gov_Department_Sites" ) ){
  $phila_department_sites = new Phila_Gov_Department_Sites();
}

 class Phila_Gov_Department_Sites {


  public function __construct(){

    add_action( 'init', array( $this, 'register_content_blocks_shortcode' ) );

    add_action( 'theme_loaded', array( $this, 'department_homepage_alert' ) );

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_department_meta_boxes' ), 100 );

  }

  function phila_register_department_meta_boxes( $meta_boxes ){
    $prefix = 'phila_';

    $meta_boxes[] = array(
      'id'       => 'departments',
      'title'    => 'Department General Information',
      'pages'    => array( 'department_page' ),
      'context'  => 'normal',
      'priority' => 'high',

      'exclude' => array(
        'is_child'  => true,
      ),

      'fields' => array(
        array(
          'name'  => 'Description',
          'desc'  => 'A short description of the department. Required.',
          'id'    => $prefix . 'dept_desc',
          'type'  => 'textarea',
          'class' => 'dept-description',
          'clone' => false,
        ),
        array(
          'name'  => 'External URL of Department',
          'desc'  => 'If the department does not live on this website, enter the location here. Eg. http://phila.gov/health/. <br>If the department lives off-site, then the transition template is displayed, instead of the body content.',
          'id'    => $prefix . 'dept_url',
          'type'  => 'URL',
          'class' => 'dept-url',
          'clone' => false,
        ),
      )
    );//External department link
    $meta_boxes[] = array(
      'id'       => 'department-home-alert',
      'title'    => 'Homepage Alert',
      'pages'    => array( 'department_page' ),
      'context'  => 'normal',
      'priority' => 'high',

      'exclude' => array(
        'is_child'  => true,
      ),

      'fields' => array(
        array(
          'name'  => 'Alert text',
          'desc'  => 'E.g. Phone lines are down. 225 character maximum.',
          'id'    => $prefix . 'department_home_alert_title',
          'type'  => 'textarea',
          'class' => 'department-home-alert',
          'clone' => false,
        ),
        array(
          'name'  => 'Link to more information',
          'desc'  => '',
          'id'    => $prefix . 'department_home_alert_link',
          'type'  => 'URL',
          'class' => 'dept-home-alert-url',
          'clone' => false,
        ),
      )
    );//Department homepage alert

    $meta_boxes[] = array(
      'title'    => 'Content Blocks',
      'pages'    => array( 'department_page' ),
      'context'  => 'normal',
      'priority' => 'low',

      'fields' => array(
        array(
         'id' => 'content_blocks',
         'type' => 'group',
         'clone'  => true,

         'fields' => array(
           array(
             'name' => 'ID',
             'id'   => $prefix . 'block_id',
             'type' => 'text',
             'class' => 'block-number',
             'desc' => 'Use this value when adding blocks to the wysiwyg.'
           ),
            array(
              'name'  => 'Block Heading',
              'id'    => $prefix . 'block_heading',
              'type'  => 'text',
              'class' => 'block-title',
              'desc'  => '20 character maximum'
            ),
            array(
              'name'  => 'Image',
              'id'    => $prefix . 'block_image',
              'type'  => 'file_input',
              'class' => 'block-image',
              'desc'  => 'Image should be no smaller than 274px by 180px.'
            ),
            array(
              'name'  => 'Title',
              'id'    => $prefix . 'block_content_title',
              'type'  => 'text',
              'class' => 'block-content-title',
              'desc'  => '70 character maximum.',
              'size'  => '60'
            ),
            array(
              'name'  => 'Summary',
              'id'    => $prefix . 'block_summary',
              'type'  => 'textarea',
              'class' => 'block-summary',
              'desc'  => '225 character maximum.'
            ),
            array(
              'name'  => 'Link to Content',
              'id'    => $prefix . 'block_link',
              'type'  => 'url',
              'class' => 'block-url',
              'desc'  => 'Enter a URL. E.g. http://alpha.phila.gov/oem',
              'size'  => '60',
            ),
          )
        )
      )
    );

    return $meta_boxes;

  }

  function content_blocks_shortcode( $atts ) {
    $a = shortcode_atts( array(
      'id' => ''
    ), $atts );

    $content_blocks = rwmb_meta( 'content_blocks' );

    foreach( $content_blocks as $key => $array_value ) {

      $block_id = isset( $array_value['phila_block_id'] ) ? $array_value['phila_block_id'] : '';

      $block_heading = isset( $array_value['phila_block_heading'] ) ? $array_value['phila_block_heading'] : '';

      //match on the ID param
      if ( strtolower( $a['id'] ) == strtolower( $block_id ) ){

        $output = '';
        $output .= '<h2 class="alternate">' . $block_heading . '</h2>';

        $block_link = isset( $array_value['phila_block_link'] ) ? $array_value['phila_block_link'] : '';
        if ($block_link == '') {
          $output .= '<div class="card equal">';
          $output .= '<div class="content-block">';
          $block_image = isset( $array_value['phila_block_image'] ) ? $array_value['phila_block_image'] : '';

          if ( !$block_image == '' ) {
            $output .= '<img src="' . $block_image . '" alt="">';
          }

          $block_title = isset( $array_value['phila_block_content_title'] ) ? $array_value['phila_block_content_title'] : '';
          $output .= '<h3>' . $block_title . '</h3>';

          $block_summary = isset( $array_value['phila_block_summary'] ) ? $array_value['phila_block_summary'] : '';
          $output .= '<p>' . $block_summary . '</p>';

          $output .= '</div></div>';
        }else{
          $output .= '<a href="' . $block_link . '" class="card equal">';

          $block_image = isset( $array_value['phila_block_image'] ) ? $array_value['phila_block_image'] : '';

          if ( !$block_image == '' ) {
            $output .= '<img src="' . $block_image . '" alt="">';
          }

          $output .= '<div class="content-block">';

          $block_title = isset( $array_value['phila_block_content_title'] ) ? $array_value['phila_block_content_title'] : '';

          $output .= '<h3>' . $block_title . '</h3>';

          $block_summary = isset( $array_value['phila_block_summary'] ) ? $array_value['phila_block_summary'] : '';

          $output .= '<p>' . $block_summary . '</p>';
          $output .= '</div>';
          $output .= '</a>';
        }
        return $output;

        break;
      }
    }

  }
  function register_content_blocks_shortcode(){
    add_shortcode( 'content-block', array($this, 'content_blocks_shortcode') );
  }

  function override_mce_options($initArray) {
      $opts = '*[*]';
      $initArray['valid_elements'] = $opts;
      $initArray['extended_valid_elements'] = $opts;
      return $initArray;
  }

  static function department_homepage_alert(){
    if (function_exists('rwmb_meta')) {
      $home_alert_title = rwmb_meta( 'phila_department_home_alert_title', $args = array('type' => 'textarea'));
      $home_alert_link = rwmb_meta( 'phila_department_home_alert_link', $args = array('type' => 'url'));
      if (!$home_alert_title == ''){
        echo '<div class="columns"><div data-alert class="alert-box info"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>' . $home_alert_title;
          if (!$home_alert_link == ''){
          echo ' <a href="' . $home_alert_link . '">More &raquo;</a>';
        }
        echo '<a href="#" class="close">&times;</a></div></div>';
      }
    }
  }
}
