<?php

$staff_leadership_array = array();
$staff_member_loop = new WP_Query( $args );

if ( $staff_member_loop->have_posts() ):
  $staff_table_output = '';
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

      $staff_unit = rwmb_meta( 'units' );

      $staff_email = rwmb_meta('phila_email', $args = array('type'=>'email'));

      $staff_phone = rwmb_meta('phila_phone', $args = array('type'=>'phone'));

      if( !$staff_phone['area'] == '' && !$staff_phone['phone-co-code'] == '' && !$staff_phone['phone-subscriber-number'] == '' ){
        $staff_phone_unformatted = $staff_phone['area'] . $staff_phone['phone-co-code'] . $staff_phone['phone-subscriber-number'];
        $staff_phone_formatted = '(' . $staff_phone['area'] . ') ' . $staff_phone['phone-co-code'] . '-' . $staff_phone['phone-subscriber-number'];
      }else{
        $staff_phone_formatted = '';
      }

      $staff_social = rwmb_meta( 'phila_staff_social' );
      $staff_social_output = '';

      if ( is_array( $staff_social )):

        if ( isset( $staff_social['phila_staff_facebook'] ) ):
          $staff_social_output .= '<a href="' . $staff_social['phila_staff_facebook'] . '" class="social-link"  data-analytics="social"><i class="fab fa-facebook fa-lg" title="Facebook" aria-hidden="true"></i><span class="show-for-sr">Facebook</span></a>';
        endif;

        if ( isset( $staff_social['phila_staff_twitter'] ) ):
          $staff_social_output .= '<a href="' . $staff_social['phila_staff_twitter'] . '" class="social-link"  data-analytics="social"><i class="fab fa-twitter fa-lg" title="Twitter" aria-hidden="true"></i><span class="show-for-sr">Twitter</span></a>';
        endif;

        if ( isset( $staff_social['phila_staff_instagram'] ) ):
          $staff_social_output .= '<a href="' . $staff_social['phila_staff_instagram'] . '" class="social-link"  data-analytics="social"><i class="fab fa-instagram fa-lg" title="Instagram" aria-hidden="true"></i><span class="show-for-sr">Instagram</span></a>';
        endif;

        if ( isset( $staff_social['phila_staff_linkedin'] ) ):
          $staff_social_output .= '<a href="' . $staff_social['phila_staff_linkedin'] . '" class="social-link"  data-analytics="social"><i class="fab fa-linkedin fa-lg" title="LinkedIn" aria-hidden="true"></i><span class="show-for-sr">LinkedIn</span></a>';
        endif;

      endif;

      $staff_leadership = rwmb_meta('phila_leadership', $args = array('type'=>'checkbox'));
    }
    if ( $staff_leadership && $all_staff == 0 ):
      $staff_options = rwmb_meta('phila_leadership_options');

      $staff_display_order = isset($staff_options['phila_display_order']) ? intval($staff_options['phila_display_order']) : 0;

      $staff_summary = isset($staff_options['phila_summary']) ? wpautop($staff_options['phila_summary']) : '';

      $staff_leadership_output .= '<div class="row staff-highlight">';
      // Leadership Thumbnail
      if ( has_post_thumbnail() ):
        $staff_photo = get_the_post_thumbnail( $post->ID, 'staff-thumb', 'class= staff-thumbnail' );
        $staff_leadership_output .= '<div class="small-24 medium-5 columns">' . $staff_photo . '</div>';
      endif;

      // Leadership Contact Info
      $staff_leadership_output .= '<div class="small-24 medium-6 columns staff-contact">';

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
        $staff_leadership_output .= '<div class="email"><a href="mailto:' . $staff_email . '">' . phila_util_return_parsed_email($staff_email) . '</a></div>';
      endif;

      if ( isset( $staff_social_output ) && !$staff_social_output == ''):
        $staff_leadership_output .= '<div class="social">' . $staff_social_output . '</div>';
      endif;

      if ( isset( $staff_summary ) && !$staff_summary == '' ):
        $staff_leadership_output .= '</div>';
        $staff_leadership_output .= '<div class="medium-13 columns staff-summary">';

        if ( strlen( $staff_summary ) > 820 ):
          $staff_leadership_output .=  '<div class="staff-bio expandable" aria-controls="' . sanitize_title_with_dashes($staff_title)  . '-control"' . ' aria-expanded="false">' . apply_filters( 'the_content', $staff_summary) . '</div><a href="#" data-toggle="expandable" class="float-right" id="' .  sanitize_title_with_dashes($staff_title) . '-control' . '"> More + </a>';
        else:
          $staff_leadership_output .= '<div class="staff-bio collapsible">' . apply_filters( 'the_content', $staff_summary) . '</div>';
        endif;
        $staff_leadership_output .= '</div></div>';

      endif;

      if ( key_exists( $staff_display_order, $staff_leadership_array ) ) :
        ++$staff_display_order;
      endif;

      $staff_leadership_array[$staff_display_order] = $staff_leadership_output;

    else:
      $staff_table_output .= '<tr>
        <td class="name"><span class="list-name">' . $staff_member_name_output . '</span></td>
        <td class="title">' . $staff_title . '<br>' . urldecode( $staff_unit ). '</td>';
        if (!empty($staff_email)) :
        $staff_table_output .= '<td class="email"><a href="mailto:' . $staff_email . '">' . $staff_email . '</a></td>';
        else:
          $staff_table_output .= '<td class="email"></td>';
        endif;

        if ( !empty( $staff_phone_unformatted ) && !empty( $staff_phone_formatted ) ):
          $staff_table_output .= '<td class="phone"><a href="tel:' . $staff_phone_unformatted . '">' . $staff_phone_formatted . '</a></td>';
        else :
          $staff_table_output .= '<td class="phone"></td>';
        endif;

        if ( !empty( $staff_social_output ) ) :
          $staff_table_output .= '<td class="social">' . $staff_social_output . '</td></tr>';
        else :
          $staff_table_output .= '<td class="social"></td>';
        endif;
        $staff_table_output .= '</tr>';
    endif;
  endwhile;

  echo '<section class="staff-directory">'; ?>
  <?php if ( isset( $unit ) ): ?>
    <div class="row">
      <div class="columns">
      <h3><?php echo urldecode($unit) ?></h3>
        <?php foreach ( $unit_meta as $meta ) : ?>
          <?php if (urldecode($unit) == $meta['name']) :?>
            <?php if (isset($meta['unit_description'])) :?>
              <div class="unit_desc">
                <?php echo apply_filters('the_content', $meta['unit_description']) ?>
              </div>
            <?php endif ?>
          <?php endif ?>
        <?php endforeach ?>
    </div>
  </div>

  <?php endif; ?>
  <?php if (!empty($staff_leadership_array)):?>
    <div class="row staff-leadership <?php if ( $user_selected_template == 'staff_directory') echo 'mbl'; ?>">
        <div class="large-24 columns">
          <?php if ( $user_selected_template == 'homepage_v2' ) : ?>
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
  <?php endif; ?>
  <!-- Begin Staff Directory Table -->
  <?php if (!$staff_table_output == ''): ?>
    <section class="row mbl all-staff-table">
      <div class="large-24 columns">
      <?php if ($all_staff == 1) : ?>
        <div id="sortable-table-0" class="search-sort-table">
          <div class="search">
            <label for="table-search"><span class="screen-reader-text">Filter staff members by name or title</span></label>
            <input type="text" class="table-search search-field" placeholder="Filter staff members by name or title" />
            <input type="submit" class="search-submit" />
          </div>
        <?php endif ?>
          <?php if ($user_selected_template != 'staff_directory' && $user_selected_template != 'staff_directory_v2') : ?>
            <h2 class="contrast">Staff</h2>
          <?php endif; ?>
          <table role="grid" class="staff responsive js-hide-empty">
            <thead>
              <tr>
                <th scope="col" class="table-sort" data-sort="name">Name</th>
                <th scope="col" class="table-sort" data-sort="title">Job Title</th>
                <th scope="col">Email</th>
                <th scope="col">Phone #</th>
                <th scope="col">Social</th>
              </tr>
            </thead>
            <tbody class="search-sortable">
              <?php echo $staff_table_output;?>
            </tbody>
          </table>
          <div class="no-results">Sorry, there are no results for that search.</div>
          <?php if ($all_staff == 1) : ?>
            <ul class="pagination-wrapper no-js">
              <li class="prev">
                <a class="prev-0" href="#">Previous</a>
              </li>
            <ul class="pagination"></ul>
            <li class="next">
              <a class="next-0" href="#">Next</a>
            </li>
          </ul>
        <?php endif ?>
        </div>
    </section>
  <?php endif; ?>
<?php wp_reset_query();?>
<?php endif; ?>
<?php echo '</section>'; ?>
