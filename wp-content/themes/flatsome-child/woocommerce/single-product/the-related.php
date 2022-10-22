<?php
//cùng product_type và 3 từ khóa đầu
global $post,$AppDB,$product_type;
$cr_product_id = $post->ID;
$related_products = [];
$title = implode(" ", array_splice(explode(" ", $post->post_title), 0, 3));
$sql = " SELECT wordpress_post_id FROM crawl_products 
WHERE wordpress_post_id IS NOT NULL 
AND wordpress_post_id != $cr_product_id 
AND product_type = '$product_type' 
AND title LIKE '%$title%' LIMIT 5";
$related_products = $AppDB->rawQueryValue($sql);
?>
<?php if( $related_products && count($related_products) ):?>
    <div class="product-section">
    <div class="container section-title-container">
        <h3 class="section-title section-title-normal">
            <span class="section-title-main">Related products </span>
        </h3>
    </div>
    <?php
    echo do_shortcode('[ux_products style="normal" type="row" columns="6" slider_nav_style="circle" slider_nav_color="light" tags="" ids="'. implode(',',$related_products) .'"]');
    ?>
</div>
<script>
    jQuery( document ).ready( function(){
        // var cr_product_id = '?php $cr_product_id; ?>';
        // var product_types = '?= $product_types; ?>';
        // setTimeout( function(){
        //     product_types = JSON.parse(product_types);
        //     let the_option = '';
        //     jQuery.each( product_types, function(key,val){
        //         if( cr_product_id == val.wordpress_post_id ){
        //             the_option += '<option selected="selected" value="'+val.product_link+'" >'+val.product_type+' ($'+val.price+')</option>';
        //         }else{
        //             the_option += '<option data-href="'+val.product_link+'" value="'+val.product_link+'" >'+val.product_type+' ($'+val.price+')</option>';
        //         }
                
        //     });
        //     let xhtml = `
        //     <tr>
        //     <th class="label">
        //         <label for="pa_style">Style
        //         </label>
        //     </th>
        //     <td class="value">
        //         <select id="pa_style" class="custom-choice" onchange=" window.location.href=this.value ">
        //             ${the_option}
        //         </select>
        //     </td>
        //     </tr>
        //     `;

        //     jQuery('table.variations').prepend(xhtml);

        // },100 );
    });
</script>
<?php endif;?>