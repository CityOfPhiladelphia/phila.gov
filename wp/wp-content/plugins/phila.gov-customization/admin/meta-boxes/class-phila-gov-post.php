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
          'id'  => 'social_intent',
          'desc'  => 'Curate Tweet and Facebook sharing text. E.g.: ',
        )
      ),
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
