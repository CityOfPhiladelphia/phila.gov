<?php
/**
 * The template used for displaying Staff Directory
 *
 * @package phila-gov
 */
?>
<!-- Staff List -->
<!-- Staff Leadership -->
<!-- End Staff Leadership -->
<!-- All Staff -->
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
        <?php

        $args = array ( 'post_type' => 'staff_directory' );
        query_posts( $args );

        if ( have_posts() ):
            while ( have_posts() ) :
                the_post();

                // Do stuff with the post content.
                the_title();
                the_permalink(); // Etc.
        ?>
        <tr>
          <td><?php echo $staff_first_name . ' ' . $staff_last_name; ?></td>
          <td><?php echo $staff_title; ?></td>
          <td><a href="mailto:<?php echo $staff_email; ?>"><?php echo $staff_email; ?></a></td>
          <td><a href="tel:<?php echo $staff_phone_unformatted; ?>"><?php echo $staff_phone_formatted; ?></a></td>
        </tr>
      <?php
          endwhile;
          else:
              // Insert any content or load a template for no posts found.
          endif;

          wp_reset_query();

        ?>
      </tbody>
    </table>
  </div>
</div>
<!-- End All Staff -->
