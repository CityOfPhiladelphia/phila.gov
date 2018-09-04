<?php

/* displays a grouped list of on-site links */

?>
  <?php if (function_exists('rwmb_meta')): ?>
  <?php //set the vars
  $user_selected_template = phila_get_selected_template();
  $resource_list_groups = rwmb_meta( 'phila_resource_list' );

    if ( ! empty( $resource_list_groups ) ) :
      $i=0;
      $total = count($resource_list_groups);
      $count = 0;
      $featured_resources = array();
      $list_item_output = '';

      //group
      foreach ( $resource_list_groups as $resource_list_group ) :
        $i++;

        //assign vars
        $resource_list_title = isset( $resource_list_group['phila_resource_list_title'] ) ? $resource_list_group['phila_resource_list_title'] : '';
        $resource_list_slug = sanitize_title_with_dashes($resource_list_title);

        $list_item_output .= '<div class="row one-quarter-row mvl">
        <section>';

        $list_item_output .= '<div class="columns medium-6"><header><h2 id="' . $resource_list_slug . '" class="h4 phm pvs">' . $resource_list_title . '</h2></header></div>';
        $list_item_output .= '<div class="column medium-16 pbxl">';
        $list_item_output .= '<div class="resource-list">';
        $list_item_output .= '<ul>';

        //items
        $list_items_group = $resource_list_group['phila_resource_list_items'];

        foreach ( $list_items_group as $list_items ) :
          $count++;

          $item_title = isset( $list_items['phila_list_item_title'] ) ? $list_items['phila_list_item_title'] : '';
          $item_url = isset( $list_items['phila_list_item_url'] ) ? $list_items['phila_list_item_url'] : '';
          $item_resource_type = isset( $list_items['phila_list_item_type'] ) ? $list_items['phila_list_item_type'] : '';
          $item_featured = isset( $list_items['phila_featured_resource'] ) ? $list_items['phila_featured_resource'] : 0;
          $item_alt_title = isset( $list_items['phila_list_item_alt_title'] ) ? $list_items['phila_list_item_alt_title'] : '';
          $featured_display_order = isset( $list_items['phila_display_order'] ) ? $list_items['phila_display_order'] : '';
          $featured_summary = isset( $list_items['phila_featured_summary'] ) ? $list_items['phila_featured_summary'] : '';

          switch ($item_resource_type) {
            case ('phila_resource_document'):
              $icon = 'fa-file-text';
              break;

            case ('phila_resource_map'):
              $icon = 'fa-map-marker';
              break;

            case ('phila_resource_link'):
              $icon = 'fa-link';
              break;

            default:
              $icon = 'fa-file-text';
          }

          if ( $item_featured ):
            $featured_output = '';

            $featured_resources[$featured_display_order] = array('title' => $item_title , 'alt-title' => $item_alt_title ,  'url' => $item_url , 'type' => $item_resource_type, 'icon' => $icon , 'summary' => $featured_summary );
          endif;

          if ( $count === 4 ):
            $expand = true;
            $list_item_output .= '<div class="staff-bio expandable">';
          endif;

            $list_item_output .= '<li class="phm pvs clickable-row" data-href="' . $item_url . '"><a href="' . $item_url . '"><i class="fa ' . $icon . ' fa-lg" aria-hidden="true"></i> ' . $item_title . '</a></li>';


        endforeach;

        $list_item_output .=  '</ul></div></div></section></div>'; ?>

      <?php endforeach; ?>

      <!-- Loop featured resources -->
      <?php if ( !empty( $featured_resources ) ):?>

          <?php
          ksort($featured_resources);
          $item_count = count( $featured_resources );
          $columns = phila_grid_column_counter( $item_count );

          // TODO: Validate featured resource order
          //Current Featured Resource limit
          $limit = 4;
          $current_position = 0;
          ?>
          <section class="row mbl <?php if( $item_count > 1 ) echo 'equal-height';?>">

          <div class="columns">
              <h2 class="h3">Featured <?php echo strtolower(the_title()) ?></h2>
          </div>

          <?php
          foreach ($featured_resources as $key => $value): ?>
          <div class="large-<?php echo $columns ?> columns">
            <a href="<?php echo $featured_resources[$key]['url']; ?>"  class="card action-panel">
              <div class="panel <?php if( $item_count > 1 ) echo 'equal';?>">
              <header class="<?php echo $columns == '24' ? 'text-align-left' : ''; ?>">
                <div class="<?php echo $columns == '24' ? 'float-left mrm' : ''; ?>">
                  <span class="fa-stack <?php echo $columns == '24' ? 'fa-3x' : 'fa-4x'; ?> center" aria-hidden="true">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa <?php echo $featured_resources[$key]['icon']; ?> fa-stack-1x fa-inverse"></i>
                  </span>
                </div>
                <?php if (!$featured_resources[$key]['alt-title'] == ''): ?>
                  <span class=""><?php echo $featured_resources[$key]['alt-title']; ?></span>
                <?php elseif (!$featured_resources[$key]['title'] == ''): ?>
                  <span class=""><?php echo $featured_resources[$key]['title']; ?></span>
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
        </section>
      <?php endif; ?>

    <?php if (! empty( $featured_resources ) && count($featured_resources) > 0) : ?>
      <section class="row">
        <div class="column">
          <h2 class="h3 mbn">All <?php echo strtolower(the_title()) ?></h2>
          <hr />
        </div>
      </section>
    <?php endif; ?>

    <div class="one-quarter-layout bdr-dark-gray">
      <?php echo $list_item_output; ?>
    </div>

    <?php endif; ?>

  <?php endif; ?>
