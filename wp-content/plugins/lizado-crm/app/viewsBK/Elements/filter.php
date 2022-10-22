<input type="hidden" name="page" value="<?= APP_PLUGIN_PAGE; ?>">
<input type="hidden" name="controller" value="<?= ( empty($_GET['controller']) ) ? 'home' : $_GET['controller']; ?>">
<input type="hidden" name="action" value="<?= ( empty($_GET['action']) ) ? 'index' : $_GET['action']; ?>">

<?php
$categories = get_terms( array(
    'taxonomy' => 'category',
    'hide_empty' => false,
) );

$_GET['q'] =( empty($_GET['q']) ) ? '' : $_GET['q'];

$link_add = AppUrlHelper::build([ 
  'controller' => $_GET['controller'],
  'action' => 'view'
]);
?>
<div class="tablenav top mb-3">

   <div class="alignleft actions">
     
   </div>
   <div class="alignright actions">
    <label class="screen-reader-text" for="post-search-input"><?php _e( 'Search' ); ?>:</label>
    <input type="date" id="post-search-input" name="date" value="<?= $_GET['date']; ?>">
    <input type="search" id="post-search-input" name="q" value="<?= $_GET['q']; ?>">
    <input type="submit" id="search-submit" class="button" value="<?php _e( 'Search' ); ?>">
    
   </div>

   <br class="clear">
</div>

