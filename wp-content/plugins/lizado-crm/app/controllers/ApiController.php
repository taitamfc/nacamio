<?php
class ApiController extends CRController {
  public $tbl_crawl_products = 'crawl_products';
  public function index() {
    echo __METHOD__;
  }

  public function import_product_to_wp(){
    $cron            = get_field('cron','option');
    $limit = $cron['import_to_wp_limit_per_call'];
    global $AppDB,$wpdb;
    $AppDB->where('wordpress_post_id',NULL,'IS');
    $AppDB->where('ready_to_wp',1);
    $items = $AppDB->ObjectBuilder()->get($this->tbl_crawl_products,$limit,[]);
    if( count($items) == 0 ){
      $return['msg']     = 'No item found';
      echo json_encode($return);
      die();
    }

    $post_ids = [];
    foreach ($items as $item) {
      $id = $item->system_id;
      $item->pa_size = json_decode($item->pa_size,true);
      $item->size_supported = json_decode($item->size_supported,true);
      $item->pa_color = json_decode($item->pa_color,true);

      $post_id = wp_insert_post( array(
        'post_title' => $item->title,
        'post_type' => 'product',
        'post_status' => 'publish',
        'post_content' => $item->description,
      ));


      //update products
      $AppDB->where('system_id',$id);
      $AppDB->update( $this->tbl_crawl_products, ['wordpress_post_id' => $post_id] );

      $product = new WC_Product_Advanced( $post_id );
      $product->set_regular_price( $item->price );
      $product->save();

      //update product_system_id
      update_post_meta($post_id,'product_system_id',$id);
      update_post_meta($post_id,'product_system_image',$item->image_url);
      
      //update category
      if( term_exists( $item->product_type, 'product_cat' ) ){
        wp_set_object_terms( $post_id, $item->product_type, 'product_cat', true );
      }else{
          $new_term = wp_insert_term(
              $item->product_type,   // the term 
              'product_cat'
          );
          wp_set_object_terms( $post_id, $new_term['term_id'], 'product_cat', true );
      }

      //attack product_tags
      $this->attach_product_tags($item->title,$post_id);
      
      if( $item->pa_size ){
        $item->pa_size = count($item->size_supported) ? $item->size_supported : $item->pa_size;
        $attributes_data = array(
            array('name'=>'Size',  'options' => $item->pa_size, 'visible' => 1, 'variation' => 1 ),
            array('name'=>'Color', 'options' => $item->pa_color, 'visible' => 1, 'variation' => 1 )
        );
        if( sizeof($attributes_data) > 0 ){
            $attributes = array(); // Initializing
        
            // Loop through defined attribute data
            foreach( $attributes_data as $key => $attribute_array ) {
                if( isset($attribute_array['name']) && isset($attribute_array['options']) ){
                    $taxonomy = 'pa_' . wc_sanitize_taxonomy_name( $attribute_array['name'] );
                    $option_term_ids = array(); // Initializing
                    foreach( $attribute_array['options'] as $option ){
                        if( term_exists( $option, $taxonomy ) ){
                            wp_set_object_terms( $post_id, $option, $taxonomy, true );
                        }else{
                            $new_term = wp_insert_term(
                                $option,   // the term 
                                $taxonomy
                            );
                            wp_set_object_terms( $post_id, $option, $taxonomy, true );
                        }
                        // Get the term ID
                        $option_term_ids[] = get_term_by( 'name', $option, $taxonomy )->term_id;
                    }
                }
                // Loop through defined attribute data
                $attributes[$taxonomy] = array(
                    'name'          => $taxonomy,
                    'value'         => $option_term_ids, // Need to be term IDs
                    'position'      => $key + 1,
                    'is_visible'    => $attribute_array['visible'],
                    'is_variation'  => $attribute_array['variation'],
                    'is_taxonomy'   => '1'
                );
            }
            // Save the meta entry for product attributes
            update_post_meta( $post_id, '_product_attributes', $attributes );
        }
      }

      
      $post_ids[] = $post_id;
    }
    $return['post_ids']     = $post_ids;
    echo json_encode($return);
    die();
  }
  public function crawl_products(){
    $web_hook 		= get_option('options_site_info_web_hook');
    $site_domain = $this->get_site_domain();
    $cron            = get_field('cron','option');
    $API_KEY          = $cron['API_KEY'];
    $api_url          = $web_hook.'/product-api/?c=Product';
    $limit            = $cron['product_limit_per_call'];
    $page             = $cron['product_api_next_page'];
    $start_id 		    = $cron['product_start_id'];
    $end_id 			    = $cron['product_end_id'];
    $api_url          .= '&limit='.$limit.'&page='.$page.'&API_KEY='.$API_KEY.'&start_id='.$start_id.'&end_id='.$end_id;
    $api_url          .= '&site_domain='.$site_domain;
    $res              = wp_remote_get( $api_url, array( 'timeout' => 30 ) );
    
    if($res['response']['code'] == 200){
      global $AppDB;
      $data      = json_decode($res['body']);
      if( isset( $data->error ) ){
        $return['error']  = $data->msg;
        echo json_encode($return);
        die();
      }
      $system_ids = $data->data->items;
      
      
      $check_ids  = [];
      foreach ($system_ids as $system_id) {
        $check_ids[] = $system_id->system_id;
      }

      $exist_ids = [];
      $import_ids = [];
      if( count($check_ids) ){
        // $AppDB->where('wordpress_post_id',NULL, 'IS NOT');
        $AppDB->where('system_id',$check_ids,'IN');
        $check_objects = $AppDB->ObjectBuilder()->get($this->tbl_crawl_products,count($check_ids),['system_id']);
        if( count($check_objects) ){
          foreach( $check_objects as $check_object ){
          $exist_ids[] = $check_object->system_id;
          }
        }

        foreach( $check_ids as $check_id  ){
          if( !in_array( $check_id, $exist_ids ) ){
            $import_ids[] = [
              'system_id' => $check_id
            ];
          }
        }
      }

      try {
        if( count($import_ids) ){
          $imported_ids = $AppDB->insertMulti($this->tbl_crawl_products, $import_ids);
          $return['imported_ids']  = count($system_ids);
          // dd( $AppDB->getLastQuery() );
        }
        update_option('options_cron_product_api_next_page', $page + 1);
      } catch (\Exeption $e) {
        $return['error']  = $e->getMessage();
      }
    }
    $return['message']  = $res['response']['message'];
    $return['code']     = $res['response']['code'];
    $return['page']     = $page;
    echo json_encode($return);
  }

