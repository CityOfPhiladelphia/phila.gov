<?php
/*
 * Press release grid for projects
*/
?>
<?php $press_categories = isset($category) ? $category : '';
$current_cat = get_category($category);
$slang_name = html_entity_decode(trim(phila_get_owner_typography($current_cat))); ?>

<?php $press_tag = isset($tag) ? $tag : ''; ?>

<?php

if (!empty($tag)) {

  $press_release_template_args  = array(
    'posts_per_page' => 4,
    'post_type' => array('post'),
    'orderby' => 'post_date',
    'tag_id'  => (int) $tag,
    'ignore_sticky_posts' => 1,
    'meta_query'  => array(
      'relation' => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'press_release',
        'compare' => '=',
      ),
    )
  );
} else {
  $press_release_template_args  = array(
    'posts_per_page' => 4,
    'post_type' => array('post'),
    'order' => 'desc',
    'orderby' => 'post_date',
    'ignore_sticky_posts' => 1,
    'cat' => $press_categories,
    'meta_query'  => array(
      'relation' => 'AND',
      array(
        'key' => 'phila_template_select',
        'value' => 'press_release',
        'compare' => '=',
      ),
    ),
  );
}
?>

<?php
$user_selected_template = phila_get_selected_template();
$post_type_parent = get_post_type($post->ID);
?>

<?php
//special handling for old press release CPT
$result = new WP_Query($press_release_template_args);
$result->post_count = count($result->posts);
?>

<?php $label = 'press_release';
$label_arr = phila_get_post_label($label);
$article_classes = 'flex-child-auto '; ?>

<div class="project-press-release press-grid<?php echo (is_page_template()) ? "" : ' pbxxl mtxl' ?>">
  <div class="grid-container">
    <?php if ($result->have_posts()) : ?>
      <?php include(locate_template('partials/posts/press-release-translated-langs-see-all.php')); ?>
      <?php while ($result->have_posts()) : $result->the_post(); ?>
        <?php $post_type = get_post_type(); ?>
        <?php $post_obj = get_post_type_object($post_type); ?>
        <div class="grid-full-height">
          <div class="cell medium-12 align-self-stretch mbm">
            <article id="post-<?php the_ID(); ?>" <?php post_class("type-press_release"); ?>>
              <a href="<?php get_permalink() ?>" class="card card--press_release pam">
                <div class="grid-x flex-dir-column card--content">
                  <div class="cell align-self-top post-label post-label--press_release">
                    <header class="mbl">
                      <h1 style="text-decoration: underline; color: #0f4d90;"><?php echo get_the_title(); ?></h1>
                    </header>
                  </div>
                  <div class="cell align-self-bottom">
                    <div class="post-meta">
                      <span class="date-published" style="text-decoration: underline; color: #0f4d90;"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date(); ?></time></span>
                    </div>
                  </div>
                </div>
              </a>
            </article>
          </div>
        </div>
      <?php endwhile; ?>
      <?php 
       $see_all = array(
        'URL' => '/the-latest/archives/?templates=press_release&department=' . $slang_name,
        'content_type' => 'press_release',
        'nice_name' => 'Press releases',
      ); 
      include(locate_template('partials/content-see-all.php')); ?>
    <?php else : ?>
      <div class="cell medium-24">
        <p><?php echo esc_html__('No press releases found.', 'phila-gov'); ?></p>
      </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </div>
</div>