<?php
/**
 * The template used for displaying a content collection
 *
 * @package phila-gov
 */

  $walker_menu = new Content_Collection_Walker();
  $has_parent = get_post_ancestors( $post );

  $this_parent = array(
    'include' => $has_parent,
    'post_type' => get_post_type()
  );
  $parent_exists = get_pages( $this_parent );

  if( $post->post_parent ) {
    //it's a child
    $children = wp_list_pages(array(
      'sort_column' => 'menu_order',
      'title_li' => '',
      'child_of' => $post->post_parent,
      'echo' => 0,
      'walker' => $walker_menu,
      'post_type' => get_post_type()
      )
    );

    $page_title = get_the_title($post->post_parent);
    $parent_link = get_permalink( $post->post_parent );

  }else{
    //it's a parent with content
    $parent_link = get_permalink();
    $current = true;
    $parent_title = get_the_title();

    $parent_content = get_the_content();

    $children = wp_list_pages(array(
      'sort_column' => 'menu_order',
      'title_li' => '',
      'child_of' => $post->ID,
      'echo' => 0,
      'walker' => $walker_menu,
      'post_type' => get_post_type()
      )
    );
  }
?>
<?php $user_selected_template = phila_get_selected_template(); ?>

<article id="post-<?php the_ID(); ?>">
<div class="row">
  <header class="entry-header small-24 columns">
      <?php if ( isset( $parent_title ) ) : ?>
        <h1 class="contrast"><?php echo $parent_title ?></h1>
      <?php else : ?>
        <h1 class="contrast"><?php echo $page_title; ?></h1>
      <?php endif; ?>
    </header>
  </div>
  <div class="row">
    <div class="medium-6 columns">
      <aside>
        <ul class="tabs vertical">
          <?php if ( count($parent_exists) > 1 || !empty( $parent_exists[0]->post_content ) ) : ?>
            <li class="tabs-title<?php echo isset( $current ) ? ' is-active' : ''?>">
              <a href="<?php echo $parent_link ?>">Overview</a>
            </li>
          <?php endif; ?>
          <?php echo $children; ?>
        </ul>
      </aside>
    </div>
    <div class="medium-18 columns">
      <div data-swiftype-index='true' data-swiftype-name="body" data-swiftype-type="text" class="entry-content tabs-content vertical">
        <div class="tabs-panel is-active">
          <header class="entry-header">
            <?php the_title( '<h2 class="entry-title mvn">', '</h2>' ); ?>
          </header><!-- .entry-header -->
          <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>

          <?php if ( isset( $parent_content ) ) : ?>
            <?php echo $parent_content ?>
          <?php else : ?>
            <?php the_content(); ?>
          <?php endif; ?>

          <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

        </div>
      </div>
    </div>
  </div>
</article><!-- #post-## -->