  public function crawl_product_detail(){
    $web_hook 		= get_option('options_site_info_web_hook');
    $cron            = get_field('cron','option');
    $API_KEY          = $cron['API_KEY'];
    $api_url          = $cron['product_detail_api_url'];
    $api_url          = $web_hook.'/product-api/?c=Product&a=show';
    $api_url          .= '&site_domain='.$site_domain;
    $limit            = $cron['product_detail_limit_per_call'];
    

    global $AppDB;
    $AppDB->where('ready_to_wp',0);
    $need_calls = $AppDB->ObjectBuilder()->get($this->tbl_crawl_products,$limit,['system_id']);
    $system_ids = [];
    if( count($need_calls) ){
      foreach( $need_calls as $need_call ){
        $system_ids[] = $need_call->system_id;
      }
    }

    if( count( $system_ids ) ){
      $api_url .= '&id='.implode(',',$system_ids).'&API_KEY='.$API_KEY;
      $res              = wp_remote_get( $api_url, array( 'timeout' => 30 ) );
      if($res['response']['code'] == 200){
        global $AppDB;
        $data      = json_decode($res['body']);
        if( isset( $data->error ) ){
          $return['error']  = $data->msg;
          echo json_encode($return);
          die();
        }
  
        
        $import_data = [];
        $system_ids = [];
        if( count($data->data) ){
          foreach( $data->data as $data ){
            $system_ids[] = $data->id;
            $import_data[] = [
              'system_id' => $data->id,
              'title' => $data->title,
              'description' => $data->description,
              'price' => $data->price,
              'image_url' => $data->image_url,
              'product_type' => $data->product_type,
              'size_supported' => json_encode($data->size_supported),
              'size_map_price' => json_encode($data->size_map_price),
              'pa_color' => json_encode($data->pa_color),
              'pa_size' => json_encode($data->pa_size),
              'variants' => json_encode($data->variants),
              'sub_products' => json_encode($data->sub_products),
              'ready_to_wp' => 1,
            ];
          }
        }

        try {
          if( count($import_data) ){
            if( count($system_ids) ){
              $AppDB->where('system_id',$system_ids, 'IN');
              $AppDB->delete($this->tbl_crawl_products);
            }

            $AppDB->insertMulti($this->tbl_crawl_products, $import_data);
            // dd( $AppDB->getLastQuery() );
          }
          update_option('options_cron_product_api_next_page', $page + 1);
        } catch (\Exeption $e) {
          $return['error']  = $e->getMessage();
        }

      }
    }

    $return['system_ids'] = implode(',',$system_ids);
    echo json_encode($return);
  }
	
