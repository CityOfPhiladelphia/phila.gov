<?php
if ( class_exists("Phila_Vue_App_Files" ) ){
  $phila_meta_desc = new Phila_Vue_App_Files();
}

class Phila_Vue_App_Files {


  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_vue_app_meta_boxes' ), 1000, 1 );

  }

  function phila_register_vue_app_meta_boxes( $meta_boxes ){

    $css_field = array(
      'placeholder'  => 'css url',
      'id'  => 'phila_vue_app_css_url',
      'type'  => 'url',
      'class' => ''
    );

    $js_field = array(
      'placeholder'  => 'js url',
      'id'  => 'phila_vue_app_js_url',
      'type'  => 'url',
      'class' => ''
    );

     $meta_boxes[] = array(
      'title' => 'Vue App Files',
      'post_types' => 'page',
      'context'  => 'normal',
      'priority' => 'high',
      'include' => array('template' => array('vue-app.php')),
      'fields' => array(
        array(
          'id' => 'phila-vue-app-css',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'add_button'  => '+ Add Css Url',
          'fields' => array(
            $css_field,
          ),
        ),
        array(
          'id' => 'phila-vue-app-js',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'add_button'  => '+ Add Js Url',
          'fields' => array(
            $js_field,
          ),
        )
      )
    );

    return $meta_boxes;

  }
}
