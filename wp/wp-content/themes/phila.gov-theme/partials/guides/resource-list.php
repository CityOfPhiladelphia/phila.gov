<?php /*
  *
  * Resource list template
  * 
*/
?>
<?php $print_all = rwmb_meta('guide_print_all');?>
<?php if( $print_all ) :?>
  <div class="guides-print-all-content"> 
    <?php 
      wp_reset_query(); 
      global $post;
      $direct_parent = $post->post_parent;

      $args = array(
        'post_parent' => $direct_parent,
        'post_type'   => 'guides',
        'numberposts' => -1,
        'post_status' => 'any',
        'orderby' => 'menu_order',
        'order'   => 'asc',
      );
      $children = new WP_Query( $args );

      if ( $children->have_posts() ) : ?>
        <?php while ( $children->have_posts() ) : $children->the_post(); ?>
          <?php $heading_groups = rwmb_meta( 'phila_heading_groups' );
            $heading_content = phila_extract_clonable_wysiwyg( $heading_groups, $array_key = 'phila_wywiwyg_alt_heading' ); ?>
            <h1><?php echo get_the_title(); ?></h1>
            <?php include(locate_template('partials/content-heading-groups.php')); ?>
        <?php endwhile; ?>
      <?php endif ;?>
    <?php wp_reset_query(); ?>
  </div>
  <!-- End Guides-print-all-content -->
<?php endif;?>

<?php $resource_lists = rwmb_meta('phila_resource_list_v2'); ?>
<?php foreach($resource_lists as $resource_list) : ?>
  <h2 class="h5 bg-ghost-gray pas">
    <?php echo $resource_list['phila_resource_list_title'] ?>
  </h2>
  <?php foreach ($resource_list['phila_resource_list_items'] as $list ): ?>
  <?php if (isset($list['phila_list_item_type'] )): ?>
    <?php switch ($list['phila_list_item_type']) :
          case 'link':
            $icon = 'link';
            break;
          case 'document':
            $icon = 'file-alt';
            break;
          case 'map':
            $icon = 'map-pin';
            break;
          case 'video':
            $icon = 'play-circle';
            break;
          default: 
            $icon = 'link';
          endswitch;
      ?>
      <?php endif; ?>
    <div class="mbm pas">
      <a href="<?php echo $list['phila_list_item_url']?>" class="<?php echo isset($list['phila_list_item_external']) ? 'external' : ''?>"><i class="fas fa-<?php echo $icon ?> fa-fw fa-lg mrm"></i> <?php echo $list['phila_list_item_title'];?></a>
    </div>
  <?php endforeach; ?>
  
  <?php endforeach; ?>


<?php if( $print_all ) :?>
  <div class="mtxl pas">
    <a href="javascript:window.print()" class="print-entire-guide"><i class="fal fa-print fa-fw fa-lg mrm"></i> Print this entire guide</a>
  </div>
<?php endif; ?>