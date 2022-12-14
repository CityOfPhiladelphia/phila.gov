<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_department_contact_us' );

function phila_register_department_contact_us( $meta_boxes ){

  $meta_boxes[] = array(
    'title' => 'Contact us',
    'pages' => array('department_page'),
    'visible' => array( 'phila_template_select', 'contact_us_v2' ),

    'fields' => array(
      array(
        'id' => 'phila_contact_us',
        'context'  => 'normal',
        'priority' => 'high',
        'type'  => 'group',
        'clone' => true,
        'add_button'  => '+ Add a row',

        'fields' => array(
          array(
            'type' => 'heading',
            'name' => 'Contact row heading',
          ),
          array(
            'id'  => 'phila_contact_row_title',
            'type' => 'text',
            'class' => 'percent-95'
          ),
          array(
            'id'     => 'phila_contact_group',
            'type'   => 'group',
            'clone'  => true,
            'max_clone' => 3,
            'add_button'  => '+ Add a column',

            'fields' => array(
              array(
                'type' => 'heading',
                'name'  => 'Column title'
              ),
              array(
                'id' => 'phila_contact_column_title',
                'type'  => 'text',
                'class' => 'percent-95'
              ),
              Phila_Gov_Standard_Metaboxes::phila_v2_icon_selection(),
              Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields(),
              Phila_Gov_Standard_Metaboxes::phila_v2_phone(),
              Phila_Gov_Standard_Metaboxes::phila_v2_fax(),
              Phila_Gov_Standard_Metaboxes::phila_v2_email(),
              array(
                'type' => 'heading',
                'name'  => 'Open hours'
              ),
              Phila_Gov_Standard_Metaboxes::phila_v2_hours(),

            ),
          ),
        ),
      ),
    )
  );

  return $meta_boxes;

}
