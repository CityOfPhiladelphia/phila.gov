<?php
/**
 * Guide homepage partial
 *
 * @package phila-gov
 */

$full_row_blog_selected =  rwmb_meta('phila_full_row_blog_selected');
$full_row_blog = rwmb_meta('phila_full_row_blog');
$full_width_press_releases_selected = rwmb_meta('phila_full_row_press_releases_selected');
$full_width_press_releases = rwmb_meta('phila_full_row_press_releases');
$cal_id = rwmb_meta('phila_full_width_calendar_id');
?>
<section>
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell page-title guide-page-title">
        <?php $guide_icon = rwmb_meta('guide_page_icon'); ?>
        <?php $landing_title = rwmb_meta('guide_landing_page_title'); ?>
        <h1>
          <?php echo !empty($guide_icon)  ? '<i class="' . $guide_icon . '"></i>' : '' ?>
          <?php if (phila_get_selected_template($post->ID) === 'guide_landing_page') : ?>
            <?php echo  !empty($landing_title) ? $landing_title : 'Overview' ?>
          <?php else : ?>
            <?php the_title(); ?>
          <?php endif; ?>
        </h1>
      </div>
    </div>
  </div>

  <?php get_template_part('partials/content', 'custom-markup-before-wysiwyg'); ?>

  <?php if (!empty(get_the_content())) : ?>
    <div class="grid-container">
      <div class="grid-x grid-x-padding">
        <div class="cell">
          <div class="intro-text" id="guides-intro-text"><?php the_content(); ?></div>

        </div>
        <div class="cell">
          <div class="intro-button button" id="guides-button-more">More</div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php get_template_part('partials/content', 'custom-markup-after-wysiwyg'); ?>

</section>

<div class="grid-container">
<div class="page-title "><h2>Explore this guide</h2></div>
  <div class="grid-x grid-padding-x guide-landing-nav">
    <?php
    $args = array(
      'post_parent' => $post->ID,
      'post_type'   => 'guides',
      'numberposts' => -1,
      'post_status' => 'any',
      'orderby' => 'menu_order',
      'order'   => 'asc',
    );
    $children = get_children($args);
    ?>
    <?php foreach ($children as $child) : ?>
      <?php
        $link = get_permalink($child->ID);
        $h3 = $child->post_title;
        $description = rwmb_meta('phila_meta_desc', array(), $child->ID);
        $icon = rwmb_meta('guide_page_icon', array(), $child->ID);
        $background = rwmb_meta('guide_color_picker', array(), $child->ID);
        ?>
      <div class="cell medium-7 mbxxl">
        <?php include(locate_template('partials/guides/navigation-card.php')); ?>
      </div>
      <div class="cell medium-1"></div>
    <?php endforeach; ?>

  </div>
 
</div>

<?php if (!empty($full_row_blog_selected)) : ?>
  <?php $blog_override = rwmb_meta('phila_get_post_cats');
    $blog_cat_override = isset($blog_override['phila_post_category']) ? $blog_override['phila_post_category'] : '';
    $blog_tag_override = isset($blog_override['tag']) ? $blog_override['tag'] : '';
    $blog_see_all = isset($blog_override['override_url']) ? $blog_override['override_url'] : ''; ?>

  <!-- Blog Content-->
  <section class="mvl">
    <?php include(locate_template('partials/departments/phila_full_row_blog.php')); ?>
  </section>
  <!-- /Blog Content-->
<?php endif; ?>


<?php if (!empty($full_width_press_releases_selected)) : ?>
  <?php $press_override = rwmb_meta('phila_get_press_cats');
    $press_cat_override = isset($press_override['phila_press_release_category']) ? $press_override['phila_press_release_category'] : '';
    $press_tag_override = isset($press_override['tag']) ? $press_override['tag'] : ''; ?>
  <!-- Press Releases -->
  <div class="row mvl">
    <?php echo do_shortcode('[press-releases posts=5]'); ?>
  </div>
  <!-- /Press Releases -->
<?php endif; ?>

<?php if (!empty($cal_id)) : ?>
  <?php $cal_calendar = rwmb_meta('phila_calendar_owner');
    $calendar_see_all = rwmb_meta('override_url'); ?>
  <!-- Full Width Calendar -->
  <?php include(locate_template('partials/departments/v2/calendar.php')); ?>

  <!-- /Full Width Calendar -->
<?php endif; ?>
<?php
switch ($user_selected_template) {
  case ('phila_one_quarter'):
    get_template_part('partials/departments/v2/content', 'one-quarter');
    break;
  case ('resource_list_v2'):
    include(locate_template('partials/resource-list.php'));
    break;
  case ('collection_page_v2'):
    include(locate_template('partials/departments/v2/collection-page.php'));
    break;
  case ('document_finder_v2'):
    include(locate_template('partials/departments/v2/document-finder.php'));
    break;
} ?>
  <?php get_template_part('partials/departments/content', 'programs-initiatives'); ?>

  <?php get_template_part('partials/content', 'additional'); ?>