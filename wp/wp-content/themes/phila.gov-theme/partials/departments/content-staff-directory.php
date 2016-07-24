<?php
/**
 * The template used for displaying Staff Directory
 *
 * @package phila-gov
 */
?>
<?php

$user_selected_template = phila_get_selected_template();

if (has_category()):
  $categories = get_the_category();
  $category_id = $categories[0]->cat_ID;
  $staff_leadership_array = array();
  // The Staff Directory Loop
  $args = array ( 'orderby' => 'title', 'order' => 'ASC', 'post_type' => 'staff_directory', 'cat' => $category_id );
  $staff_member_loop = new WP_Query( $args );

  if ( $staff_member_loop->have_posts() ):
    $all_staff_table_output = '';
    while ( $staff_member_loop->have_posts() ) :
      $staff_leadership_output = '';
      $staff_member_loop->the_post();
      if (function_exists('rwmb_meta')){

        $staff_first_name = rwmb_meta('phila_first_name', $args = array('type'=>'text'));
        $staff_middle_name = rwmb_meta('phila_middle_name', $args = array('type'=>'text'));
        $staff_last_name = rwmb_meta('phila_last_name', $args = array('type'=>'text'));
        $staff_name_suffix = rwmb_meta('phila_name_suffix', $args = array('type'=>'select'));

        //Build the name
        $staff_member_name_output = '';

        if ( isset( $staff_first_name ) && !$staff_first_name == '' && isset( $staff_last_name ) && !$staff_last_name == ''):
          $staff_member_name_output .= $staff_first_name . ' ';
          if( isset( $staff_middle_name ) && !$staff_middle_name == '' ) $staff_member_name_output .= $staff_middle_name . ' ';
          $staff_member_name_output .= $staff_last_name;
          if( isset( $staff_name_suffix ) && !$staff_name_suffix == '' ) $staff_member_name_output .= ', ' . $staff_name_suffix;
        endif;


        $staff_title = rwmb_meta('phila_job_title', $args = array('type'=>'text'));
        $staff_email = rwmb_meta('phila_email', $args = array('type'=>'email'));
        $staff_phone = rwmb_meta('phila_phone', $args = array('type'=>'phone'));
        if( !$staff_phone['area'] == '' && !$staff_phone['phone-co-code'] == '' && !$staff_phone['phone-subscriber-number'] == '' ){
          $staff_phone_unformatted = $staff_phone['area'] . $staff_phone['phone-co-code'] . $staff_phone['phone-subscriber-number'];
          $staff_phone_formatted = '(' . $staff_phone['area'] . ') ' . $staff_phone['phone-co-code'] . '-' . $staff_phone['phone-subscriber-number'];
        }

        $staff_social = rwmb_meta( 'phila_staff_social' );
        $staff_social_output = '';

        if ( is_array( $staff_social )):

          if ( isset( $staff_social['phila_staff_facebook'] ) ):
            $staff_social_output .= '<a href="' . $staff_social['phila_staff_facebook'] . '" target="_blank" class="phs"><i class="fa fa-facebook fa-lg" title="Facebook" aria-hidden="true"></i><span class="show-for-sr">Facebook</span></a>';
          endif;

          if ( isset( $staff_social['phila_staff_twitter'] ) ):
            $staff_social_output .= '<a href="' . $staff_social['phila_staff_twitter'] . '" target="_blank" class="phs"><i class="fa fa-twitter fa-lg" title="Twitter" aria-hidden="true"></i><span class="show-for-sr">Twitter</span></a>';
          endif;

          if ( isset( $staff_social['phila_staff_instagram'] ) ):
            $staff_social_output .= '<a href="' . $staff_social['phila_staff_twitter'] . '" target="_blank" class="phs"><i class="fa fa-instagram fa-lg" title="Instagram" aria-hidden="true"></i><span class="show-for-sr">Instagram</span></a>';
          endif;

        endif;


        $staff_leadership = rwmb_meta('phila_leadership', $args = array('type'=>'checkbox'));
      }
      if ( $staff_leadership ):
        $staff_options = rwmb_meta('phila_leadership_options');
        $staff_display_order = intval( $staff_options['phila_display_order'] );
        $staff_summary = $staff_options['phila_summary'];
        $staff_leadership_output .= '<div class="row">';
        // Leadership Thumbnail
        if ( has_post_thumbnail() ):
          $staff_photo = get_the_post_thumbnail( $post->ID, 'staff-thumb', 'class= staff-thumbnail' );
          $staff_leadership_output .= '<div class="small-24 medium-5 columns">' . $staff_photo . '</div>';
        endif;

        // Leadership Contact Info
        $staff_leadership_output .= '<div class="small-24 medium-5 columns staff-contact">';

        $staff_leadership_output .= '<div class="name">';
        $staff_leadership_output .= $staff_member_name_output;
        $staff_leadership_output .= '</div>';

        if ( isset( $staff_title ) && !$staff_title == ''):
          $staff_leadership_output .= '<div class="job-title">' . $staff_title . '</div>';
        endif;

        if ( isset( $staff_phone_unformatted ) && isset( $staff_phone_formatted ) ):
          $staff_leadership_output .= '<div class="tel"><a href="tel:' . $staff_phone_unformatted . '">' . $staff_phone_formatted . '</a></div>';
        endif;

        if ( isset( $staff_email ) && !$staff_email == ''):
          $staff_leadership_output .= '<div class="email"><a href="mailto:' . $staff_email . '">' . $staff_email . '</a></div>';
        endif;

        if ( isset( $staff_social_output ) && !$staff_social_output == ''):
          $staff_leadership_output .= '<div class="social">' . $staff_social_output . '</div>';
        endif;

        if ( isset( $staff_summary ) && !$staff_summary == '' ):
          $staff_leadership_output .= '</div>';
          $staff_leadership_output .= '<div class="medium-14 columns staff-summary">' . $staff_summary . '</div>';
        endif;
        $staff_leadership_output .= '</div>';

        if ( key_exists( $staff_display_order, $staff_leadership_array ) ):
          ++$staff_display_order;
        endif;

        $staff_leadership_array[$staff_display_order] = $staff_leadership_output;

      else:

        $all_staff_table_output .= '<tr>
          <td>' . $staff_member_name_output . '</td>
          <td>' . $staff_title . '</td>
          <td><a href="mailto:' . $staff_email . '">' . $staff_email . '</a></td>
          <td><a href="tel:' . $staff_phone_unformatted . '">' . $staff_phone_formatted . '</a></td>
          <td class="social">' . $staff_social_output . '</td>
        </tr>';
      endif;
    endwhile;

    echo '<section class="mvm staff-directory">';

    if (!empty($staff_leadership_array)):?>
      <div class="mvm staff-leadership">
        <div class="row">
          <div class="large-24 columns">
          <?php if ($user_selected_template != 'staff_directory') : ?>
            <h2 class="contrast">Leadership</h2>
          <?php endif; ?>
            <?php
            ksort($staff_leadership_array);
            foreach ($staff_leadership_array as $key => $value):
              echo $value;
            endforeach;
            ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <!-- Begin Staff Directory Table -->
    <?php if (!$all_staff_table_output == ''): ?>
      <div class="mvl all-staff-table">
        <div class="row">
          <div class="large-24 columns">
            <?php if ($user_selected_template != 'staff_directory') : ?>
              <h2 class="contrast">All Staff</h2>
            <?php endif; ?>
            <table role="grid" class="tablesaw tablesaw-stack" data-tablesaw-mode="stack">
              <thead>
                <tr>
                  <th scope="col">Name</th>
                  <th scope="col">Job Title</th>
                  <th scope="col">Email</th>
                  <th scope="col">Phone #</th>
                  <th scope="col">Social</th>
                </tr>
              </thead>
              <tbody>
                <?php echo $all_staff_table_output;?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php else: ?>
      <div class="mvm">
        <div class="row">
          <div class="large-24 columns">
            <div class="placeholder center mbl mtm mtl-mu">
              <p>No staff content found.</p>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php wp_reset_query();?>
<?php else: ?>
  <div class="mvm">
    <div class="row">
      <div class="large-24 columns">
        <div class="placeholder center mbl mtm mtl-mu">
          <p>This page must have at least one category in order to display a staff directory.</p>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php echo '</section>'; ?>