  public function sync(){
    $sync_type = $_REQUEST['sync_type'];
    $web_hook 		= get_option('options_site_info_web_hook');
    $site_domain = $this->get_site_domain();

    switch ($sync_type) {
      case 'init_settings':
        $web_hook = $_POST['web_hook'];
        update_option('options_site_info_web_hook',$web_hook);
        update_field( 'site_info_web_hook', $web_hook, 'option' );
        $return['web_hook'] = $web_hook;
        $return['next_step'] = 1;
        break;
      case 'site_settings':
        $response = wp_remote_get( $web_hook.'/api/sites?domain='.$site_domain , [
          'timeout'     => 30,
          'sslverify' => false
        ] );
        $body = $response['body'];
        $body = json_decode($body,true);
        $this->site_settings($body['data']);
        $return['next_step'] = 1;
        break;
      case 'menu_items':
        $response = wp_remote_get( $web_hook.'/api/menu_items?domain='.$site_domain , [
          'timeout'     => 30,
          'sslverify' => false
        ] );
        $body = $response['body'];
        $body = json_decode($body,true);
        $this->menu_items($body);
        $return['next_step'] = 1;
        break;
      case 'events':
        $response = wp_remote_get( $web_hook.'/api/events?domain='.$site_domain , [
          'timeout'     => 30,
          'sslverify' => false
        ] );
        $body = $response['body'];
        $body = json_decode($body,true);
        $this->events($body);
        $return['next_step'] = 1;
        break;
      case 'categories':
        $response = wp_remote_get( $web_hook.'/api/categories?domain='.$site_domain , [
          'timeout'     => 30,
          'sslverify' => false
        ] );
        $body = $response['body'];
        $body = json_decode($body,true);
        $this->categories($body);
        $return['next_step'] = 1;
        break;
      case 'assign_product_tags':
        $return = $this->assign_product_tags();
        $return['next_step'] = 1;
        break;
      default:
        # code...
        break;
    }
    $return['status'] = 1;
    $return['sync_type'] = $sync_type;
    echo json_encode($return);
    die();
  }

