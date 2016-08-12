<?php

if ( class_exists("Phila_Item_Meta_Desc" ) ){
  $phila_meta_desc = new Phila_Item_Meta_Desc();
}

 class Phila_Item_Meta_Desc {


  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_item_desc_meta_boxes' ), 10, 1 );

  }

  function phila_register_item_desc_meta_boxes( $meta_boxes ){
    $post_types = array(
      'public'  => true,
      '_builtin'  => true
    );

    $public_post_types = get_post_types( $post_types, $output = 'names', $operator = 'or' );

    $prefix = 'phila_';
    //THESE ARE OLD
    $meta_boxes[] = array(
      'id'       => $prefix . 'events',
      'title'    => 'General Information',
      'pages'    => array( 'event_page' ),
      'context'  => 'normal',
      'priority' => 'high',

      'fields' => array(
        array(
          'name'  => 'Description',
          'desc'  => 'A short description of the event (365 character maximum). Required.',
          'id'    => $prefix . 'event_desc',
          'type'  => 'textarea',
          'class' => 'event-description',
          'clone' => false,
        )
      )
    );

    $meta_boxes[] = array(
      'id'       => 'news',
      'title'    => 'News Information',
      'pages'    => array( 'news_post' ),
      'context'  => 'normal',
      'priority' => 'high',


      'fields' => array(
        array(
          'name'  => 'Description',
          'desc'  => 'A one or two sentence description describing this article. Required.',
          'id'    => $prefix . 'news_desc',
          'type'  => 'textarea',
          'class' => 'news-description',
          'clone' => false,
        )
      )
    );//news description

    $meta_boxes[] = array(
      'id'       => $prefix . 'document-description',
      'title'    => 'Document Information',
      'pages'    => array( 'document' ),
      'context'  => 'normal',
      'priority' => 'high',


      'fields' => array(
        array(
         'name' => 'Description',
         'id'   => $prefix . 'document_description',
         'type' => 'textarea'
       ),
     )
   );

   $meta_boxes[] = array(
     'id'       => 'post-description',
     'title'    => 'Post Summary',
     'pages'    => array( 'phila_post' ),
     'context'  => 'normal',
     'priority' => 'high',


     'fields' => array(
       array(
        'name' => 'Summary',
        'id'   => $prefix . 'post_desc',
        'type' => 'textarea'
      ),
    ),
   );

  $meta_boxes[] = array(
    'id'  => 'item_description',
    'title' => 'Item Summary',
    'context'  => 'advanced',
    'priority' => 'high',
    'pages' => $public_post_types,

      'fields' => array(
        array(
         'name' => 'Summary',
         'id'   => $prefix . 'post_desc',
         'type' => 'textarea'
       ),
     ),
  );

    return $meta_boxes;

  }
}
