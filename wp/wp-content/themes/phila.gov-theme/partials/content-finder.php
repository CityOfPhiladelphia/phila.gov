<?php
/**
 * The template used for sortable lists
 *
 * @package phila-gov
 */
?>

<?php
if (is_post_type_archive('department_page')): ?>
  <div id="filter-list">
    <form>
        <input class="search" type="text" placeholder="Filter results...">
    </form>
  <?php elseif (is_tax('topics')) : ?>
    <div id="filter-list">
      <h2>  <?php display_current_selected_topic(); ?> </h2>
  <?php endif; ?>
