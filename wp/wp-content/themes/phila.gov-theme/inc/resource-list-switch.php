<?php
function phila_resource_list_switch($item_resource_type) {
    if ($item_resource_type === 'document' || $item_resource_type === 'Document') {
        return 'fas fa-file-alt';
    } elseif ($item_resource_type === 'map' || $item_resource_type === 'Map') {
        return 'fas fa-map-marker-alt';
    } elseif ($item_resource_type === 'link' || $item_resource_type === 'Link') {
        return 'far fa-link';
    } elseif ($item_resource_type === 'video' || $item_resource_type === 'Video') {
        return 'fas fa-video';
    } else {
        return 'fas fa-file-alt';
    }
    
}
?>
