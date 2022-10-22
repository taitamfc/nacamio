<?php
/**
 * The template for displaying the footer.
 *
 * @package flatsome
 */

global $flatsome_opt;
?>

</main>

<footer id="footer" class="footer-wrapper">
	<!--div class="row row-main">
		<div class="large-12 col">
			<div class="col-inner">
				
			</div>
		</div>
	</div-->
	<?php do_action('flatsome_footer'); ?>
</footer>

</div>
<script>
	jQuery( document ).ready( function(){
		jQuery( '.woocommerce.columns-5 .large-columns-4').removeClass('large-columns-4').addClass('large-columns-5');
		setTimeout( function(){
			jQuery('body').trigger( "scroll" );
			jQuery('body').trigger( "hover" );
		},500);
	});
</script>
<?php wp_footer(); ?>
</body>
</html>
