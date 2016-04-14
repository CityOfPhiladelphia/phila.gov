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
  );
  $parent_content = get_pages( $this_parent );

  $check_parent_content = $parent_content[0]->post_content;

  $append_before_wysiwyg = rwmb_meta( 'phila_append_before_wysiwyg', $args = array('type' => 'textarea'), $post->ID);
  $append_after_wysiwyg = rwmb_meta( 'phila_append_after_wysiwyg', $args = array('type' => 'textarea'), $post->ID);

  if( $post->post_parent ) {
    //it's a child
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
        <div class="medium-6 columns">
          <aside>
            <ul class="tabs vertical">
              <?php if ( $check_parent_content ) : ?>
                <li class="tabs-title<?php echo ( $current ) ? ' is-active' : ''?>">
                  <a href="<?php echo $parent_link ?>">Overview</a>
                </li>
              <?php endif; ?>
              <?php echo $children; ?>
            </ul>
          </div>
        </aside>
        <div class="medium-18 columns">
          <div data-swiftype-name="body" data-swiftype-type="text" class="entry-content tabs-content vertical">
            <div class="tabs-panel is-active">
              <header class="entry-header">
                <?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
              </header><!-- .entry-header -->

              <!-- If Custom Markup append_before_wysiwyg is present print it -->
              <?php if (!$append_before_wysiwyg == ''):?>
                <?php echo $append_before_wysiwyg; ?>
              <?php endif; ?>

              <?php if ( $parent_content ) : ?>
                
                <?php echo $parent_content ?>

              <?php else : ?>

                <?php the_content(); ?>

              <?php endif; ?>

              <!-- If Custom Markup append_after_wysiwyg is present print it -->
              <?php if (!$append_after_wysiwyg == ''):?>
                <?php echo $append_after_wysiwyg; ?>
              <?php endif; ?>

            </div>
          </div>
        </div><!-- .entry-content -->
      </div>
    </div>
  </article><!-- #post-## -->
</div>
