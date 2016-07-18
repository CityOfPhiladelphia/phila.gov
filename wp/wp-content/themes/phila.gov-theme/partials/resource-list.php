<?php

/* displays a grouped list of on-site links */

?>
<?php if (function_exists('rwmb_meta')): ?>

  <?php //set the vars
  $resource_list_groups = rwmb_meta( 'phila_resource_list' );

    //we require at least three
    if ( ! empty( $resource_list_groups ) && count($resource_list_groups) >= 3 ) :
      $count = 0;
      $featured_resources = array();
      $list_item_output = '';

      //group
      foreach ( $resource_list_groups as $resource_list_group ) :

        if ( $count == 0 ) :
        $list_item_output .= '<div class="row">';
        endif;

        //assign vars
        $count++;
        $resource_list_title = isset( $resource_list_group['phila_resource_list_title'] ) ? $resource_list_group['phila_resource_list_title'] : '';

        $list_item_output .= '<div class="medium-8 small-24 column resource-list end">';
        $list_item_output .= '<header><h2 class="h4 phm pvs">' . $resource_list_title . '</h2></header>';
        $list_item_output .= '<ul>';

        //items
        $list_items_group = $resource_list_group['phila_resource_list_items'];

        foreach ( $list_items_group as $list_items ) :

          $item_title = isset( $list_items['phila_list_item_title'] ) ? $list_items['phila_list_item_title'] : '';
          $item_url = isset( $list_items['phila_list_item_url'] ) ? $list_items['phila_list_item_url'] : '';
          $item_resource_type = isset( $list_items['phila_list_item_type'] ) ? $list_items['phila_list_item_type'] : '';
          $item_featured = isset( $list_items['phila_featured_resource'] ) ? $list_items['phila_featured_resource'] : 0;
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

            $featured_resources[$featured_display_order] = array('title' => $item_title , 'url' => $item_url , 'type' => $item_resource_type, 'icon' => $icon , 'summary' => $featured_summary );
          endif;

          $list_item_output .= '<li class="phm pvs clickable-row" data-href="' . $item_url . '"><a href="' . $item_url . '"><div><i class="fa ' . $icon . ' fa-lg" aria-hidden="true"></i></div> <div>' . $item_title . '</div></a></li>';

        endforeach;

        $list_item_output .=  '</ul></div>'; ?>

      <?php if ( $count == 3 ) :
      $list_item_output .= '</div>';
      $count = 0;
      endif; ?>

      <?php endforeach; ?>

      <!-- Loop featured resources -->
      <?php if ( !empty( $featured_resources ) ):?>
        <div class="row">
          <div class="columns">
              <h2 class="contrast">Featured Resources</h2>
          </div>
        </div>
              <?php
              ksort($featured_resources);
              $item_count = count( $featured_resources );
              $columns = phila_grid_column_counter( $item_count );
              
              // TODO: Validate featured resource order
              //Current Featured Resource limit
              $limit = 4;
              $current_position = 0;
              ?>
              <div class="row <?php if( $item_count > 1 ) echo 'equal-height';?>">

              <?php
              foreach ($featured_resources as $key => $value): ?>
              <div class="large-<?php echo $columns ?> columns">
                <a href="<?php echo $featured_resources[$key]['url']; ?>"  class="action-panel">
                  <div class="panel <?php if( $item_count > 1 ) echo 'equal';?>">
                  <header class="<?php echo $columns == '24' ? 'text-align-left' : ''; ?>">
                    <div class="<?php echo $columns == '24' ? 'float-left mrm' : ''; ?>">
                      <span class="fa-stack <?php echo $columns == '24' ? 'fa-3x' : 'fa-4x'; ?> center" aria-hidden="true">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa <?php echo $featured_resources[$key]['icon']; ?> fa-stack-1x fa-inverse"></i>
                      </span>
                    </div>
                  <?php if (!$featured_resources[$key]['title'] == ''): ?>
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
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="columns">
            <h2 class="contrast">Resources</h2>
        </div>
      </div>
      <?php echo $list_item_output; ?>


    <?php else : ?>
      <div class="row">
        <div class="columns">
          <?php echo 'Please enter at least three groups of links.'; ?>
        </div>
      </div>
    <?php endif; ?>

  <?php endif; ?>
</div>
