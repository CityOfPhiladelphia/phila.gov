<?php
/* Register taxonomy metaboxes */

if ( class_exists('Phila_Gov_Taxonomy_Meta' ) ){
  $phila_taxonomy_meta_load = new Phila_Gov_Taxonomy_Meta();
}

class Phila_Gov_Taxonomy_Meta {

  public function __construct(){

    add_filter( 'rwmb_meta_boxes', array($this, 'phila_register_taxonomy_meta_boxes' ), 10 );

  }

  function phila_register_taxonomy_meta_boxes( $meta_boxes ){
    $meta_boxes[] = array(
      'title'      => '',
      'taxonomies' => 'category', // List of taxonomies. Array or string

      'fields' => array(
        array(
          'desc' => 'Prevent this owner from appearing on the user-facing site? Primarily used for staged content.',
          'id'   => 'hidden',
          'type' => 'checkbox',
        ),
        array(
          'type'  => 'group',
          'name' => 'Unit information',
          'id'  => 'department_units',
          'clone' => true,
          'sort_clone'  => true,
          'desc'  => 'Does this owner have units that sit under it in the org. chart? If so, enter those units here. Used for expressing hierarchy throughout the site.',
          'add_button'  => '+ Add another unit',
          'fields'  => array(
            array(
              'name' => 'Unit name',
              'id'   => 'name',
              'type' => 'text',
              'placeholder' => 'E.g. Director\'s Office'
            ),
            array(
              'name'=> 'Unit description (optional)',
              'id'  => 'unit_description',
              'type'  => 'wysiwyg',
              'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
              'desc'  => 'Use this area to add a short description of a unit. This will appear in locations where staff members are grouped by unit.'
            ),
            array(
              'name' => 'Department homepage (optional)',
              'id' => 'url',
              'type'  => 'post',
              'post_type' => 'department_page',
              'query_args'  => array(
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'post_parent'  => 0,
              ),
            ),
          ),
        )
      ),
    );
    return $meta_boxes;
  }

}
