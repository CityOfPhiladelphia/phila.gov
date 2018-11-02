<div class="aqi grid-container">
  <div class="grid-x pal">
    <h2 class="aqi-status-title">Current conditions in Philadelphia</h2>
    <div class="cell medium-12 pal status-section">
      <div class="aqi-status-time"></div>
      <h3 class="aqi-status-name"></h3>
      <div class="aqi-status-description"></div>
    </div>
    <div class="cell medium-12">
      <!-- Forcing the gauge label to be hidden, then it will be displayed using javascript animation latter on -->
      <style>
        .highcharts-label {
          opacity: 0;
          transition: opacity 0.25s linear;
          -webkit-transition: opacity 0.25s linear 0.5s;
          -moz-transition: opacity 0.25s linear 0.5s;
          -ms-transition: opacity 0.25s linear 0.5s;
          -o-transition: opacity 0.25s linear 0.5s;
        }
      </style>
      <div id="aqi-gauge" style="min-width: 310px; max-width: 400px; height: 300px; margin: 0 auto;"></div>
    </div>
  </div>
</div>
<div class="grid-container no-padding mtl">
  <div class="aqi-info-title">
    <h3 class="mbn">AQI basics for ozone and particle pollution</h3>
  </div>
  
    <?php if (!empty($scale)):?>
      <?php foreach($scale as $item):?>
      <?php
      $color_slug = sanitize_title($item['color']['name']);
      $label_slug = sanitize_title($item['label']);
      ?>
      <div class="grid-x grid-padding-x align-middle">
        <div class="cell medium-4">
          <div class="aqi-info-color aqi-<?php echo $color_slug; ?>">
            <?php echo $item['color']['name'];?>
          </div>
        </div>
        <div class="cell medium-18">
          <div class="aqi-description">
            <h4><?php echo $item['label'];?> &mdash; <?php echo $item['range'];?></h4>
            <p class="aqi-status-<?php echo $label_slug; ?>"><?php echo $item['desc'];?></p>
          </div>
        </div>
      </div>
      <hr>
      <?php endforeach;?>
    <?php endif;?>
  
</div>