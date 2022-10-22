<?php
    global $wpdb;
    $product_event_ids = [];
    $p_tags = get_the_terms( get_the_ID(),'product_tag' );
    $p_tag_ids = [];
    if( $p_tags && count($p_tags) ){
        foreach( $p_tags as $p_tag ){
            $p_tag_ids[] = $p_tag->term_id;
        }
    }
    if( count($p_tag_ids) ){
        $sql = "SELECT ID FROM `{$wpdb->prefix}posts`
        JOIN {$wpdb->prefix}term_relationships ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}term_relationships.object_id
        WHERE {$wpdb->prefix}posts.post_status = 'publish' AND {$wpdb->prefix}posts.post_type = 'product'
        AND {$wpdb->prefix}term_relationships.term_taxonomy_id IN ( " .implode(',',$p_tag_ids). " )
        GROUP BY {$wpdb->prefix}posts.ID LIMIT 10;";
        $product_event_ids = $AppDB->rawQueryValue($sql);
    }
    ?>
<?php if( $product_event_ids && count($product_event_ids) ):?>    
<div class="product-section">
    <div class="container section-title-container">
        <h3 class="section-title section-title-normal">
            <span class="section-title-main">In the same event</span>
        </h3>
    </div>
    <?php
    echo do_shortcode('[ux_products style="normal" columns="6" slider_nav_style="circle" slider_nav_color="light" tags="" ids="'. implode(',',$product_event_ids) .'"]');
    ?>
</div>
<?php endif;?>