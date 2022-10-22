<?php
class AppUrlHelper {
	public function __construct(){
		echo __METHOD__;
	}
    public static function build ( $options ) {
        $link = admin_url('admin.php?page='.APP_PLUGIN_PAGE);
        foreach ($options as $key => $option) {
        	$link .= '&'.$key.'='.$option;
        }
        return $link;
    }
    public static function ajaxBuild ( $options ) {
        $link = admin_url('admin-ajax.php?action='.APP_PLUGIN_PAGE);
        foreach ($options as $key => $option) {
            $link .= '&'.$key.'='.$option;
        }
        return $link;
    }
}