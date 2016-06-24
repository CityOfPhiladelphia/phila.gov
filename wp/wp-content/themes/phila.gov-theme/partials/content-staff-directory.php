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
<div class="row">
  <div class="large-24 columns">
    <h2 class="contrast">All Staff</h2>
        <?php
        $categories = get_the_category();
        $category_id = $categories[0]->cat_ID;

        // The Staff Directory Loop
        $args = array ( 'orderby' => 'title', 'order' => 'ASC', 'post_type' => 'staff_directory', 'cat' => $category_id );
        $staff_member_loop = new WP_Query( $args );

        if ( $staff_member_loop->have_posts() ):?>
        <!-- Begin Staff Directory Table -->
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

          <?php while ( $staff_member_loop->have_posts() ) :
                $staff_member_loop->the_post();
                if (function_exists('rwmb_meta')){
                  $staff_first_name = rwmb_meta('phila_first_name', $args = array('type'=>'text'));
                  $staff_last_name = rwmb_meta('phila_last_name', $args = array('type'=>'text'));
                  $staff_title = rwmb_meta('phila_job_title', $args = array('type'=>'text'));
                  $staff_email = rwmb_meta('phila_email', $args = array('type'=>'email'));
                  $staff_phone = rwmb_meta('phila_phone', $args = array('type'=>'phone'));
                  $staff_phone_unformatted = $staff_phone['area'] . $staff_phone['phone-co-code'] . $staff_phone['phone-subscriber-number'];
                  $staff_phone_formatted = '(' . $staff_phone['area'] . ') ' . $staff_phone['phone-co-code'] . '-' . $staff_phone['phone-subscriber-number'];
                }
        ?>
        <tr>
          <td><?php echo $staff_first_name . ' ' . $staff_last_name; ?></td>
          <td><?php echo $staff_title; ?></td>
          <td><a href="mailto:<?php echo $staff_email; ?>"><?php echo $staff_email; ?></a></td>
          <td><a href="tel:<?php echo $staff_phone_unformatted; ?>"><?php echo $staff_phone_formatted; ?></a></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
              <!--TODO: "No Staff found" placeholder -->
    <?php endif; ?>

    <?php wp_reset_query();?>
  </div>
</div>
<!-- End All Staff -->
