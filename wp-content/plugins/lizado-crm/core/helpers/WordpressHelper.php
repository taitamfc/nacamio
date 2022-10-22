<?php
class WordpressHelper {
	public function __construct(){
		add_action('admin_enqueue_scripts', array( $this,'app_admin_style' ) );
	}
	function app_admin_style() {
		$theme_version = '1.0.0';

		// 2. Scripts.
		wp_enqueue_script( 'axios', '//unpkg.com/axios/dist/axios.min.js', array(), $theme_version, false );
		//wp_enqueue_script( 'vue', '//cdn.jsdelivr.net/npm/vue@2', array(), $theme_version, false );
		wp_enqueue_script( 'vue', '//cdn.jsdelivr.net/npm/vue@2/dist/vue.js', array(), $theme_version, false );
		
		wp_enqueue_style('app-bootstrap', APP_PLUGIN_ASSETS_URL.'/css/bootstrap.css');
		wp_enqueue_style('app-custom', APP_PLUGIN_ASSETS_URL.'/css/custom.css');
		wp_enqueue_script('app-bootstrap-js', APP_PLUGIN_ASSETS_URL.'/js/bootstrap.min.js',array(),false,true);
		wp_enqueue_script('app-function-js', APP_PLUGIN_ASSETS_URL.'/js/function.js',array(),false,false);
	}
}

