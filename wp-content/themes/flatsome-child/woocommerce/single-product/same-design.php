<?php
//cùng product_type và 3 từ khóa đầu
global $post,$AppDB,$product_type;
$cr_product_id = $post->ID;
$title = implode(" ", array_splice(explode(" ", $post->post_title), 0, 3));
$sql = " SELECT wordpress_post_id FROM crawl_products 
WHERE wordpress_post_id IS NOT NULL 
AND wordpress_post_id != $cr_product_id 
AND product_type != '$product_type' 
AND title LIKE '%$title%' LIMIT 5";
$same_design = $AppDB->rawQueryValue($sql);
?>
<?php if( $same_design && count($same_design) ):?>
<div class="available-product-wrapper">
    <div class="product-section">
        <div class="container section-title-container">
            <h3 class="section-title section-title-normal">
                <span class="section-title-main">The design is also available on</span>
            </h3>
        </div>
        <?php
            echo do_shortcode('[ux_products style="normal" columns="4" slider_nav_style="circle" slider_nav_color="light" tags="" ids="'. implode(',',$same_design) .'"]');
        ?>
    </div>
</div>
<?php endif;?>