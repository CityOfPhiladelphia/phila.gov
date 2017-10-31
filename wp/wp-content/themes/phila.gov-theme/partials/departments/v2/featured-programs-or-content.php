<?php
/*
 *
 Featured homepage content
 *
 */
?>
<?php $section_title = rwmb_meta('phila_v2_homepage_featured_section');?>
<!-- Display Featured Pages -->
<section class="mvl">
  <div class="row">
    <div class="columns">
      <h2 class="contrast"> <?php echo ( isset( $section_title['title'] ) ) ? $section_title['title'] : 'Featured programs'; ?> </h2>
    </div>
  </div>
  <section class="row equal-height mbl">
    <?php
      $c = phila_grid_column_counter(count($this->featured));
      foreach ($this->featured as $key => $value):
    ?>
      <article class="large-<?php echo $c ?> medium-24 columns featured-content programs equal">
        <?php
          $post_id = get_post( $this->featured[$key]['phila_featured_page'] );

          $title = (isset($this->featured[$key]['phila_featured_title'])) ? $this->featured[$key]['phila_featured_title'] : $post_id->post_title;

          $image_id = isset( $this->featured[$key]['phila_featured_img'][0] ) ? $this->featured[$key]['phila_featured_img'][0] : '';

          $img = wp_get_attachment_image_src($image_id, $size = 'full');

          $description = isset( $this->featured[$key]['phila_featured_description'] ) ? $this->featured[$key]['phila_featured_description'] : '';
        ?>
          <div class="featured-thumbnail">
            <img src="<?php echo (isset($img) ) ? $img[0] : '' ; ?>" alt="" class="mrm">
          </div>
          <div>
            <header>
              <a href="<?php echo get_permalink($post_id); ?>"><h4 class="h6"><?php echo $title ?></h4></a>
            </header>
            <p><?php echo $description;?></p>
          </div>
        </article>
    <?php endforeach;?>
  </section>
</section>
