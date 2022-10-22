<div class="product-container 123">
	<div class="product-main">
		<div class="row content-row mb-0">
			<!-- start product-left -->
			<div class="product-gallery col large-7">
				<?php
					/**
					 * woocommerce_before_single_product_summary hook
					 *
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action( 'woocommerce_before_single_product_summary' );
				?>
				
				<div class="product-custom-left hide-for-small">
					<?php include( dirname(__FILE__).'/../same-design.php' );?>

					<div class="md-bought-together-wrapper">

					</div>
				</div>
			</div>
			<!-- end product-left -->

			<!-- start product-right -->
			<div class="product-info summary col-fit col entry-summary <?php flatsome_product_summary_classes();?>">
				<?php flatsome_sticky_column_open(); ?>
				<div class="product-stacked-info">
					<?php if(!get_theme_mod('product_header') && get_theme_mod('product_next_prev_nav',1)) { ?>
					<div class="product-stacked-next-prev-nav absolute top right hide-for-medium">
						<?php  flatsome_product_next_prev_nav('nav-right'); ?>
					</div>
					<?php } ?>

					<?php
						/**
						 * woocommerce_single_product_summary hook
						 *
						 * @hooked woocommerce_template_single_title - 5
						 * @hooked woocommerce_template_single_rating - 10
						 * @hooked woocommerce_template_single_price - 10
						 * @hooked woocommerce_template_single_excerpt - 20
						 * @hooked woocommerce_template_single_add_to_cart - 30
						 * @hooked woocommerce_template_single_meta - 40
						 * @hooked woocommerce_template_single_sharing - 50
						 */
						do_action( 'woocommerce_single_product_summary' );
					?>
						
					<div class="product-custom-info">
						<div class="product-detail-row flex-b align-c product-detail-addon" style="">
							<img class="p-2" src="<?= home_url();?>/wp-content/uploads/images/guarantee.webp" alt="" style="padding: 2px" width="60px" height="60px">
							<div>
								<div class="product-row-text addition-head">Don’t love it? We’ll fix it. For free.</div>
								<a class="product-row-text addition-head" href="<?= get_the_permalink(837);?>" target="_blank" style="color: var(--secondary)">
								Perfect Fit Guarantee »
								</a>
							</div>
						</div>
						
						<div class="product-detail-row product-detail-addon product-size-guide flex-b align-c">
							<img class="product-row-icon" src="<?= home_url();?>/wp-content/uploads/images/3d-printer.svg">
							<?= do_shortcode('[button class="btn-open-size-guide product-row-text addition-head" text="View size guide" link="#sizeGuideModal"]'); ?>							
							<?= do_shortcode('[button class="btn-open-style-info hidden hide d-none" text="View size guide" link="#styleInfoModal"]'); ?>							
						</div>
						
						<div class="product-detail-row product-delivery flex-b align-s" style="">
							<img class="product-row-icon" src="<?= home_url();?>/wp-content/uploads/images/shipped.svg" alt="">
							<div class="product-row-text flex-b column">
								<span class="addition-head">Deliver to&nbsp;<span class="delivery-country-name">Viet Nam</span></span>
								<div class="shipping-info-calculate">
									<p style="margin: 10px 0; font-size: 14px; font-weight: 400;">
										<span style="text-transform: capitalize; font-weight: 600;">
										standard</span> between <?= date('M. d');?> - <?= date('M. d',strtotime('+13 days'));?>
									</p>
								</div>
							</div>
						</div>
						
						<div class="product-detail-row flex-b align-c" style="">
							<img class="product-row-icon p-2" src="<?= home_url();?>/wp-content/uploads/images/exchange.webp" alt="" style="padding: 2px">
							<a class="product-row-text addition-head" href="<?= get_the_permalink(11);?>" target="_blank">
								Refund &amp; Exchange
							</a>
						</div>
						
						<div class="submit-a-ticket">
							Having trouble? <a class="submit-a-ticket-link" target="_blank" href="<?= get_the_permalink(268);?>">Submit a ticket</a> and we will get back to you!
						</div>

					</div>
						
				</div>
				<?php flatsome_sticky_column_close(); ?>
				<!-- end product-right -->
			</div>

			<div id="product-sidebar" class="mfp-hide">
				<div class="sidebar-inner">
					<?php
					do_action( 'flatsome_before_product_sidebar' );
					/**
					 * woocommerce_sidebar hook
					 *
					 * @hooked woocommerce_get_sidebar - 10
					 */
					if ( is_active_sidebar( 'product-sidebar' ) ) {
						dynamic_sidebar( 'product-sidebar' );
					} else if ( is_active_sidebar( 'shop-sidebar' ) ) {
						dynamic_sidebar( 'shop-sidebar' );
					}
					?>
				</div>
			</div>

		</div>
	</div>

	<div class="product-footer">
		<div class="container">
			<?php
				/**
				 * woocommerce_after_single_product_summary hook
				 *
				 * @hooked woocommerce_output_product_data_tabs - 10
				 * @hooked woocommerce_upsell_display - 15
				 * @hooked woocommerce_output_related_products - 20
				 */
				do_action( 'woocommerce_after_single_product_summary' );
			?>
		</div>
	</div>
