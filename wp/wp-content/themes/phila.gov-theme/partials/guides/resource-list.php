<?php /*
  *
  * Resource list template
  * 
*/
$resource_lists = rwmb_meta('phila_resource_list_v2');
?>
<?php foreach($resource_lists as $resource_list) : ?>
  <h2 class="h5 bg-ghost-gray pas">
    <?php echo $resource_list['phila_resource_list_title'] ?>
  </h2>
  <?php foreach ($resource_list['phila_resource_list_items'] as $list ): ?>
  <?php if (isset($list['phila_list_item_type'] )): ?>
    <?php switch ($list['phila_list_item_type']) :
          case 'link':
            $icon = 'link';
            break;
          case 'document':
            $icon = 'file-alt';
            break;
          case 'map':
            $icon = 'map-pin';
            break;
          case 'video':
            $icon = 'play-circle';
            break;
          default: 
            $icon = 'link';
          endswitch;
      ?>
      <?php endif; ?>
    <div class="mbm pas">
      <a href="<?php echo $list['phila_list_item_url']?>" class="<?php echo isset($list['phila_list_item_external']) ? 'external' : ''?>"><i class="fas fa-<?php echo $icon ?> fa-fw fa-lg mrm"></i> <?php echo $list['phila_list_item_title'];?></a>
    </div>
  <?php endforeach; ?>
  
  <?php endforeach; ?>


