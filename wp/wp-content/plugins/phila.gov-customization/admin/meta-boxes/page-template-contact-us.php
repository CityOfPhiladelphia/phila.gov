<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_department_contact_us' );

function phila_register_department_contact_us( $meta_boxes ){

  $meta_boxes[] = array(
    'title' => 'Contact us',
    'pages' => array('department_page'),

    'fields' => array(
      array(
        'id' => 'phila_contact_us',
        'context'  => 'normal',
        'priority' => 'high',
        'type'  => 'group',
        'clone' => true,
        'visible' => array( 'phila_template_select', 'contact_us_v2' ),

        'fields' => array(
          array(
            'type' => 'heading',
            'name' => 'Contact row title',
          ),
          array(
            'id'  => 'phila_contact_row_title',
            'type' => 'text',
          ),
          array(
            'id'     => 'phila_contact_group',
            'type'   => 'group',
            'clone'  => true,
            'max_clone' => 3,

            'fields' => array(
              array(
                'type' => 'heading',
                'name'  => 'Column title'
              ),
              array(
                'id' => 'phila_contact_column_title',
                'type'  => 'text',
                'desc'  => 'Enter the title for this column of contact junk'
              ),
              Phila_Gov_Standard_Metaboxes::phila_v2_icon_selection(),
              Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields(),
              Phila_Gov_Standard_Metaboxes::phila_v2_phone(),
              Phila_Gov_Standard_Metaboxes::phila_v2_fax(),
              Phila_Gov_Standard_Metaboxes::phila_v2_email(),
              Phila_Gov_Standard_Metaboxes::phila_v2_hours(),

            ),
          ),
        ),
      ),
    )
  );

  return $meta_boxes;

}