</div>
<?php
	$guide_content 	= apply_filters( 'the_content' , get_post(394)->post_content );
	echo do_shortcode('[lightbox id="sizeGuideModal" width="1000px" padding="20px"]'.$guide_content.'[/lightbox]'); 
	
	$guide_content 	= apply_filters( 'the_content' , get_post(440)->post_content );
	echo do_shortcode('[lightbox id="styleInfoModal" width="1000px" padding="20px"]'.$guide_content.'[/lightbox]'); 
?>
<template id="templateStyleInfo">
<a id="btnOpenStyleInfo" href="#styleInfoModal" class="openTheModal" >
	<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
		<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z">
		</path>
	</svg>
</a>
</template>
<template id="templateSizeGuide">
<a id="btnOpenSizeGuide" href="#sizeGuideModal" class="openTheModal">
	<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
		<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z">
		</path>
	</svg>
</a>
</template>
<?php
$object = WoocommerceHelper::get_system_product( get_the_ID() );
$size_map_price = $object->size_map_price;
$size_map_price = json_encode($size_map_price);
global $product;
$product_price = $product->get_price();
?>

<script>
	jQuery( document ).ready( function(){

		var size_map_price = <?= $size_map_price;?>;
		var product_price = <?= $product_price;?>;
		
		function add_price_to_options(){
			for (const the_size in size_map_price) {
				var size_price = product_price + parseInt(size_map_price[the_size]);
				jQuery('#pa_size').find('option[value="'+the_size+'"]').text( the_size + ' ($' + size_price+')' );
			}
			
			let the_price = jQuery('.woocommerce-variation.single_variation .woocommerce-variation-price').text();
			the_price = '$' + the_price;
			jQuery('.woocommerce-variation.single_variation .woocommerce-variation-price').html(the_price);
		}
		
		
		setTimeout( function(){
			jQuery('label[for="pa_style"]').append( jQuery('#templateStyleInfo').html() );	
			jQuery('label[for="pa_size"]').append( jQuery('#templateSizeGuide').html() );
			
			add_price_to_options();
			
			jQuery('#btnOpenStyleInfo').on('click',function(){
				jQuery('.btn-open-style-info').trigger('click');
			});
			jQuery('#btnOpenSizeGuide').on('click',function(){
				jQuery('.btn-open-size-guide').trigger('click');
			});
			
		},700 );

		jQuery( 'body' ).on('change','#pa_size',function(){
			add_price_to_options();
		});
		jQuery( 'body' ).on('change','#pa_color',function(){
			add_price_to_options();
		});
			
	});
</script>