<?php

/**
 * Template for displaying single project pages.
 *
 * @package phila.gov_theme
 */
get_header();

include(locate_template('partials/projects/project-header.php'));

include(locate_template('partials/departments/phila_module_row_1.php'));

include(locate_template('partials/departments/v2/full-width-call-to-action.php'));

include(locate_template('partials/projects/project-get-involved.php'));

$cal_id = rwmb_meta('phila_full_width_calendar_id');
$owner = rwmb_meta('phila_calendar_owner');
$cal_category = !empty($owner) ? $owner->name : '';

include(locate_template('partials/departments/v2/calendar.php'));

include(locate_template('partials/projects/project-posts.php'));

include(locate_template('partials/projects/project-press-release.php')); ?>

<div class="mvl">
    <?php include(locate_template('partials/departments/phila_call_to_action_multi.php')); ?>
</div>

<?php include(locate_template('partials/departments/phila_staff_directory_listing.php')); ?>

<?php include(locate_template('partials/projects/project-partner-list.php')); ?>

<?php get_footer(); ?>