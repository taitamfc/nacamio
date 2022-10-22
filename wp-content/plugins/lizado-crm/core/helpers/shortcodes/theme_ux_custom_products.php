<?php
	add_shortcode("theme_ux_custom_products", "theme_ux_custom_products");
	function theme_ux_custom_products($attr){
		$type = 'most_views';
		extract($attr);
		
		global $wpdb;
		switch($type) {
		  case 'most_views':
			$sql = "SELECT post_id FROM `$wpdb->postmeta` WHERE `meta_key` = 'wcmvp_product_view_count' ORDER BY `meta_value` DESC LIMIT 15";
			break;
		  case 'recent_views':
			$sql = "SELECT post_id FROM `$wpdb->postmeta` WHERE `meta_key` = 'wcmvp_product_view_count' ORDER BY `post_id` DESC LIMIT 15";
			break;
		  default:
			$sql = "SELECT post_id FROM `$wpdb->postmeta` WHERE `meta_key` = 'wcmvp_product_view_count' ORDER BY `meta_value` DESC LIMIT 15";
			break;
		} 
		
		$ids =  $wpdb->get_col($sql);
		if( $ids ){
			$ids = implode(',',$ids);
		}else{
			$ids = 0;
		}
		echo '<div class="theme_ux_custom_products">';
		echo do_shortcode('[ux_products columns="5" slider_nav_style="circle" show_rating="0" show_add_to_cart="0" show_quick_view="0" equalize_box="true" ids="'.$ids.'"]');
		echo '</div>';
	}
?>