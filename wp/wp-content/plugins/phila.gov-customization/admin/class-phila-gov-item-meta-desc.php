<?php

if ( class_exists("Phila_Item_Meta_Desc" ) ){
  $phila_meta_desc = new Phila_Item_Meta_Desc();
}

 class Phila_Item_Meta_Desc {


  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_item_desc_meta_boxes' ), 10, 1 );

  }

  function phila_register_item_desc_meta_boxes( $meta_boxes ){
    //TODO: remove deprecated code
    $post_types = array(
      'public'  => true,
      '_builtin'  => true
    );

    $public_post_types = get_post_types( $post_types, $output = 'names', $operator = 'or' );

    $prefix = 'phila_';

    $meta_boxes[] = array(
      'id'  => 'item_description',
      'title' => 'Short Description',
      'context'  => 'advanced',
      'priority' => 'high',

      'pages' => $public_post_types,

      'fields' => array(
        array(
          'id'   => $prefix . 'meta_desc',
          'type' => 'textarea',
          'desc'  => 'Enter a short description of this content. This description will appear in lists that include this item, search results, and social media link previews. 140 character maximum.',
          'required'  => true
         ),
       ),
    );

    return $meta_boxes;

  }
}
