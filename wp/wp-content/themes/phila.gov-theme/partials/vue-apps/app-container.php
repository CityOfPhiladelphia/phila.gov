<?php 
/*
 *
 * Partial for vue app container
 * Required params: $app_title, $app_id
 */
?>
<!--Vuejs-->
<?php if (!empty($app_title) ): ?>
  <div class="grid-container">
    <div class="grid-x">
      <div class="cell small-24">
        <h3><?php echo $app_title ?> </h3>
        </div>
    </div>
  </div>
<?php endif; ?>
<div class="grid-container">
  <div class="grid-x">
    <div class="cell small-24">
    <div id="<?php echo empty($app_id) ? 'vue-app' : $app_id ?>"></div>
    </div>
  </div>
</div>
<!--/Vuejs-->