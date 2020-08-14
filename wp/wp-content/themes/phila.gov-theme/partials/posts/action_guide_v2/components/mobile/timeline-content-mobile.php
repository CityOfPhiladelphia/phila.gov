<!-- Timeline content mobile -->
<?php if( isset($current_row[$current_row_option]['phila_timeline_content']) && isset($current_row[$current_row_option]['phila_timeline_content']['timeline-items'])): ?>
  <li class="mbs accordion-item" data-accordion-item>
    <a href="#" class="accordion-title"><?php echo $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_title']; ?></a>
    <div class="phm accordion-content" data-tab-content>
      <?php if( isset($current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_content'])): ?>
        <?php echo apply_filters( 'the_content', $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_content']) ?>
      <?php endif; ?>
      <div class="grid-x grid-margin-x mvl">
        <div class="medium-24 cell pbm">
          <div class="mbl">	
            <?php if( isset($current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_title'] )): ?>	
              <?php $current_row_id = strtolower(str_replace(' ', '-', $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_title']));?>
              <h4 id="<?php echo $current_row_id;?>" class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_title']; ?></h4>	
            <?php endif;?>	
            <?php if( isset($current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_content'] )): ?>	
              <div class="plm">	
                <?php echo apply_filters( 'the_content', $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_content']) ?>	
              </div>	
            <?php endif;?>	
          </div>
          <?php if( isset($current_row[$current_row_option]['phila_timeline_content']) && isset($current_row[$current_row_option]['phila_timeline_content']['timeline-items'])): ?>
            <?php $timeline_items = $current_row[$current_row_option]['phila_timeline_content']['timeline-items']; ?>
            <div class="mbl">
              <div class="plm">
                <!-- Timeline -->
                <div class="timeline">
                  <?php $j = 0; ?>
                  <?php $temp_month = ''; ?>
                  <?php foreach($timeline_items as $item) { ?>
                    <div class="timeline-item row">
                      <?php $item_date = $item['phila_timeline_item_timestamp']; ?>
                      <?php if( DateTime::createFromFormat('m-d-Y', $item['phila_timeline_item_timestamp'])->format('F Y') != $temp_month ) { ?>
                        <?php $temp_month = DateTime::createFromFormat('m-d-Y', $item_date)->format('F Y'); ?>
                        <div class="month-label medium-5 columns" id="<?php echo strtolower(str_replace(' ', '-', $temp_month));?>">
                          <div>
                            <span ><?php echo $temp_month; ?></span>
                          </div>
                        </div>
                      <?php } ?>
                      <div class="timeline-details medium-19 columns timeline-right">
                        <div class="timeline-dot-container <?php echo ($j == 0) ? 'first-dot' : '' ?>">
                          <div class="timeline-dot"></div>
                        </div>
                        <div class="timeline-text">
                          <div class="timeline-month"><?php echo DateTime::createFromFormat('m-d-Y', $item_date)->format('F d');?></div>
                          <div class="timeline-copy"><?php echo do_shortcode(wpautop( $item['phila_timeline_item'] )); ?></div>
                        </div>
                      </div>
                    </div>
                    <?php $j++; ?>
                  <?php } ?>
                </div>
                <!-- /Timeline -->
              </div>
              <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </li>
<?php endif;?>
<!-- /Timeline content mobile -->