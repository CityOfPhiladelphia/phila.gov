<?php 
function phila_resource_list_switch( $item_resource_type ){

  switch ($item_resource_type) {
    case ('phila_resource_document'):
      return 'fas fa-file-alt';
      break;

    case ('phila_resource_map'):
      return 'fas fa-map-marker-alt';
      break;

    case ('phila_resource_link'):
      return 'far fa-link';
      break;

    case ('phila_resource_video'):
      return 'fas fa-video';
      break;

    default:
      return 'fas fa-file-alt';
  }
}
?>