  private function menu_items($data){
      $site_domain = $this->get_site_domain();
      $site_url = get_site_url();
      $menu_name = 'Main';
      $menu_location = 'primary';
      $menu_exists = wp_get_nav_menu_object( $menu_name );
      if( $menu_exists ){
        $menu_id = $menu_exists->term_id;
      }else{
        $menu_id = wp_create_nav_menu($menuname);
      }

      // Set menu location
      if( !has_nav_menu( $menu_location ) ){
          $locations = get_theme_mod('nav_menu_locations');
          $locations[$menu_location] = $menu_id;
          set_theme_mod( 'nav_menu_locations', $locations );
      }

      $menu_items = wp_get_nav_menu_items($menu_id);
      if( count($menu_items) ){
        foreach( $menu_items as $menu_item ){
          wp_delete_post($menu_item->db_id);
        }
      }

      foreach( $data as $menu_item ){
        $update_id = wp_update_nav_menu_item($menu_id, 0, array(
          'menu-item-title' => $menu_item['name'],
          'menu-item-url'   => $site_url.'/'.$menu_item['link'],
          'menu-item-status' => 'publish',
          'menu-item-type' => 'custom', // optional
        ));
        if( count( $menu_item['children'] ) ){
          foreach( $menu_item['children'] as $child ){
            wp_update_nav_menu_item($menu_id, 0, array(
              'menu-item-title' => $child['name'],
              'menu-item-url'   => $site_url.'/'.$child['link'],
              'menu-item-parent-id' => $update_id,
              'menu-item-status' => 'publish',
              'menu-item-type' => 'custom', // optional
            ));
          }
        }
      }

  }
  private function site_settings($data){
      //update bloginfo
      update_option('blogname',$data['site_title']);
      update_option('blogdescription',$data['tagline']);
      update_option('admin_email',$data['administration_email_address']);

      $theme_options = get_option('theme_mods_flatsome-child');
      $theme_options['html_scripts_header'] = $data['html_scripts_header'];
      $theme_options['html_scripts_footer'] = $data['html_scripts_footer'];
      $theme_options['html_scripts_after_body'] = $data['html_scripts_after_body'];
      $theme_options['html_scripts_before_body'] = $data['html_scripts_before_body'];
      update_option('theme_mods_flatsome-child',$theme_options);

      $gtm4wp_options = get_option('gtm4wp-options');
      $gtm4wp_options['gtm-code'] = $data['google_api_key'];
      update_option('gtm4wp-options',$gtm4wp_options);

      
      //update acf field
      $cron_fields = [
        'API_KEY',
        'product_api_url',
        'product_start_id',
        'product_end_id',
        'product_limit_per_call',
        'product_call_interval',
        'product_detail_api_url',
        'product_detail_limit_per_call',
        'product_detail_call_interval',
        'import_to_wp_limit_per_call',
        'import_to_wp_interval',
      ];

      $site_info_fields = [
        'site_title',
        'tagline',
        'site_domain',
        'administration_email_address',
        'web_hook',
        'site_phone',
        'contact_email_address',
        'site_address',
        'site_map',
        'site_open_hours',
      ];


      foreach( $cron_fields as $field ){
        update_option('options_cron_'.$field,$data[$field]);
        update_field( 'cron_'.$field, $data[$field], 'option' );
      }
      foreach( $site_info_fields as $field ){
        // echo '<br>'.$field.'-'.$data[$field];
        update_option('options_site_info_'.$field,$data[$field]);
        update_field( 'site_info_'.$field, $data[$field], 'option' );

      }

      //search_config_keywords
      update_option('options_search_search_config_active',$data['search_config_active']);
      update_field( 'search_search_config_active',$data['search_config_active'], 'option' );
      update_option('options_search_search_config_keywords',$data['search_config_keywords']);
      update_field( 'search_search_config_keywords',$data['search_config_keywords'], 'option' );

  }
  private function events($product_events){
    $web_hook = get_option('options_site_info_web_hook');
    
    if( $product_events && count($product_events) ){
      foreach( $product_events as $product_event ){
        
        $term = term_exists( $product_event['name'], 'pa_event' );
        if( !$term ){
          //insert new term
          $term = wp_insert_term(
            $product_event['name'],
            'pa_event'
          );
        }
        //update term meta
        update_field( 'start_date', $product_event['start_month'].$product_event['start_day'], 'pa_event_'.$term['term_id'] );
        
        //update term meta tags
        $product_event['product_tags'] = explode(',',$product_event['product_tags']);
        if( count($product_event['product_tags']) ){
          $tag_ids = [];
          foreach( $product_event['product_tags'] as $event_tag ){
            $tag = term_exists( $event_tag, 'product_tag' );
            if( $tag ){
              $tag_ids[] = $tag['term_id'];
            }else{
              $tag = wp_insert_term(
                $event_tag,
                'product_tag'
              );
              $tag_ids[] = $tag['term_id'];
            }
          }
          update_field( 'tags', $tag_ids, 'pa_event_'.$term['term_id'] );
        }
        
        //update image meta
        if( $product_event['image_url'] ){
          $download_remote_image = new KM_Download_Remote_Image( $web_hook.'/storage/'.$product_event['image_url'] );
          $attachment_id         = $download_remote_image->download();
          if($attachment_id){
            update_field( 'image', $attachment_id, 'pa_event_'.$term['term_id'] );
          }
        }
        
      }//end foreach
    }//end if
  }
  private function categories($data){
    $web_hook = get_option('options_site_info_web_hook');
    if( $data && count($data) ){
      foreach( $data as $product_cagegory ){
        $term = term_exists( $product_cagegory['name'], 'product_cat' );
        if( !$term ){
          $term = wp_insert_term(
            $product_cagegory['name'],
            'product_cat'
          );
        }

        //update image meta
        if( $product_cagegory['image_url'] ){
          $download_remote_image = new KM_Download_Remote_Image( $web_hook.'/storage/'.$product_cagegory['image_url'] );
          $attachment_id         = $download_remote_image->download();
          if($attachment_id){
            update_term_meta($term['term_id'], 'thumbnail_id',$attachment_id);
          }
        }
      }
    }  
  }
  private function assign_product_tags(){
    global $AppDB,$wpdb;
    $page = get_option('assign_product_tags_paged',1);
    $AppDB->pageLimit = 100;
    $AppDB->where('post_type','product');
    $AppDB->where('post_status','publish');
    $AppDB->orderBy('ID','desc');
    $items = $AppDB->ObjectBuilder()->paginate($wpdb->prefix.'posts', $page,['ID','post_title']);
    foreach ($items as $item) {
        $this->attach_product_tags($item->post_title,$item->ID);
    }
    update_option('assign_product_tags_paged',(int)$page + 1);
    $return = [
      'totalPages'  => $AppDB->totalPages,
      'page'        => (int)$page,
      'loop'        => 1
    ];
    if( $page >= $AppDB->totalPages ){
      $return['loop'] = 0;
      update_option('assign_product_tags_paged',1);
    }
    return $return;
  }

