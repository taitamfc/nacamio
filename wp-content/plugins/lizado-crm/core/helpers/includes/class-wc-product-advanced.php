<?php

/**
 * Advanced Product Type
 */
class WC_Product_Advanced extends WC_Product_Simple {
    
    /**
     * Return the product type
     * @return string
     */
    public function get_type() {
        return 'advanced';
    }

    /**
     * Returns the product's active price.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string price
     */
    public function get_price( $context = 'view' ) {
        $object = WoocommerceHelper::get_system_product($this->get_id(),['price']);
        if($object){
            return $object->price;
        }else{
            return $this->get_prop( 'price', $context );
        }
    }

    public function get_image_id($context = 'view'){
        //return product id to filter woocommerce_product_get_image
        return $this->get_id();
    }

    public function get_available_variations($context = 'view'){
        $object = WoocommerceHelper::get_system_product($this->get_id(),['id','title'],true);
        $available_variations = $object->variants;
        return $available_variations;
    }

    public function get_variation_attributes($context = 'view'){
        $object = WoocommerceHelper::get_system_product($this->get_id(),['id','title'],true);
        $pa_color = $object->pa_color;
        $pa_size = ($object->size_supported) ? $object->size_supported : $object->pa_size;
        $attributes = [];
		$attributes['pa_color'] = $pa_color;
		$attributes['pa_size'] = $pa_size;
        return $attributes;
    }


    public function get_attributes($context = 'view'){
        return [];
    }
    public function get_variation_default_attributes() {
		return $this->_get_product_attributes();
	}

    public function get_variation_default_attribute( $attribute_name ) {
        
		$defaults       = $this->_get_product_attributes();
		$attribute_name = sanitize_title( $attribute_name );
		return isset( $defaults[ $attribute_name ] ) ? $defaults[ $attribute_name ] : '';
	}

    public function get_default_attributes($context = 'view'){
        $attributes = $this->_get_product_attributes();
        $return = [
            'pa_color' => ( isset($attributes['pa_color']) ) ? current($attributes['pa_color']) : '',
            'pa_size' => ( isset($attributes['pa_size']) ) ? current($attributes['pa_size']) : ''
        ];
        return $return;
    }

    private function _get_product_attributes(){
        $object = WoocommerceHelper::get_system_product($this->get_id(),['id','title'],true);
        $pa_color = $object->pa_color;
        $pa_size = $object->pa_size;
        $attributes = [];
		$attributes['pa_color'] = $pa_color;
		$attributes['pa_size'] = $pa_size;
        return $attributes;
    }

    
    
}