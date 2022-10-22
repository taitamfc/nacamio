<?php
/*
Plugin Name: Lizado Crm
Plugin URI: #
Description: #.
Author: Crawler
Version: 1.7.2
Author URI: http://ma.tt/
*/

require __DIR__ . '/core/app.php';
define("URI", $_SERVER['REQUEST_URI']);
define("ROOT", dirname(__FILE__));
define("APP_PLUGIN_NAME", 'LizadoCrm');
define("APP_PLUGIN_PAGE", 'LizadoCrm');
define("APP_PLUGIN_URL",plugin_dir_url( __FILE__ ));
define("APP_PLUGIN_ASSETS_URL",APP_PLUGIN_URL.'/public/assets');

register_activation_hook(__FILE__, 'plugin_activate');
register_deactivation_hook(__FILE__, 'plugin_deactivate');

require_once ROOT . '/core/helpers/WoocommerceHelper.php';
require_once ROOT . '/core/helpers/Cron.php';
require_once ROOT . '/core/helpers/MysqliDb.php';
require_once ROOT . '/core/helpers/Curl.php';
require_once ROOT . '/core/helpers/KM_Download_Remote_Image.php';
require_once ROOT . '/app/libraries/simple_html_dom.php';
require_once ROOT . '/core/helpers/shortcodes/ux_product_attribute.php';
require_once ROOT . '/core/helpers/shortcodes/theme_site_info.php';
require_once ROOT . '/core/helpers/shortcodes/theme_ux_custom_products.php';
//Shortcodes

global $cr_user_id,$AppDB;
$cr_user_id = get_current_user_id();
$AppDB 	= new MysqliDb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$app 	= new App();

function pr( $params ){
    echo '<pre>';
    print_r($params);
    echo '</pre>';
}
function dd( $params ){
    pr( $params );
    die();
}

function plugin_activate(){
    error_log("=======> Plugin activating.");
    if ( ! wp_get_schedule( 'app_schedule_hook_5min' ) ) {
        wp_schedule_event( time(), '5min', 'app_schedule_hook_5min' );
    }
    if ( ! wp_get_schedule( 'app_schedule_hook_30min' ) ) {
        wp_schedule_event( time(), '30min', 'app_schedule_hook_30min' );
    }
    if ( ! wp_get_schedule( 'app_schedule_hook_1min' ) ) {
        wp_schedule_event( time(), '1min', 'app_schedule_hook_1min' );
    }
    if ( ! wp_get_schedule( 'app_scheduleTriggered60min' ) ) {
        wp_schedule_event( time(), '60min', 'app_scheduleTriggered60min' );
    }
    if ( ! wp_get_schedule( 'app_scheduleTriggered12h' ) ) {
        wp_schedule_event( time(), '12h', 'app_scheduleTriggered12h' );
    }
    if ( ! wp_get_schedule( 'app_scheduleTriggered24h' ) ) {
        wp_schedule_event( time(), '24h', 'app_scheduleTriggered24h' );
    }
    error_log("=======> Plugin activated.");
}
function plugin_deactivate(){
    error_log("=======> Plugin deactivating.");
    wp_clear_scheduled_hook('app_schedule_hook_5min');
    wp_clear_scheduled_hook('app_schedule_hook_30min');
    wp_clear_scheduled_hook('app_schedule_hook_1min');
    wp_clear_scheduled_hook('app_scheduleTriggered60min');
    wp_clear_scheduled_hook('app_scheduleTriggered12h');
    wp_clear_scheduled_hook('app_scheduleTriggered24h');
    error_log("=======> Plugin deactivated.");
}