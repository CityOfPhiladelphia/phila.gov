<!-- Translated content -->
<?php 
if ( count( $language_list ) >= 9 ) {
  $language_list_overflow = array_slice($language_list, 8);
  $language_list = array_slice($language_list, 0, 8, true);
}
?>

<div class="grid-container translations-container">
    <div class="grid-x medium-24 bg-ghost-gray mvl translations">
      <span class="phm-mu globe"><i class="fas fa-globe fa-2x"></i></span>
      <ul class="inline-list no-bullet mbn pln">
        <?php foreach ($language_list as $key => $value): ?>
          <?php echo ( $value === get_the_permalink() ) 
            ? '<li class="phm-mu phs active">' . phila_language_output($key) .'</li>' 
            : '<li class="phm-mu phs"><a class="translation-link" href="' .  $value . '">' . phila_language_output($key) . '</a></li>' ?>
        <?php endforeach; ?>
        <?php if ( isset($language_list_overflow) ) { ?>
          <li class="phm-mu phs">
            <ul class="dropdown menu" data-dropdown-menu>
              <li>
                <a href="#" class="dropdown-selector"></a>
                <ul class="menu">
                  <?php foreach ($language_list_overflow as $key => $value): ?>
                    <?php echo ( $value === get_the_permalink() ) 
                      ? '<li class="phs active">' . phila_language_output($key) .'</li>' 
                      : '<li class="phs"><a class="translation-link" href="' .  $value . '">' . phila_language_output($key) . '</a></li>' ?>
                  <?php endforeach; ?>
                </ul>
              </li>
            </ul>
          </li>
        <?php } ?>
      </ul>

    </div>
  </div>
  <!-- /Translated content -->