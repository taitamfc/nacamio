<?php
function my_cron_schedules($schedules){
    if(!isset($schedules["1min"])){
        $schedules["1min"] = array(
            'interval' => 1*60,
            'display' => __('Once every 1 minutes'));
    }
    if(!isset($schedules["5min"])){
        $schedules["5min"] = array(
            'interval' => 5*60,
            'display' => __('Once every 5 minutes'));
    }
    if(!isset($schedules["30min"])){
        $schedules["30min"] = array(
            'interval' => 30*60,
            'display' => __('Once every 30 minutes'));
    }
    if(!isset($schedules["60min"])){
        $schedules["60min"] = array(
            'interval' => 60*60,
            'display' => __('Once every 60 minutes'));
    }
    if(!isset($schedules["24h"])){
        $schedules["24h"] = array(
            'interval' => 1440*60,
            'display' => __('Once every 24h '));
    }
    if(!isset($schedules["12h"])){
        $schedules["12h"] = array(
            'interval' => 720*60,
            'display' => __('Once every 12h '));
    }
    return $schedules;
}
add_filter('cron_schedules','my_cron_schedules');

function app_scheduleTriggered1min() {
    $cron = get_field('cron','option');
    if( $cron['product_call_interval'] == 'hourly' ){
        craw_products();
    }
    if( $cron['product_detail_call_interval'] == 'everyminutes' ){
        craw_product_detail();
    }
    if( $cron['import_to_wp_interval'] == 'everyminutes' ){
        import_product_to_wp();
    }
	app_write_to_log( "=======> Start triggered 1min!" );
}
function app_scheduleTriggered60min() {
    app_write_to_log( "=======> Scheduler triggered 60min!" );
    $cron = get_field('cron','option');
    if( $cron['product_call_interval'] == 'hourly' ){
        craw_products();
    }
    if( $cron['product_detail_call_interval'] == 'hourly' ){
        craw_product_detail();
    }
    if( $cron['import_to_wp_interval'] == 'hourly' ){
        import_product_to_wp();
    }
}
function app_scheduleTriggered24h() {
    app_write_to_log( "=======> Scheduler triggered 24h!" );
    $cron = get_field('cron','option');
    if( $cron['product_call_interval'] == 'daily' ){
        craw_products();
    }
    if( $cron['product_detail_call_interval'] == 'daily' ){
        craw_product_detail();
    }
    if( $cron['import_to_wp_interval'] == 'daily' ){
        import_product_to_wp();
    }
}

function app_write_to_log($txt){
    file_put_contents( 
        dirname(__FILE__).'/logs.txt', 
        $txt.PHP_EOL , 
        FILE_APPEND | LOCK_EX
    );
}

add_action( 'app_schedule_hook_1min', 'app_scheduleTriggered1min' );
add_action( 'app_scheduleTriggered24h', 'app_scheduleTriggered24h' );
add_action( 'app_scheduleTriggered60min', 'app_scheduleTriggered60min' );

function craw_products(){
    $call_url   = admin_url('admin-ajax.php').'?action=LizadoCrm&controller=api&task=crawl_products';
    $return     = wp_remote_get( $call_url, array( 'timeout' => 30 ) );
    if( $return ){
        app_write_to_log( "craw_products: ". json_encode($return['body']) );
    }
    return $return;
}

function craw_product_detail(){
    $call_url   = admin_url('admin-ajax.php').'?action=LizadoCrm&controller=api&task=crawl_product_detail';
    $return     = wp_remote_get( $call_url, array( 'timeout' => 30 ) );
    if( $return ){
        app_write_to_log( "craw_product_detail: ". json_encode($return['body']) );
    }
}

function import_product_to_wp(){
    $call_url   = admin_url('admin-ajax.php').'?action=LizadoCrm&controller=api&task=import_product_to_wp';
    $return     = wp_remote_get( $call_url, array( 'timeout' => 30 ) );
    if( $return ){
        app_write_to_log( "import_product_to_wp: ". json_encode($return['body']) );
    }
}

