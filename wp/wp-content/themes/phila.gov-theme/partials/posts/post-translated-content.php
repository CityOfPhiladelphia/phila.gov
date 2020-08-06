<!-- Translated content -->
<?php 
if ( count( $language_list ) >= 8 ) {
  $language_list_over_7 = array_slice($language_list, 7);
  $language_list = array_slice($language_list, 0, 7, true);
}
?>

<div class="grid-container translations-container">
    <div class="grid-x medium-24 bg-ghost-gray mvl pas translations">
      <span class="border-right phl-mu hide-for-small-only"><i class="fas fa-globe fa-2x"></i></span>
      <ul class="inline-list no-bullet mbn pln">
        <?php foreach ($language_list as $key => $value): ?>
          <li class="phl-mu phs">
            <?php echo ( $value === get_the_permalink() ) ? '' : '<a href="' .  $value . '">' ?><?php echo phila_language_output($key)?><?php echo ($value === get_the_permalink()) ? '' : '</a>' ?>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php if ( isset($language_list_over_7) ) { ?>
        <ul class="dropdown menu" data-dropdown-menu>
          <li>
            <a href="#"></a>
            <ul class="menu">
              <?php foreach ($language_list_over_7 as $key => $value): ?>
                <li class="phl-mu phs">
                  <?php echo ( $value === get_the_permalink() ) ? '' : '<a href="' .  $value . '">' ?><?php echo phila_language_output($key)?><?php echo ($value === get_the_permalink()) ? '' : '</a>' ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>
        </ul>
      <?php } ?>

    </div>
  </div>
  <!-- /Translated content -->