  private function attach_product_tags($title,$post_id){
      global $wpdb;
      $title = str_replace('-','',$title);
      $post_title_arr = explode(' ',trim($title));
      if( count($post_title_arr) ){
        $tag_conditions = [];
        $term_sql = "SELECT {$wpdb->prefix}terms.term_id,{$wpdb->prefix}terms.name FROM `{$wpdb->prefix}terms` 
        JOIN  {$wpdb->prefix}term_taxonomy  ON {$wpdb->prefix}terms.term_id = {$wpdb->prefix}term_taxonomy.term_id
        WHERE {$wpdb->prefix}term_taxonomy.taxonomy = 'product_tag' ";
        foreach( $post_title_arr as $post_title_sm ){
          if($post_title_sm){
            $tag_conditions[] = " {$wpdb->prefix}terms.name LIKE '%$post_title_sm%'";
          }
        }
        if( count($tag_conditions) > 1 ){
          $term_sql .= " AND ( ". implode(' OR ', $tag_conditions ) ." )";
        }else{
          $term_sql .= " AND $tag_conditions[0]";
        }

        $available_tags = $wpdb->get_results($term_sql);
        if( $available_tags && count($available_tags) ){
          foreach( $available_tags as $available_tag ){
            wp_set_object_terms( $post_id, $available_tag->name, 'product_tag', true );
          }
        }

      }
  }

  private function get_site_domain(){
    $site_domain 	= get_option('options_site_info_site_domain');
    $site_domain 	= trim( str_replace( array( 'http://', 'https://' ), '', get_site_url() ), '/' );
    $site_domain 	= str_replace('camonspa.vn/lizado','camonspa.vn',$site_domain);
    return $site_domain;
  }
}