<?php

/*
 *   Date: 2017-06-01
 * Author: Dawid Yerginyan
 */

class App {
    private $config = [];
    public $db;
    function __construct () {
        // register_activation_hook(__FILE__, array($this, 'activate'));
        // register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        // register_uninstall_hook(__FILE__, array($this, 'delete'));
        add_action('admin_menu', array($this, 'setupAdminMenu'));
        add_action('current_screen', array($this, 'check_to_run'));

        add_action( 'wp_ajax_'.APP_PLUGIN_PAGE, array($this, 'ajax'));
        add_action( 'wp_ajax_nopriv_'.APP_PLUGIN_PAGE, array($this, 'ajax'));

    }

    function activate(){
        
    }
    function deactivate(){
        
    }
    function delete(){
        
    }

    function ajax(){

        $this->app_require('/core/config/functions.php');
        $this->autoload();
        $uri = AppUrlHelper::build([
            'controller'    => $_REQUEST['controller'],
            'action'        => $_REQUEST['task'],
        ]);
        $this->start($uri);
        die();
    }
    function check_to_run(){
        $currentScreen = get_current_screen();
        if( $currentScreen->id === "toplevel_page_".APP_PLUGIN_PAGE ) {
            $this->autoload();
            $this->loadAssets();
            $this->config();
        }
    }

    function setupAdminMenu(){
        $capability = 'manage_options';
        $capability = 'read';
        add_menu_page(
            APP_PLUGIN_NAME,// page title
            APP_PLUGIN_NAME,// menu title
            $capability,// capability
            APP_PLUGIN_PAGE,// slug
            array($this, 'start'),// callback
            'dashicons-building', // icon
            71  // ordering
        );
    }

    function autoload () {
        spl_autoload_register(function ($class) {
            if (file_exists(ROOT . '/core/classes/' . $class . '.php')) {
                require_once ROOT . '/core/classes/' . $class . '.php';
            } else if (file_exists(ROOT . '/core/helpers/' . $class . '.php')) {
                require_once ROOT . '/core/helpers/' . $class . '.php';
            }
        });
    }

    function loadAssets(){
        new WordpressHelper();
    }

    function config () {
        $this->app_require('/core/config/functions.php');
        $this->app_require('/core/config/session.php');
    }

    function app_require ($path) {
        require ROOT . $path;
    }
    function start ( $uri = '' ) {
       
        if( !$uri ){
            $route = explode('/', URI);
        }else{
            $route = explode('/', $uri);
        }
        $route = end($route);
        $route_arr = explode('&', $route);
        $controller = 'Home';
        $action     = 'index';
        $pass       = '';
        $is_ajax    = false;

        if( count($route_arr) > 1 ){
            $route_controller = $route_arr[1];
            $controller = @end( explode('=', $route_controller) );

            if( isset($route_arr[2]) ){
                 $route_action = $route_arr[2];
                 $action = @end( explode('=', $route_action) );
            }
            if( isset($route_arr[3]) ){
                 $route_pass = $route_arr[3];
                 $pass = @end( explode('=', $route_pass) );
            }
        }

        $controller = ucfirst($controller).'Controller';

        if (file_exists(ROOT . '/app/controllers/' . $controller . '.php')) {
            $this->app_require('/app/controllers/' . $controller . '.php');
            $controller = new $controller();
            
            if( $pass ){
                $controller->$action($pass);
            }else{
                $controller->$action();
            }
        } else {
            // echo 'Missing file '.$controller.'.php';
            // $this->require('/app/controllers/HomeController.php');
            // $controller = new HomeController();
            // $controller->index();
        }
    }
}