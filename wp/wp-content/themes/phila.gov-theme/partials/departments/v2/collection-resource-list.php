<?php

/* Displays a grouped list of links, with optional featured items */

?>
  <?php if (function_exists('rwmb_meta')): ?>
  <?php
  $user_selected_template = phila_get_selected_template();

  $featured_resources = array();

  foreach ( $resource_list_groups as $resource_list_group ) :
    $list_items_group = $resource_list_group['phila_resource_list_items'];

    foreach ( $list_items_group as $list_items ) :
      $item_title = isset( $list_items['phila_list_item_title'] ) ? $list_items['phila_list_item_title'] : '';
      $item_url = isset( $list_items['phila_list_item_url'] ) ? $list_items['phila_list_item_url'] : '';
      $item_external = isset( $list_items['phila_list_item_external'] ) ? $list_items['phila_list_item_external'] : '' ;
      $item_resource_type = isset( $list_items['phila_list_item_type'] ) ? $list_items['phila_list_item_type'] : '';
      $item_featured = isset( $list_items['phila_featured_resource'] ) ? $list_items['phila_featured_resource'] : 0;
      $item_alt_title = isset( $list_items['phila_list_item_alt_title'] ) ? $list_items['phila_list_item_alt_title'] : '';
      $featured_display_order = isset( $list_items['phila_display_order'] ) ? $list_items['phila_display_order'] : '';
      $featured_summary = isset( $list_items['phila_featured_summary'] ) ? $list_items['phila_featured_summary'] : '';

      $icon = phila_resource_list_switch( $item_resource_type );

    if ( $item_featured ):
      $featured_output = '';

      $featured_resources[$featured_display_order] =
      array('title' => $item_title ,
      'alt-title' => $item_alt_title ,
      'url' => $item_url,
      'external' => $item_external,
      'type' => $item_resource_type,
      'icon' => $icon ,
      'summary' => $featured_summary );  ?>
    <?php endif;?>
  <?php endforeach ?>

