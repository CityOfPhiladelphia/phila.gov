<?php
/**
 * The template used for displaying Staff Directory
 *
 * @package phila-gov
 */
?>
<!-- Staff List -->
<!-- Staff Leadership -->
  <!-- Placeholder -->
<!-- End Staff Leadership -->
<!-- All Staff -->
        <?php
        $categories = get_the_category();
        $category_id = $categories[0]->cat_ID;

        // The Staff Directory Loop
        $args = array ( 'orderby' => 'title', 'order' => 'ASC', 'post_type' => 'staff_directory', 'cat' => $category_id );
        $staff_member_loop = new WP_Query( $args );

        if ( $staff_member_loop->have_posts() ):
          $staff_leadership_output = '';
          $all_staff_table_output = '';
          while ( $staff_member_loop->have_posts() ) :
                $staff_member_loop->the_post();
                if (function_exists('rwmb_meta')){
                  $staff_first_name = rwmb_meta('phila_first_name', $args = array('type'=>'text'));
                  $staff_last_name = rwmb_meta('phila_last_name', $args = array('type'=>'text'));
                  $staff_title = rwmb_meta('phila_job_title', $args = array('type'=>'text'));
                  $staff_email = rwmb_meta('phila_email', $args = array('type'=>'email'));
                  $staff_phone = rwmb_meta('phila_phone', $args = array('type'=>'phone'));
                  if( !$staff_phone['area'] == '' && !$staff_phone['phone-co-code'] == '' && !$staff_phone['phone-subscriber-number'] == '' ){
                    $staff_phone_unformatted = $staff_phone['area'] . $staff_phone['phone-co-code'] . $staff_phone['phone-subscriber-number'];
                    $staff_phone_formatted = '(' . $staff_phone['area'] . ') ' . $staff_phone['phone-co-code'] . '-' . $staff_phone['phone-subscriber-number'];
                  }
                  $staff_leadership = rwmb_meta('phila_leadership', $args = array('type'=>'checkbox'));
                }
                if ( $staff_leadership ):

                  $staff_options = rwmb_meta('phila_leadership_options');
                  $staff_summary = $staff_options['phila_summary'];

                  // Leadership Thumbnail
                  if ( has_post_thumbnail() ):
                    $staff_photo = get_the_post_thumbnail( $post->ID, 'thumbnail', 'class= small-thumb mrm' );
                    $staff_leadership_output .= '<div class="small-12 medium-4 columns">' . $staff_photo . '</div>';
                  endif;

                  // Leadership Contact Info
                  $staff_leadership_output .= '<div class="small-12 medium-4 columns vcard">';
                  if ( isset( $staff_first_name ) && !$staff_first_name == '' && isset( $staff_last_name ) && !$staff_last_name == ''):
                    $staff_leadership_output .= '<div class="name">' . $staff_first_name . ' ' . $staff_last_name . '</div>';
                  endif;
                  if ( isset( $staff_title ) && !$staff_title == ''):
                    $staff_leadership_output .= '<div class="job-title">' . $staff_title . '</div>';
                  endif;

                  if ( isset( $staff_email ) && !$staff_email == ''):
                    $staff_leadership_output .= '<div class="email"><a href="mailto:' . $staff_email . '">' . $staff_email . '</a></div>';
                  endif;

                  if ( isset( $staff_phone_unformatted ) && isset( $staff_phone_formatted ) ):
                    $staff_leadership_output .= '<div class="tel"><a href="tel:' . $staff_phone_unformatted . '">' . $staff_phone_formatted . '</a></div>';
                  endif;
                  // Leadership Summary
                  if ( isset( $staff_summary ) && !$staff_summary == '' ):
                    $staff_leadership_output .= '</div>';
                    $staff_leadership_output .= '<div class="medium-16 columns">' . $staff_summary . '</div>';
                  endif;

                else:

                  $all_staff_table_output .= '<tr>
                    <td>' . $staff_first_name . ' ' . $staff_last_name . '</td>
                    <td>' . $staff_title . '</td>
                    <td><a href="mailto:' . $staff_email . '">' . $staff_email . '</a></td>
                    <td><a href="tel:' . $staff_phone_unformatted . '">' . $staff_phone_formatted . '</a></td>
                  </tr>';
                endif;
          endwhile; ?>
<section class="mvm">
<div class="row">
  <div class="large-24 columns">
    <h2 class="contrast">Leadership</h2>
      <div class="row">
        <?php echo $staff_leadership_output; ?>
      </div>
    </div>
</div>
</section>
    <!-- Begin Staff Directory Table -->

<section class="mvm">
  <div class="row">
    <div class="large-24 columns">
      <h2 class="contrast">All Staff</h2>
      <table role="grid" class="tablesaw tablesaw-stack" data-tablesaw-mode="stack">
        <thead>
          <tr>
            <th scope="col">Name</th>
            <th scope="col">Job Title</th>
            <th scope="col">Email</th>
            <th scope="col">Phone #</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $all_staff_table_output;?>
        </tbody>
      </table>
      <?php else: ?>
                <!--TODO: "No Staff found" placeholder -->
      <?php endif; ?>

      <?php wp_reset_query();?>
    </div>
  </div>
</section>
<!-- End All Staff -->
