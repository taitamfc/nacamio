<?php
$flashs = @$_SESSION['app_mvc_flash'];
if( $flashs ){
	foreach ($flashs as $type => $msg) {
		?>
		<div class="alert alert-<?= $type; ?> alert-dismissible" role="alert">
		  <?= $msg; ?>
		</div>
		<?php
	}
	?>
	<script type="text/javascript">
		setTimeout(function(){ 
			jQuery('.alert-dismissible').fadeOut('slow');
		}, 3000);
		
	</script>
	<?php
}
unset($_SESSION['app_mvc_flash']);