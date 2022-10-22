<input type="hidden" name="page" value="<?= APP_PLUGIN_PAGE; ?>">
<input type="hidden" name="controller" value="<?= ( empty($_GET['controller']) ) ? 'home' : $_GET['controller']; ?>">
<input type="hidden" name="action" value="<?= ( empty($_GET['action']) ) ? 'index' : $_GET['action']; ?>">


<div class="tablenav top mb-3">

   <div class="alignleft actions">
   <span v-html="ajax_msg"></span>
   </div>
   <div class="alignright actions">
    <label class="screen-reader-text" for="post-search-input"><?php _e( 'Search' ); ?>:</label>
	
   </div>

   <br class="clear">
</div>

