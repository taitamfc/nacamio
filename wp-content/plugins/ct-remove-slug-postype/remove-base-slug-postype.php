<?php
/*
	Plugin Name: CTremove Slug Postype
	Plugin URI: #
	Description: Remove custom post type base slug from url
	Version: 2.0
	Author: CT
	Author URI:#
	Text Domain: ctbcls_remove_base_slug
	Domain Path: /languages
*/
class ctbcls_remove_base_slug{
	var $plugin_admin_page;
	static $instance = null;

	static public function init(){
		if( self::$instance == null ){
			self::$instance = new self();
		}
		return self::$instance;
	}

	function __construct(){
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		$this->ctarr_postype = get_option( 'ctarr_postype', array() );
		$this->ctarr_postype_keys = array_keys( $this->ctarr_postype );

		add_action( 'admin_menu', array( $this, 'plugin_menu_link' ) );
		add_filter( 'post_type_link', array( $this, 'remove_slug' ), 10, 3 );
		add_action( 'template_redirect', array( $this, 'auto_redirect_old' ), 1 );
		add_action( 'pre_get_posts', array( $this, 'handle_cpts' ), 1 );
	}
	function plugins_loaded(){
		load_plugin_textdomain( 'ctbcls_remove_base_slug', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	function filter_plugin_actions( $links, $file ){
		$settings_link = '<a href="options-general.php?page=' . basename( __FILE__ ) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	function plugin_menu_link(){
		$this->plugin_admin_page = add_submenu_page(
			'options-general.php',
			__( 'CT Remove Slug', 'ctbcls_remove_base_slug' ),
			__( 'CT Remove Slug', 'ctbcls_remove_base_slug' ),
			'manage_options',
			basename( __FILE__ ),
			array( $this, 'admin_options_page' )
		);
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'filter_plugin_actions' ), 10, 2 );
	}

	function admin_options_page(){
		if( get_current_screen()->id != $this->plugin_admin_page ) return;
		global $wp_post_types; ?>
		<div class="wrap">
			<h2><?php _e( 'Remove base slug from url for these custom post types:', 'ctbcls_remove_base_slug' ) ?></h2><?php
			if( isset( $_POST['ctarr_postype_sent'] ) ){

				if( ! isset( $_POST['rcptb_alternation'] ) || ! is_array( $_POST['rcptb_alternation'] ) ){
					$alternation = array();
				}else{
					$alternation = $_POST['rcptb_alternation'];
				}

				if( ! isset( $_POST['ctarr_postype'] ) || ! is_array( $_POST['ctarr_postype'] ) ){
					$this->ctarr_postype = array();
				}else{
					$this->ctarr_postype = $_POST['ctarr_postype'];
				}

				foreach( $this->ctarr_postype as $post_type => $active ){
					$this->ctarr_postype[ $post_type ] = isset( $alternation[ $post_type ] ) ? 1 : 0;
				}

				$this->ctarr_postype_keys = array_keys( $this->ctarr_postype );

				update_option( 'ctarr_postype', $this->ctarr_postype, 'no' );
				echo '<div class="below-h2 updated"><p>'.__( 'Settings saved.' ).'</p></div>';
				flush_rewrite_rules();
			} ?>
			<br>
			<form method="POST" action="">
				<input type="hidden" name="ctarr_postype_sent" value="1">
				<table class="widefat" style="width:auto">
					<tbody><?php
						foreach( $wp_post_types as $type => $custom_post ){
							if( $custom_post->_builtin == false ){ ?>
								<tr>
									<td>
										<label>
											<input type="checkbox" name="ctarr_postype[<?php echo $custom_post->name ?>]" value="1" <?php echo isset( $this->ctarr_postype[ $custom_post->name ] ) ? 'checked' : '' ?>>
											<?php echo $custom_post->label ?> (<?php echo $custom_post->name ?>)
										</label>
									</td>
									<td>
										<label>
											<input type="checkbox" name="rcptb_alternation[<?php echo $custom_post->name ?>]" value="1" <?php echo isset( $this->ctarr_postype[ $custom_post->name ] ) && $this->ctarr_postype[ $custom_post->name ] == 1 ? 'checked' : '' ?>>
											<?php _e( 'alternation', 'ctbcls_remove_base_slug' ) ?>
										</label>
									</td>
								</tr><?php
							}
						} ?>
					</tbody>
				</table>

				<p><?php _e( '* if your custom post type children return error 404, then try alternation mode', 'ctbcls_remove_base_slug' ) ?></p>
				<hr>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save') ?>">
				</p>
			</form>
		</div><?php
	}

	function remove_slug( $permalink, $post, $leavename ){
		global $wp_post_types;
		foreach( $wp_post_types as $type => $custom_post ){
			if( $custom_post->_builtin == false && $type == $post->post_type && isset( $this->ctarr_postype[ $custom_post->name ] ) ){
				$custom_post->rewrite['slug'] = trim( $custom_post->rewrite['slug'], '/' );
				$permalink = str_replace( '/' . $custom_post->rewrite['slug'] . '/', '/', $permalink );
			}
		}
		return $permalink;
	}

	function get_current_url(){
		$REQUEST_URI = strtok( $_SERVER['REQUEST_URI'], '?' );
		$real_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
		$real_url .= $_SERVER['SERVER_NAME'] . $REQUEST_URI;
		return $real_url;
	}

	function handle_cpts( $query ){
		// make sure it's main query on frontend
		if( ! is_admin() && $query->is_main_query() && ! $query->get('queried_object_id') ){
			// conditions investigated after many tests
			if( $query->is_404() || $query->get('pagename') || $query->get('attachment') || $query->get('name') || $query->get('category_name') ){
				// test both site_url and home_url
				$web_roots = array();
				$web_roots[] = site_url();
				if( site_url() != home_url() ){
					$web_roots[] = home_url();
				}
				// polylang fix
				if( function_exists('pll_home_url') ){
					if( site_url() != pll_home_url() ){
						$web_roots[] = pll_home_url();
					}
				}

				foreach( $web_roots as $web_root ){
					// get clean current URL path
					$path = $this->get_current_url();
					$path = str_replace( $web_root, '', $path );
					// fix missing slash
					if( substr( $path, 0, 1 ) != '/' ){
						$path = '/' . $path;
					}
					// test for posts
					$post_data = get_page_by_path( $path, OBJECT, 'post' );
					if( ! ( $post_data instanceof WP_Post ) ){
						// test for pages
						$post_data = get_page_by_path( $path );
						if( ! is_object( $post_data ) ){
							// test for selected CPTs
							$post_data = get_page_by_path( $path, OBJECT, $this->ctarr_postype_keys );
							if( is_object( $post_data ) ){
								// maybe name with ancestors is needed
								$post_name = $post_data->post_name;
								if( $this->ctarr_postype[ $post_data->post_type ] == 1 ){
									$ancestors = get_post_ancestors( $post_data->ID );
									foreach( $ancestors as $ancestor ){
										$post_name = get_post_field( 'post_name', $ancestor ) . '/' . $post_name;
									}
								}
								// get CPT slug
								$query_var = get_post_type_object( $post_data->post_type )->query_var;
								// alter query
								$query->is_404 = 0;
								$query->tax_query = NULL;
								$query->is_attachment = 0;
								$query->is_category = 0;
								$query->is_archive = 0;
								$query->is_tax = 0;
								$query->is_page = 0;
								$query->is_single = 1;
								$query->is_singular = 1;
								$query->set( 'error', NULL );
								unset( $query->query['error'] );
								$query->set( 'page', '' );
								$query->query['page'] = '';
								$query->set( 'pagename', NULL );
								unset( $query->query['pagename'] );
								$query->set( 'attachment', NULL );
								unset( $query->query['attachment'] );
								$query->set( 'category_name', NULL );
								unset( $query->query['category_name'] );
								$query->set( 'post_type', $post_data->post_type );
								$query->query['post_type'] = $post_data->post_type;
								$query->set( 'name', $post_name );
								$query->query['name'] = $post_name;
								$query->set( $query_var, $post_name );
								$query->query[ $query_var ] = $post_name;

								break;
							}else{
								// deeper matching
								global $wp_rewrite;
								// test all selected CPTs
								foreach( $this->ctarr_postype_keys as $post_type ){
									// get CPT slug and its length
									$query_var = get_post_type_object( $post_type )->query_var;
									// test all rewrite rules
									foreach( $wp_rewrite->rules as $pattern => $rewrite ){
										// test only rules for this CPT
										if( strpos( $pattern, $query_var ) !== false ){
											if( strpos( $pattern, '(' . $query_var . ')' ) === false ){
												preg_match_all( '#' . $pattern . '#', '/' . $query_var . $path, $matches, PREG_SET_ORDER );
											}else{
												preg_match_all( '#' . $pattern . '#', $query_var . $path, $matches, PREG_SET_ORDER );
											}

											if( count( $matches ) !== 0 && isset( $matches[0] ) ){
												// build URL query array
												$rewrite = str_replace( 'index.php?', '', $rewrite );
												parse_str( $rewrite, $url_query );
												foreach( $url_query as $key => $value ){
													$value = (int)str_replace( array( '$matches[', ']' ), '', $value );
													if( isset( $matches[0][ $value ] ) ){
														$value = $matches[0][ $value ];
														$url_query[ $key ] = $value;
													}
												}

												// test new path for selected CPTs
												if( isset( $url_query[ $query_var ] ) ){
													$post_data = get_page_by_path( '/' . $url_query[ $query_var ], OBJECT, $this->ctarr_postype_keys );
													if( is_object( $post_data ) ){
														// alter query
														$query->is_404 = 0;
														$query->tax_query = NULL;
														$query->is_attachment = 0;
														$query->is_category = 0;
														$query->is_archive = 0;
														$query->is_tax = 0;
														$query->is_page = 0;
														$query->is_single = 1;
														$query->is_singular = 1;
														$query->set( 'error', NULL );
														unset( $query->query['error'] );
														$query->set( 'page', '' );
														$query->query['page'] = '';
														$query->set( 'pagename', NULL );
														unset( $query->query['pagename'] );
														$query->set( 'attachment', NULL );
														unset( $query->query['attachment'] );
														$query->set( 'category_name', NULL );
														unset( $query->query['category_name'] );
														$query->set( 'post_type', $post_data->post_type );
														$query->query['post_type'] = $post_data->post_type;
														$query->set( 'name', $url_query[ $query_var ] );
														$query->query['name'] = $url_query[ $query_var ];
														// solve custom rewrites, pagination, etc.
														foreach( $url_query as $key => $value ){
															if( $key != 'post_type' && substr( $value, 0, 8 ) != '$matches' ){
																$query->set( $key, $value );
																$query->query[ $key ] = $value;
															}
														}
														break 3;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	function auto_redirect_old(){
		global $post;
		if( ! is_preview() && is_single() && is_object( $post ) && isset( $this->ctarr_postype[ $post->post_type ] ) ){
			$new_url = get_permalink();
			$real_url = $this->get_current_url();
			if( substr_count( $new_url, '/' ) != substr_count( $real_url, '/' ) && strstr( $real_url, $new_url ) == false ){
				remove_filter( 'post_type_link', array( $this, 'remove_slug' ), 10 );
				$old_url = get_permalink();
				add_filter( 'post_type_link', array( $this, 'remove_slug' ), 10, 3 );
				$fixed_url = str_replace( $old_url, $new_url, $real_url );
				wp_redirect( $fixed_url, 301 );
			}
		}
	}
}

function rcptb_remove_plugin_options(){
	delete_option('ctarr_postype');
}

add_action( 'init', array( 'ctbcls_remove_base_slug', 'init' ), 99 );
register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_uninstall_hook( __FILE__, 'rcptb_remove_plugin_options' );