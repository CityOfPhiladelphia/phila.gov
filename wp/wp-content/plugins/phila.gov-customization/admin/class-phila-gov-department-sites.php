<?php

if ( class_exists("Phila_Gov_Department_Sites" ) ){

  $phila_department_sites = new Phila_Gov_Department_Sites();
}

 class Phila_Gov_Department_Sites {


  public function __construct(){

    add_action( 'init', array( $this, 'register_content_blocks_shortcode' ) );

    add_action( 'add_meta_boxes', array( $this, 'register_homepage_metabox_order' ),10, 2);

    add_action( 'theme_loaded', array( $this, 'department_homepage_alert' ) );

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_department_meta_boxes' ), 100 );

    $this->prefix = "phila_";

  }


   public function register_homepage_metabox_order( $post_type, $post){
    global $wpdb;

    if($post_type === "department_page"){

      // get all Department Site Homepage IDs
      $department_homepages = $wpdb->get_results( $wpdb->prepare(
        "SELECT $wpdb->posts.ID, $wpdb->posts.post_title
         FROM $wpdb->posts INNER JOIN $wpdb->postmeta
         ON $wpdb->posts.ID = $wpdb->postmeta.post_id
         WHERE $wpdb->posts.post_type = %s AND
         $wpdb->postmeta.meta_key = 'phila_template_select' AND
         meta_value = 'homepage_v2'"
      , $post_type ) );


      //check if the "phila_meta-box-order" post meta has been set
      //if not set it as default
      foreach ($department_homepages as $post) {
        if ( !metadata_exists('post', $post->ID,  $this->prefix.'meta-box-order') ) {
          add_post_meta( $post->ID,  $this->prefix.'meta-box-order', 'default' );
        }
      }


    }

  }



  function phila_register_department_meta_boxes( $meta_boxes ){
    // $prefix = 'phila_';

    $meta_boxes[] = array(
      'id'       => 'departments',
      'title'    => 'External Site',
      'pages'    => array( 'department_page' ),
      'context'  => 'advanced',
      'priority' => 'high',

      'visible' => array(
        'phila_template_select', '=', 'off_site_department',
      ),

      'fields' => array(

        array(
          'name'  => 'External URL of Department',
          'desc'  => 'If the department does not live on this website, enter the location here. Eg. http://phila.gov/health/. <br>If the department lives off-site, then the transition template is displayed, instead of the body content.',
          'id'    => $this->prefix . 'dept_url',
          'type'  => 'URL',
          'class' => 'dept-url',
          'clone' => false,
        ),
      )
    );//External department link




    $meta_boxes[] = array(
      'title'    => 'Content Blocks',
      'pages'    => array( 'department_page' ),
      'context'  => 'normal',
      'priority' => 'low',
      'visible' => array(
        'phila_template_select', '=', array( 'default' ),
      ),

      'fields' => array(
        array(
         'id' => 'content_blocks',
         'type' => 'group',
         'clone'  => true,

         'fields' => array(
           array(
             'name' => 'ID',
             'id'   => $this->prefix . 'block_id',
             'type' => 'text',
             'class' => 'block-number',
             'desc' => 'Use this value when adding blocks to the wysiwyg.'
           ),
            array(
              'name'  => 'Block Heading',
              'id'    => $this->prefix . 'block_heading',
              'type'  => 'text',
              'class' => 'block-title',
              'desc'  => '20 character maximum'
            ),
            array(
              'name'  => 'Image',
              'id'    => $this->prefix . 'block_image',
              'type'  => 'file_input',
              'class' => 'block-image',
              'desc'  => 'Image should be no smaller than 274px by 180px.'
            ),
            array(
              'name'  => 'Title',
              'id'    => $this->prefix . 'block_content_title',
              'type'  => 'text',
              'class' => 'block-content-title',
              'desc'  => '70 character maximum.',
              'size'  => '60'
            ),
            array(
              'name'  => 'Summary',
              'id'    => $this->prefix . 'block_summary',
              'type'  => 'textarea',
              'class' => 'block-summary',
              'desc'  => '225 character maximum.'
            ),
            array(
              'name'  => 'Link to Content',
              'id'    => $this->prefix . 'block_link',
              'type'  => 'url',
              'class' => 'block-url',
              'desc'  => 'Enter a URL. E.g. http://beta.phila.gov/oem',
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
