<?php
function phila_resource_list_switch($item_resource_type) {
    if ($item_resource_type === 'document') {
        return 'fas fa-file-alt';
    } elseif ($item_resource_type === 'map') {
        return 'fas fa-map-marker-alt';
    } elseif ($item_resource_type === 'link') {
        return 'far fa-link';
    } elseif ($item_resource_type === 'video') {
        return 'fas fa-video';
    } else {
        return 'fas fa-file-alt';
    }
    
}
?>
