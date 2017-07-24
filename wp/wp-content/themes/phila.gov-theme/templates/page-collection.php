<?php
/**
 * The template used for displaying a content collection
 *
 * @package phila-gov
 */

  $parent_anc = get_post_ancestors ( $post );
  $parent_id = array_pop( $parent_anc );
  $parent_title = get_the_title( $parent_id );
  if ( empty( $parent_id ) ) {
    $parent_id = $post->ID;
  }
?>
<div id="post-<?php the_ID(); ?>">
  <div class="row">
    <header class="small-24 columns">
      <?php if ( isset( $parent_title ) ) : ?>
        <h1 class="contrast"><?php echo $parent_title ?></h1>
      <?php else : ?>
        <h1 class="contrast"><?php echo $page_title; ?></h1>
      <?php endif; ?>
    </header>
  </div>
  <div class="row">
    <div class="side-menu medium-7 columns bdr-right bdr-sidewalk equal hide-for-small-only pbxl">
        <nav data-swiftype-index="false" id="side-nav">
          <ul id="menu-<?php echo sanitize_title( $parent_title )?>" class="vertical menu">
          <?php $args = array(
              'post_type' => 'page',
              'sort_column' => 'menu_order, title',
              'order' => 'ASC',
              'title_li' => '',
              'child_of'  => $parent_id,
              'link_before' => '<span>',
              'link_after'  => '</span>',
            );
            wp_list_pages($args); ?>
        </ul>
      </nav>
    </div>
  <div class="medium-16 columns equal pbxl">
    <article>
      <header class="entry-header">
        <h2><?php echo ( $parent_title != get_the_title() ) ?  get_the_title() : '' ?></h2>
      </header><!-- .entry-header -->
      <div data-swiftype-index='true' data-swiftype-name="body" data-swiftype-type="text" class="entry-content">
        <?php get_template_part( 'partials/content', 'default' ); ?>
      </div>
    </article>
    </div>
  </div>
</div>
