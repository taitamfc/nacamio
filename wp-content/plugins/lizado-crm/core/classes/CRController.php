<?php
abstract class CRController {

    private $route = [];

    private $args = 0;

    private $params = [];

    function __construct () {
		
    }
    function loadModel ($path) {
        $path = $path;
        $class = explode('/', $path);
        $class = $class[count($class)-1];
        $path = ucfirst($path);
        $path .= 'Model';
        $class = ucfirst($path);
        require(ROOT . '/app/models/' . $path . '.php');
        $this->$class = new $class;
    }

    function setView ($path, $data = []) {
        if (is_array($data))
            extract($data);
        require(ROOT . '/app/views/' . $path . '.php');
    }


    function element ($path, $data = []) {
        if (is_array($data))
            extract($data);
        require(ROOT . '/app/views/Elements/' . $path . '.php');
    }

    public function refresh() {
        $location = $this->current_url();
        $this->redirect($location);
    }
    public function current_url() {
        return $_SERVER['REQUEST_URI'];
    }

    public function redirect($location, $status=302) {
        if (headers_sent()) {
            $html = '
                <script type="text/javascript">
                    window.location = "'.$location.'";
                </script>';
            echo $html;
        } else {
            wp_redirect($location, $status);
        }
        
        die();

    }
    public function flash($type, $message=null) {
        if (func_num_args() == 1) {
            $message = $this->get_flash($type);
            $this->unset_flash($type);
            return $message;
        }
        $this->set_flash($type, $message);
    }
    private function init_flash() {
        if (!isset($_SESSION['app_mvc_flash'])) {
            $_SESSION['app_mvc_flash'] = array();
        }
    }
    protected function set_flash($type, $message) {
        $this->init_flash();
        $_SESSION['app_mvc_flash'][$type] = $message;
    }
    
    protected function unset_flash($type) {
        $this->init_flash();
        unset($_SESSION['app_mvc_flash'][$type]);
    }
    
    protected function get_flash($type) {
        $this->init_flash();
        $message = empty($_SESSION['app_mvc_flash'][$type]) ? null : $_SESSION['app_mvc_flash'][$type];
        return $message;
    }
    
    public function get_all_flashes() {
        $this->init_flash();
        return $_SESSION['app_mvc_flash'];
    }

}