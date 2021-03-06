<?php
/*
 *
 * In-page Service Updates
 *
 */
?>

<?php if ( $service_update_page['parent_group'] ): ?>
  <?php $num_items = count($service_update_page['parent_group']);
    $i = 0;?>
    <div class="grid-container"> 
      <div class="grid-x">
        <div class="cell"> 
          <p>Last updated: <?php the_modified_date();?> </p>
          <?php if ( isset( $service_update_page['service_intro'] ) ) :?> 
            <p><?php echo apply_filters('the_content',$service_update_page['service_intro'])?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <div class="grid-container"> 
    <div class="grid-x">
      <div class="cell">
        <p>Jump to:</p>
      </div>
    </div>
  </div>
    <div class="grid-container"> 
      <div class="grid-x">
      <?php foreach ($service_update_page['parent_group'] as $group ) : ?>
        <div class="cell medium-6">
          <p><a href="#<?php echo sanitize_title_with_dashes($group['group_title']) ?>"><?php echo $group['group_title']?></a></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php foreach ($service_update_page['parent_group'] as $group ) : ?>
    <div class="grid-container mvxl"> 
      <div class="grid-x">
        <div class="cell medium-6">
          <h3 id="<?php echo sanitize_title_with_dashes($group['group_title']) ?>"><?php echo $group['group_title']?></h3>
        </div>
        <div class="cell medium-18">
          <table class="service-update">
            <tbody>
            <?php 
            if (strpos($group['group_title'], 'trash') !== false || strpos($group['group_title'], 'recycling') !== false) {
              echo do_shortcode('[trashday-alerts is_in_table=1 icon_text=1 icon_padding=1]');
            }
            ?>
            <?php 
              foreach ($group['service_content'] as $item) :?>
                <tr class="service-update--<?php if ( !$item['level'] == '' ) echo $item['level']; ?> ">
                  <th class="phl-mu">
                    <i class="fa-2x fa-fw <?php if ( $item['icon']  ) echo $item['icon']; ?> service-icon" aria-hidden="true"></i>
                    <span class="icon-label"><?php if ( $item['service_name']) echo $item['service_name']; ?></span>
                  </th>
                  <td class="pam">
                    <?php if ( !$item['message'] == '' ):?>
                      <span>
                        <?php  echo $item['message']; ?>
                      </span>
                    <?php endif;?>
                  </td>
                </tr><!-- row -->
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php if (++$i !== $num_items) :?>
        <hr>
    <?php endif; ?>

    </div>
  <?php endforeach; //row ?>
<?php endif; //?>

