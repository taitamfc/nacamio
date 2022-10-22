<?php
add_shortcode("ux_product_attribute", "ux_product_attribute");
function ux_product_attribute($atts, $content = null, $tag = '' ){
    $options = [
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
        'hide_empty' => 0,
        'parent'     => 'false',
        'offset' => '',
        'show_count' => 'true',
        'tax' => 'pa_event',
        'type' => 'default',
    ];
    extract($options);
    extract($atts);
    $args = array(
        'taxonomy' 	 => $tax,
        'orderby'    => $orderby,
        'order'      => $order,
        'hide_empty' => $hide_empty,
        'pad_counts' => true,
        'child_of'   => 0,
        'offset'    => $offset
    );
	
	if($type == 'up-comming'){
		$args['meta_query'] = [
            [
               'key'       => 'start_date',
               'value'     => (int)date('md' ,strtotime('- 5 days') ),
               'compare'   => '>='
            ]
        ];
	}
	
    $thumbnail_size   = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
    $product_categories = get_terms( $args );
    $slider = false;
    ob_start();?>
    <?php if( $slider ):?>
    <div 
        class="row large-columns-4 medium-columns-3 small-columns-2 row-small"
    >
    <?php else: ?>
    <div 
        class="row large-columns-4 medium-columns-3 small-columns-2 row-small slider row-slider slider-nav-reveal slider-nav-push"  
        data-flickity-options='{"imagesLoaded": true, "groupCells": "100%", "dragThreshold" : 5, "cellAlign": "left","wrapAround": true,"prevNextButtons": true,"percentPosition": true,"pageDots": false, "rightToLeft": false, "autoPlay" : false}'
    >
    <?php endif;?>    
        <?php  foreach ( $product_categories as $category ) :?>
        <?php
            $thumbnail_id = get_field('image', $category->taxonomy . '_' . $category->term_id);
            if ( $thumbnail_id ) {
                $image = wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size);
                $image = $image ? $image[0] : wc_placeholder_img_src();
            } else {
                $image = wc_placeholder_img_src();
            }    
        ?>
        <div class="product-category col" >
            <div class="col-inner">
                <a href="<?= get_term_link($category);?>">
                    <div class="box box-category has-hover box-default ">
                        <div class="box-image" style="border-radius:5%;">
                            <div class="image-zoom-fade image-cover" style="padding-top:75%;">
                                <img 
                                    class="lazy-load" 
                                    src="data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%20300%20300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3C%2Fsvg%3E" 
                                    data-src="<?= $image; ?>" 
                                    alt="<?= $category->name; ?>" 
                                    width="300" 
                                    height="300" 
                                />                                                      
                            </div>
                        </div>
                        <div class="box-text text-center" >
                            <div class="box-text-inner">
                                <h5 class="uppercase header-title">
                                    <?= $category->name; ?>                    
                                </h5>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php endforeach;?>
	
    </div>
    <?php
    return ob_get_clean();
}
