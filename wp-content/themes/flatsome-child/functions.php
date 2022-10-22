<?php
// Add custom Theme Functions here

function remove_query_strings() {
   if(!is_admin()) {
       add_filter('script_loader_src', 'remove_query_strings_split', 15);
       add_filter('style_loader_src', 'remove_query_strings_split', 15);
   }
}

function remove_query_strings_split($src){
   $output = preg_split("/(&ver|\?ver)/", $src);
   return $output[0];
}
add_action('init', 'remove_query_strings');

add_filter( 'the_content', 'theme_replace_site_domain' );
add_filter( 'woocommerce_format_content', 'theme_replace_site_domain' );
add_filter( 'the_excerpt', 'theme_replace_site_domain' );
add_filter( 'widget_text', 'theme_replace_site_domain' );
add_filter( 'flatsome_contentfix', 'theme_replace_site_domain' );

function theme_replace_site_domain($content){
	$site_info = get_field('site_info','option');
	
	$urlparts = parse_url(get_bloginfo('url'));
	$domain = $urlparts ['host'];


	$content = str_replace('__SITE_MAP__',$site_info['site_map'],$content);
	$content = str_replace('__SITE_CONTACT_EMAIL__',$site_info['contact_email_address'],$content);
	$content = str_replace('__SITE_OPEN_HOURS__',$site_info['site_open_hours'],$content);
	$content = str_replace('__SITE_PHONE__',$site_info['site_phone'],$content);
	$content = str_replace('__SITE_ADDRESS__',$site_info['site_address'],$content);
	$content = str_replace('__SITE_DOMAIN__',$domain,$content);
	$content = str_replace('__SITE_NAME__',get_bloginfo('name'),$content);
	
	return $content;
}

add_action( 'woocommerce_checkout_order_review', 'theme_woocommerce_order_review', 11 );
function theme_woocommerce_order_review(){
	echo do_shortcode('[order_tip_form]');
}

//search multi keywords
function theme_posts_search( $search, $wp_query )
{
    global $wpdb;
    if(empty($search)) {
        return $search;
    }
    if ( $wp_query->is_search() && $wp_query->get('post_type') == 'product') {
      $search = $wp_query->get('s');
			$search = trim($search);
      if( $search ){
        $search_arr = explode(' ',$search);
        $search_config = get_field('search','option');
        $search_config_active   = $search_config['search_config_active'];
        $search_config_keywords = $search_config['search_config_keywords'];
        $search_config_keywords = explode(',',$search_config_keywords);

        $or_condition = [];
        if( count($search_arr) > 1 ){
          foreach ($search_arr as $key => $value){
            if( $search_config_active == 'yes' ){
              if( in_array( strtolower( $value ) , $search_config_keywords ) ){
                $empty_search = true;
              }
            }

            $or_condition[] = " $wpdb->posts.post_title LIKE '%$value%' ";
          }
          $search = " AND ( ". implode(' OR ',$or_condition) ." )";
        }else{
          $search = " AND $wpdb->posts.post_title LIKE '%$search%' ";
          if( $search_config_active == 'yes' ){
            if( in_array( strtolower( $search ) , $search_config_keywords ) ){
              $empty_search = true;
            }
          }
        }
        if( $empty_search ){
          $search = " AND 1 = 2";
        }
      }
			
    }
    return $search;
}
add_filter('posts_search', 'theme_posts_search', 500, 2);


if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

function search_filter_get_posts($query) {
  if ( $query->is_main_query() && $query->is_tax('pa_event') ){
    $term = get_queried_object();
    $tags = get_field('tags', $term->taxonomy . '_' . $term->term_id);
    if( $tags && count($tags) > 0 ){
      // $query->query = null; 
      $query->query_vars['pa_event'] = null; 
      $taxquery = [
        [
          'taxonomy'  => 'product_tag',
          'field'     => 'term_id',
          'terms'     => $tags,
        ]
      ];
      $query->set('tax_query', $taxquery); 
    }
  }
  return $query;
}
add_filter( 'pre_get_posts', 'search_filter_get_posts',999 );


add_shortcode( 'recently_viewed_products', 'theme_recently_viewed_products' );
 
function theme_recently_viewed_products() {
   $viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
   $viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
   if ( empty( $viewed_products ) ) return;
   $product_ids = implode( ",", $viewed_products );
   return do_shortcode('[ux_products style="normal" columns="6" slider_nav_style="circle" slider_nav_color="light" tags="" ids="'. $product_ids .'"]');;
}

add_filter( 'wc_stripe_generate_payment_request', 'theme_wc_stripe_generate_payment_request',999,3 );

function theme_wc_stripe_generate_payment_request($post_data, $order, $prepared_payment_method){
  $post_data['description'] = 'Order '.$order->get_order_number();
  return $post_data;
}
