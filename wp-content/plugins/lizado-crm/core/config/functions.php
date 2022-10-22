<?php
if( !function_exists('apr') ){
	function apr( $array ){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}

if( !function_exists('app_selected') ){
	function app_selected( $compare, $value , $echo = true ){
		if( $compare == $value ){
			if( $echo ){
				echo 'selected';
			}
		}
	}
}
