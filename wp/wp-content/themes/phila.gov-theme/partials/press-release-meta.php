<?php
/**
 * Press release header
 */
?>
<?php
  if ( function_exists('rwmb_meta') ) :
    $category = phila_get_current_department_name( get_the_category() );

    $press_contacts = rwmb_meta( 'press_release_contact' );

    $press_date = rwmb_meta( 'phila_press_release_date', $args = array('type' => 'date'));
    printf( _e( '<span class="date-released"><strong>For immediate release: </strong>' . $press_date . '</span>', 'phila-gov' ) );

    printf ( _e('<span><strong>Published by:</strong> ' . $category . '</span>', 'phila-gov' ) ) ;

    foreach( $press_contacts as $contact ) :
      $press_name = isset( $contact['phila_press_release_contact_name'] ) ? $contact['phila_press_release_contact_name'] : '';

      $press_phone = isset( $contact['phila_press_release_contact_phone'] ) ? $contact['phila_press_release_contact_phone'] : '';

      $press_email = isset( $contact['phila_press_release_contact_email'] ) ? $contact['phila_press_release_contact_email'] : '';

      $phone_exists = ( $press_phone == '' ) ? '' : $press_phone . ', ';

      $all_contacts = trim($press_name) . ', '. trim($phone_exists) . ' <a href="mailto:' . $press_email . '">' . $press_email . '</a> ' ;
    endforeach;

    printf( _e( '<span><strong>Contact: </strong>' . $all_contacts . '</span>', 'phila-gov' ) );

  endif;
?>
