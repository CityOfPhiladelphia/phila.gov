<!-- Translated content -->
<?php 

// unset english item

if ($key = array_search('english', array_keys($language_list)) !== false) {
  $english_item = $language_list['english'];
  $english_item_key = 'english';
  unset($language_list['english']);
}

// unset active item
if (($key = array_search(get_the_permalink(), $language_list)) !== false) {
  $active_item = $language_list[$key];
  $active_item_key = $key;
  unset($language_list[$key]);
}

// reinsert items
if(isset($active_item) && isset($english_item)) {
  $language_list = array_merge(array($active_item_key => $active_item), $language_list);
  $language_list = array_merge(array($english_item_key => $english_item), $language_list);
}
elseif(isset($english_item)) {
  $language_list = array_merge(array($english_item_key => $english_item), $language_list);
}
elseif(isset($active_item)) {
  $language_list = array_merge(array($active_item_key => $active_item), $language_list);
}

$new_language_list = [];
foreach ($language_list as $key => $value) {
  $language_list_item['language'] = $key;
  $language_list_item['key'] = phila_language_output($key);
  $language_list_item['value'] = $value;
  array_push($new_language_list, $language_list_item); 
}

wp_localize_script( 'phila-scripts', 'phila_language_list', $new_language_list );
?>

<div class="grid-container translations-container">
    <div class="grid-x medium-24 bg-ghost-gray mvl translations">
      <span class="phm globe"><i class="fas fa-globe fa-2x"></i></span>
      <ul id="main-translation-bar" class="inline-list no-bullet mbn pln"></ul>
      <div class="phm phs dropdown-container">
        <ul class="dropdown menu" data-dropdown-menu>
          <li>
            <a href="#" class="dropdown-selector"></a>
            <ul id="dropdown-translation-bar" class="menu"></ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Translated content -->