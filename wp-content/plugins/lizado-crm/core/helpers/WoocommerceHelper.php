<?php
class WoocommerceHelper {
	public function __construct(){
		//add to card for custom type product
		add_action( 'woocommerce_advanced_add_to_cart', array( $this, 'woocommerce_advanced_add_to_cart' ) );
		//register new custom type product
		add_action( 'woocommerce_loaded', array( $this, 'load_plugin' ) );
		//select custom type product
		add_filter( 'product_type_selector', array( $this, 'add_type' ) );
		//field custom type product
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'advanced_product_tab' ) );
		//if custom product set stock true
		add_filter( 'woocommerce_product_is_in_stock', array( $this, 'woocommerce_product_is_in_stock' ),10,2 );
		//filter the image by cdn
		add_filter( 'woocommerce_product_get_image', array( $this, 'woocommerce_product_get_image' ),10,5 );
		add_filter( 'wp_get_attachment_image_src', array( $this, 'wp_get_attachment_image_src' ),10,4 );
		add_filter( 'woocommerce_structured_data_product', array( $this, 'woocommerce_structured_data_product' ),10,2 );

		//handle Custom fields
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'woocommerce_cart_item_subtotal' ),10,3 );
		//filter cart subtotal
		// add_filter( 'woocommerce_cart_subtotal', array( $this, 'woocommerce_cart_subtotal' ),10, 3 );
		//filter product subtotal
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_before_calculate_totals' ) );
		//add info to cart data
		add_action( 'woocommerce_cart_item_price', array( $this, 'woocommerce_cart_item_price' ),10,3 );
		//add info to cart data
		add_action( 'woocommerce_add_cart_item_data', array( $this, 'woocommerce_add_cart_item_data' ),  10, 2 );
		//show info at cart and checkout data
		add_action( 'woocommerce_get_item_data', array( $this, 'woocommerce_get_item_data' ),  10, 2 );
		// show info orders and email notifications (save as custom order item meta data)
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'woocommerce_checkout_create_order_line_item' ),  10, 4 );
		// Change displayed label for specific custom order item meta keys
		add_action( 'woocommerce_order_item_display_meta_key', array( $this, 'woocommerce_order_item_display_meta_key' ),  10, 3 );

		//change badge
		add_filter( 'flatsome_new_flash_html', array( $this, 'flatsome_new_flash_html' ),10,4 );
		
		//handle count view
		add_action( 'woocommerce_after_single_product', array( $this, 'woocommerce_after_single_product' ));
	}

	function woocommerce_after_single_product(){
		global $product;
		$post_id = $product->id;
		$count_key = 'wcmvp_product_view_count';
		$count     = get_post_meta( $post_id, $count_key, true );
		if ( $count == '' ) {
			delete_post_meta( $post_id, $count_key );
			update_post_meta( $post_id, $count_key, '1' );
		} else {
			$count ++;
			update_post_meta( $post_id, $count_key, (string) $count );
		}
	}
	function flatsome_new_flash_html($html,$post, $product, $badge_style){
		return '';
		$html = '<img class="trending-product-icon" src="'.get_stylesheet_directory_uri().'/assets/images/hot-icon.png" alt="">';
		return $html;
	}

	function woocommerce_cart_subtotal($cart_subtotal, $compound, $cart){
		//dd($cart);
	}

	function woocommerce_cart_item_subtotal($wc, $cart_item, $cart_item_key){
		$new_subtotal = $cart_item['custom_price'] * $cart_item['quantity'];
		return wc_price( $new_subtotal );
		return $wc;
	}

	function woocommerce_cart_item_price( $price_html, $cart_item, $cart_item_key ) {
		if( isset( $cart_item['custom_price'] ) ) {
			$args = array( 'price' => $cart_item['custom_price'] );
			if ( WC()->cart->display_prices_including_tax() ) {
				$product_price = wc_get_price_including_tax( $cart_item['data'], $args );
			} else {
				$product_price = wc_get_price_excluding_tax( $cart_item['data'], $args );
			}
			return wc_price( $product_price );
		}
		return $price_html;
	}

	function woocommerce_before_calculate_totals($cart){
		// This is necessary for WC 3.0+
		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

		// Avoiding hook repetition (when using price calculations for example | optional)
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
			return;

		// Loop through cart items
		foreach ( $cart->get_cart() as $cart_item ) {
			$cart_item['data']->set_price( $cart_item['custom_price'] );
			$cart_item['data']->set_regular_price( $cart_item['custom_price'] );
		}
	}

	private function get_variant_price($product_id,$color,$size){
		global $AppDB;
		$AppDB->where('wordpress_post_id',$product_id);
		$object = $AppDB->ObjectBuilder()->getOne('crawl_products');
		$new_price = ($object) ? $object->price : 0;
		if($object){
			$object->size_supported = json_decode($object->size_supported,true);
			$object->size_map_price = json_decode($object->size_map_price,true);

			$the_price = ( isset( $object->size_map_price[$size] ) ) ? $object->price + (int)$object->size_map_price[$size] : $object->price;
			return $the_price;
		}
		return $new_price;
	}

	function wp_get_attachment_image_src($image, $attachment_id, $size, $icon){
		$src = get_post_meta($attachment_id,'product_system_image',true);
		if($src){
			
			switch( $size ){
				case 'woocommerce_thumbnail':
					$src = str_replace('UL1500','UL280',$src);
					$image[1] = 280;
					$image[2] = 265;
					break;
				case 'woocommerce_single':
				case 'full':
					$src = str_replace('UL1500','UL792',$src);
					$image[1] = 792;
					$image[2] = 740;
					break;
				default:
				
					break;
			}
			$image[0] = $src;
		}
		return $image;
	}

	function woocommerce_product_get_image($image, $product, $size, $attr, $placeholder){
		$product_id = $product->get_id();
		$src = get_post_meta($product_id,'product_system_image',true);
		if( $src ){
			switch( $size ){
				case 'woocommerce_thumbnail':
					$src = str_replace('UL1500','UL280',$src);
					$width = 280;
					$height = 265;
					break;
				case 'woocommerce_single':
					$src = str_replace('UL1500','UL792',$src);
					$width = 792;
					$height = 740;
					break;
				default:
					$width  = 280;
					$height = 265;
					break;
			}
			if( !is_admin() ){
				$image = '<image class="lazy-load wp-post-image custom-img" width="'.$width.'" height="'.$height.'" data-src="'.$src.'" />';
			}else{
				$image = '<image class="wp-post-image " width="'.$width.'" height="'.$height.'" src="'.$src.'" />';

			}
		}
		return $image;
	}

	function woocommerce_order_item_display_meta_key( $display_key, $meta, $item ) {
		// Change displayed label for specific order item meta key
		if( is_admin() && $item->get_type() === 'line_item' ) {
			if( $meta->key === 'color' ) {
				$display_key = __("Color", "woocommerce");
			}
			if( $meta->key === 'size' ) {
				$display_key = __("Size", "woocommerce");
			}
		}
		return $display_key;
	}

	function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
		if ( isset($values['attribute_pa_color']) ) {
			$item->add_meta_data( 'color', $values['attribute_pa_color'] );
		}
		if ( isset($values['attribute_pa_size']) ) {
			$item->add_meta_data( 'size', $values['attribute_pa_size'] );
		}
	}

	function woocommerce_get_item_data( $cart_data, $cart_item ) {
		if ( isset($cart_item['attribute_pa_color']) ) {
			$custom_items[] = array( 'name' => 'Color', 'value' => $cart_item['attribute_pa_color'] );
		}
		if ( isset($cart_item['attribute_pa_size']) ) {
			$custom_items[] = array( 'name' => 'Size', 'value' => $cart_item['attribute_pa_size'] );
		}
		return $custom_items;
	}

	public function woocommerce_add_cart_item_data($cart_item_data, $cart_item){
		
		$product_id = $_REQUEST['product_id'];
		$color 		= $_REQUEST['attribute_pa_color'];
		$size 		= $_REQUEST['attribute_pa_size'];
		$cart_item_data['attribute_pa_color'] = $_REQUEST['attribute_pa_color'];
		$cart_item_data['attribute_pa_size'] 	= $_REQUEST['attribute_pa_size'];
		$cart_item_data['custom_price'] 		= $this->get_variant_price($product_id,$color,$size);
		return $cart_item_data;
	}

	private function format_variations(){
		$variations = [];
		
	}

	public function woocommerce_advanced_add_to_cart() {
		global $product;
		// Enqueue variation scripts.
		wp_enqueue_script( 'wc-add-to-cart-variation' );
		// $object = WoocommerceHelper::get_system_product($product->get_id(),['id','title'],true);

		// $variations = $this->format_variations();
		wc_get_template( 'single-product/add-to-cart/advanced.php' ,[
			'attributes' => $product->get_variation_attributes(),
			'available_variations' => $product->get_available_variations(),
			'selected_attributes'  => $product->get_default_attributes(),
		]);
	}
	public function woocommerce_product_is_in_stock($status,$product) {
		if( $product->get_type() == 'advanced'){
			return true;
		}
		return $status;
	}
	public function load_plugin() {
        require_once 'includes/class-wc-product-advanced.php';
    }
	public function add_type( $types ) {
        $types['advanced'] = __( 'Advanced', 'yourtextdomain' );
        return $types;
    }
	public function advanced_product_tab( $tabs) {
			
		$tabs['advanced'] = array(
		'label'	 => __( 'Advanced', 'dm_product' ),
		'target' => 'advanced_product_options',
		'class'  => 'show_if_advanced_product',
		);
		return $tabs;
	}

	public static function get_system_product( $post_id, $fields = [] ,$get_sub = false,$get_variant = true){
		global $AppDB;
		$fields[] = 'product_type';
		$AppDB->where('wordpress_post_id',$post_id);
		$item = $AppDB->ObjectBuilder()->getOne('crawl_products');
		$item->size_supported = json_decode($item->size_supported,true);
		$item->size_map_price = json_decode($item->size_map_price,true);
		$item->pa_color = json_decode($item->pa_color,true);
		$item->pa_size = json_decode($item->pa_size,true);
		$item->variants = json_decode($item->variants,true);
		$item->sub_products = json_decode($item->sub_products,true);
		return $item;

	}

	public static function get_system_attribute_items($fields = ['id','title'] ){
		global $AppDB;
		$items = $AppDB->ObjectBuilder()->get('product_attribute_items',null,$fields);
		$attribute_items = [];
		foreach( $items as $item ){
			$attribute_items[$item->id] = $item->title;
		}
		return $attribute_items;
	}
	public static function get_single_variant($sub_product,$size = null,$size_map_price = []){
		$sub_product->image_url = str_replace('UL1500','UL792',$sub_product->image_url);
		$the_price = ( isset( $size_map_price[$size] ) ) ? $sub_product->price + (int)$size_map_price[$size] : $sub_product->price;
		$variation = [
			'attributes' => [
				'attribute_pa_color' 	=> $sub_product->color,
				'attribute_pa_size' 	=> ($size) ? $size : $sub_product->size,
			],
			'availability_html' 	=> '',
			'backorders_allowed' 	=> false,
			'dimensions_html' 		=> 'N/A',
			'display_price' 		=> $the_price,
			'display_regular_price' => $the_price,
			'image' => [
				'title' => $sub_product->title,
				'caption' => $sub_product->title,
				'url' => $sub_product->image_url,
				'alt' => $sub_product->title,
				'src' => $sub_product->image_url,
				'srcset' => '',
				'sizes' => '',
			],
			'image_id' => 0,
			'is_downloadable' => false,
			'is_in_stock' => true,
			'is_purchasable' => true,
			'is_sold_individually' => 'no',
			'is_virtual' => false,
			'max_qty' => null,
			'price_html' => $the_price,
			'sku' => '',
			'variation_description' => '',
			'variation_id' => $sub_product->id,
			'variation_is_active' => true,
			'variation_is_visible' => true,
			'weight' => true,
			'weight_html' => 'N/A'
		];

		return $variation;
	}

	public function woocommerce_structured_data_product($markup, $product){
		$shop_name = get_bloginfo( 'name' );
		$shop_url  = home_url();
		$markup['image'] =  get_post_meta($product->get_id(),'product_system_image',true);
		$markup['brand'] = [
			'@type' => 'Organization',
			'name'  => $shop_name,
			'url'   => $shop_url,
		];
		if( !isset( $markup['aggregateRating'] )  ){
			$markup['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => 5,
				'reviewCount' => 5,
			);
		}
		if( !isset( $markup['review'] )  ){
			$markup['review'][] = array(
				'@type'         => 'Review',
				'reviewRating'  => array(
					'@type'       => 'Rating',
					'bestRating'  => '5',
					'ratingValue' => 5,
					'worstRating' => '1',
				),
				'author'        => array(
					'@type' => 'Person',
					'name'  => $shop_name,
				),
				'reviewBody'    => 'Good',
				'datePublished' => date('Y-m-d'),
			);
		}
		
		return $markup;
	}

	
}

new WoocommerceHelper();