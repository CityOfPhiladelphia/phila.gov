<?php
/**
 * The template used for displaying a content collection
 *
 * @package phila-gov
 */

  $walker_menu = new Content_Collection_Walker();

  if( $post->post_parent ) {
    $children = wp_list_pages(array(
      'sort_column' => 'menu_order',
      'title_li' => '',
      'child_of' => $post->post_parent,
      'echo' => 0,
      'walker' => $walker_menu
      )
    );

    $page_title = get_the_title($post->post_parent);
    $parent_title = null;
    $parent_content = null;
    $parent_link = get_permalink( $post->post_parent );
    $current = false;
  }else{

    $parent_link = get_permalink();
    $current = true;
    $parent_title = get_the_title();

    $parent_content = get_the_content();

    $children = wp_list_pages(array(
      'sort_column' => 'menu_order',
      'title_li' => '',
      'child_of' => $post->ID,
      'echo' => 0,
      'walker' => $walker_menu
      )
    );

  }
  ?>
  <div class="data-swiftype-index='true'">
    <div class="row">
      <header class="entry-header small-24 columns">
        <?php if ( $parent_title ) : ?>
          <h1><?php echo $parent_title ?> </h1>
        <?php else : ?>
          <h1><?php echo $page_title; ?></h1>
        <?php endif; ?>
      </header>
    </div>
    <article id="post-<?php the_ID(); ?>">
      <div class="row">
        <div class="small-24 columns">
            <aside>
              <ul class="tabs vertical">
                <li class="tab-title<?php echo ($current) ? ' active' : ''?>">
                  <a href="<?php echo $parent_link ?>">Overview</a></li>
                <?php echo $children; ?>
              </ul>
            </aside>
        <div data-swiftype-name="body" data-swiftype-type="text" class="entry-content tabs-content">
          <div class="content active">
            <header class="entry-header">
              <?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
            </header><!-- .entry-header -->
            <?php if ( $parent_content ) : ?>
              <?php echo $parent_content ?>
            <?php else : ?>
              <?php the_content(); ?>
            <?php endif; ?>
          </div>
        </div><!-- .entry-content -->
      </div>
    </div>
    <?php get_template_part( 'partials/content', 'modified' ) ?>
  </article><!-- #post-## -->
</div>