<?php endforeach ?>
<section class="mtl">
    <?php if ( !empty( $featured_resources ) ):?>
  <!-- Featured resources -->
      <?php
      ksort($featured_resources);
      $item_count = count( $featured_resources );
      $columns = phila_grid_column_counter( $item_count );

      // TODO: Validate featured resource order
      //Current Featured Resource limit
      $limit = 4;
      $current_position = 0;
      ?>
    <div class="grid-container">

      <div class="grid-x grid-padding-x mbxl <?php if( $item_count > 1 ) echo 'equal-height' ?>">

      <div class="cell">
        <h2 class="h3">Featured <?php echo strtolower( get_the_title() ) ?></h2>
      </div>

        <?php
        foreach ($featured_resources as $key => $value): ?>
        <div class="large-<?php echo $columns ?> cell">
          <a href="<?php echo $featured_resources[$key]['url']; ?>"  class="card action-panel">
            <div class="panel <?php if( $item_count > 1 ) echo 'equal';?>">
            <header class="<?php echo $columns == '24' ? 'text-align-left' : ''; ?>">
              <div class="<?php echo $columns == '24' ? 'float-left mrm' : ''; ?>">
                <span class="fa-stack <?php echo $columns == '24' ? 'fa-3x' : 'fa-4x'; ?> center" aria-hidden="true">
                  <i class="fas fa-circle fa-stack-2x"></i>
                  <i class="<?php echo $featured_resources[$key]['icon']; ?> fa-stack-1x fa-inverse"></i>
                </span>
              </div>
              <?php if (!$featured_resources[$key]['alt-title'] == ''): ?>
                <span class="<?php if ($featured_resources[$key]['external']) echo 'external';?>"><?php echo $featured_resources[$key]['alt-title']; ?></span>
              <?php elseif (!$featured_resources[$key]['title'] == ''): ?>
                <span class="<?php if ($featured_resources[$key]['external']) echo 'external';?>"><?php echo $featured_resources[$key]['title']; ?></span>
              <?php endif; ?>
            </header>
            <?php echo $columns == '24' ? '' : '<hr class="mll mrl">'; ?>
              <span class="details"><?php echo $featured_resources[$key]['summary']; ?></span>
            </div>
          </a>
        </div>
        <?php
          ++$current_position;
          if ($current_position >= 4):
            break;
          endif;
        ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if ( !empty( $featured_resources ) && count($featured_resources) > 0) : ?>
      <div class="grid-container">
        <section class="grid-x">
          <div class="cell">
            <h2 class="h3 ptn">All <?php echo strtolower( get_the_title() ) ?></h2>
          </div>
        </section>
      <hr class="man" />
    </div>
    <?php endif; ?>
      <?php
      if ( ! empty( $resource_list_groups ) ) :
        $outer_loop = 0;
        $total = count($resource_list_groups);

        $last_key = phila_util_is_last_in_array( (array) $resource_list_groups);

        foreach ( $resource_list_groups as $key => $resource_list_group) :

          $outer_loop++;
          $resource_list_title = isset( $resource_list_group['phila_resource_list_title'] ) ? $resource_list_group['phila_resource_list_title'] : '';
          $resource_list_description = isset( $resource_list_group['phila_resource_list_description'] ) ? $resource_list_group['phila_resource_list_description'] : '';
          $resource_list_slug = sanitize_title_with_dashes($resource_list_title); ?>
          <div class="grid-container">
            <div class="grid-x grid-padding-x one-quarter-row mvm">
              <?php if (isset($resource_list_title)) { ?>
                <div class="medium-24"><header><h2 id="<?php echo $resource_list_slug ?>" class="<?php echo !empty( $featured_resources ) ? 'h4' : 'h3'; ?>"><?php echo  $resource_list_title ?></h2></header></div>
              <?php } ?>
              <?php if( isset($resource_list_description )): ?>	
                <div class="medium-24">	
                  <?php echo apply_filters( 'the_content', $resource_list_description) ?>	
                </div>	
              <?php endif;?>	
              <div class="medium-24">
                <?php
                if ( count($resource_list_group['phila_resource_list_items']) > 3 ) : ?>
                  <div class="expandable" aria-controls="<?php echo $resource_list_slug . '-control' ?>" aria-expanded="false">
                <?php endif; ?>
                <div class="resource-list">
                  <ul><?php
                  $list_items_group = $resource_list_group['phila_resource_list_items'];?>
                  <?php foreach ( $list_items_group as $list_items ) :
                    $item_title = isset( $list_items['phila_list_item_title'] ) ? $list_items['phila_list_item_title'] : '';
                    $item_url = isset( $list_items['phila_list_item_url'] ) ? $list_items['phila_list_item_url'] : '';
                    $item_external = isset( $list_items['phila_list_item_external'] ) ? $list_items['phila_list_item_external'] : '';
                    $item_resource_type = isset( $list_items['phila_list_item_type'] ) ? $list_items['phila_list_item_type'] : '';
                    $item_featured = isset( $list_items['phila_featured_resource'] ) ? $list_items['phila_featured_resource'] : 0;
                    $item_alt_title = isset( $list_items['phila_list_item_alt_title'] ) ? $list_items['phila_list_item_alt_title'] : '';
                    $featured_display_order = isset( $list_items['phila_display_order'] ) ? $list_items['phila_display_order'] : '';
                    $featured_summary = isset( $list_items['phila_featured_summary'] ) ? $list_items['phila_featured_summary'] : '';

                    $icon = phila_resource_list_switch( $item_resource_type );
                    
                    ?>

                    <?php if (!empty($item_url)) : ?>
                      <li class="phm pvs clickable-row" data-href="<?php echo $item_url ?>"><a href="<?php echo $item_url ?>" <?php echo ($item_external) ? 'class="external"' : ''?>><i class="<?php echo $icon ?> fa-lg" aria-hidden="true"></i><?php echo $item_title ?></a></li>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php if ( count($resource_list_group['phila_resource_list_items']) > 3 ) : ?>
              </div><a href="#" data-toggle="expandable" class="float-right" id="<?php echo $resource_list_slug . '-control' ?>"> More + </a>
            <?php endif; ?>
          </div>
        </div>
        <?php if ($last_key != $key) : ?>
          <hr class="grid-padding-x mhn">
        <?php endif ?>
      </div>

    <?php endforeach; ?>
    <?php $featured_resources = array(); ?>
  <?php endif; ?>
<?php endif; ?>
</section>
<!-- /Resource list -->
<?php wp_reset_postdata(); ?>