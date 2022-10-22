<?php 
$link_index = AppUrlHelper::build([
	'controller' => $_GET['controller'],
	'action'	 => (empty($_GET['action'])) ? 'index' : $_GET['action'],
]);
?>
<div class="tablenav bottom">
	<div class="tablenav-pages">
		<span class="pagination-links">
			<?php if( $items['page'] > 1 ):?>
			<a class="prev-page button" href="<?= $link_index; ?>&pageno=<?= $items['page'] - 1;?>">
				<span class="screen-reader-text">Previous page</span>
				<span aria-hidden="true">‹</span>
			</a>
			<?php endif;?>
			<span class="screen-reader-text">Current Page</span>
			<span id="table-paging" class="paging-input">
				<span class="tablenav-paging-text">Page <?= $items['page']; ?> of 
					<span class="total-pages"><?= $items['total_pages']; ?></span>
				</span>
			</span>
			<?php if( $items['page'] < $items['total_pages'] ):?>
			<a class="next-page button" href="<?= $link_index; ?>&pageno=<?= $items['page'] + 1;?>">
				<span class="screen-reader-text">Next page</span>
				<span aria-hidden="true">›</span>
			</a>
			<?php endif;?>
		</span>
	</div>
</div>