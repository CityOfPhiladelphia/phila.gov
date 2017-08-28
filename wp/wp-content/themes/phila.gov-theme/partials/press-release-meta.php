<?php
/**
 * Press release header
 */
?>
<?php
  if ( function_exists('rwmb_meta') ) :
    $category = phila_get_current_department_name( get_the_category() );

    $press_contact_vars = rwmb_meta( 'press_release_contact' );
    $press_contacts = phila_loop_clonable_metabox($press_contact_vars);

    $press_date = rwmb_meta( 'phila_press_release_date', $args = array('type' => 'date'));
    printf( _e( '<span class="date-released"><strong>For immediate release: </strong>' . $press_date . '</span>', 'phila-gov' ) );

    printf ( _e('<span><strong>Published by:</strong> ' . $category . '</span>', 'phila-gov' ) ) ;

    if (isset($press_contacts)) : ?>
    <span><strong>Contact: </strong>
      <?php foreach( $press_contacts as $contact ) :

      $press_name = isset( $contact['phila_press_release_contact_name'] ) ? trim($contact['phila_press_release_contact_name'] ) : '';

      $press_phone = isset( $contact['phila_press_release_contact_phone'] ) ? trim($contact['phila_press_release_contact_phone']) : '';

      $press_email = isset( $contact['phila_press_release_contact_email'] ) ? trim($contact['phila_press_release_contact_email']) : '';
      ?>
      <?php echo (!empty($press_name) ) ? ($press_name) : ''; ?>
      <?php echo (!empty($press_email) ) ? ('<a href="mailto:' . $press_email . '">' . $press_email . '</a>') : ''; ?>
      <?php echo (!empty($press_phone) ) ? ($press_phone) : ''; ?>
    <?php endforeach;?>
  </span>

  <?php endif;?>
<?php endif; ?>
