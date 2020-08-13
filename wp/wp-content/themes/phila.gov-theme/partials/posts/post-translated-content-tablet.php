<!-- Translated content -->
<?php 
$language_list_tablet = $language_list;

// unset english item
if ($key = array_search('english', array_keys($language_list_tablet)) !== false) {
  $english_item = $language_list_tablet['english'];
  $english_item_key = 'english';
  unset($language_list_tablet['english']);
}

// unset active item
if (($key = array_search(get_the_permalink(), $language_list_tablet)) !== false) {
  $active_item = $language_list_tablet[$key];
  $active_item_key = $key;
  unset($language_list_tablet[$key]);
}

// reinsert items
if(isset($active_item) && isset($english_item)) {
  $language_list_tablet = array_merge(array($active_item_key => $active_item), $language_list_tablet);
  $language_list_tablet = array_merge(array($english_item_key => $english_item), $language_list_tablet);
}
elseif(isset($english_item)) {
  $language_list_tablet = array_merge(array($english_item_key => $english_item), $language_list_tablet);
}
elseif(isset($active_item)) {
  $language_list_tablet = array_merge(array($active_item_key => $active_item), $language_list_tablet);
}

if ( count( $language_list_tablet ) >= 9 ) {
  $language_list_overflow = array_slice($language_list_tablet, 4);
  $language_list_tablet = array_slice($language_list_tablet, 0, 4, true);
}
?>

<div class="grid-container translations-container">
    <div class="grid-x medium-24 bg-ghost-gray mvl translations">
      <span class="phm globe"><i class="fas fa-globe fa-2x"></i></span>
      <ul class="inline-list no-bullet mbn pln">
        <?php foreach ($language_list_tablet as $key => $value): ?>
          <?php echo ( $value === get_the_permalink() ) 
            ? '<li class="phm phs active">' . phila_language_output($key) .'</li>' 
            : '<li class="phm phs"><a class="translation-link" href="' .  $value . '">' . phila_language_output($key) . '</a></li>' ?>
        <?php endforeach; ?>
      </ul>
      <?php if ( isset($language_list_overflow) ) { ?>
        <div class="phm phs dropdown-container">
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
        </div>
      <?php } ?>

    </div>
  </div>
  <!-- /Translated content -->