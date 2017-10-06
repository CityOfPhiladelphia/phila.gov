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
      'title'    => 'Social media share pre-filled text',
      'pages'    => array( 'post' ),
      'context'  => 'advanced',
      'priority' => 'default',
      'fields'  => array(
        array(
          'type' => 'textarea',
          'required'  => true,
          'id'  => 'phila_social_intent',
          'desc'  => 'Curate Tweet sharing text. Required. 116 character limit.<br /> E.g.: Now through Sept. 25, #WelcomingWeek has free events citywide to support Philly being welcoming and inclusive',
        )
      ),
    );

    $meta_boxes[] = array(
      'title'    => 'Elevate to feature',
      'pages'    => array( 'post' ),
      'context'  => 'side',
      'priority' => 'high',
      'include' => array(
        'user_role'  => array( 'administrator', 'phila_master_homepage_editor', 'editor' ),
        'relation' => 'OR',
       ),
       'fields' => array(
         array(
           'name'  => '',
           'desc'  => 'Feature this item in the latest?',
           'id'    => 'phila_is_feature',
           'type'  => 'radio',
           'std'=> '0',
           'options' =>  array(
               '0' => 'No',
               '1' => 'Yes'
           )
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

    return $meta_boxes;
  }

}
