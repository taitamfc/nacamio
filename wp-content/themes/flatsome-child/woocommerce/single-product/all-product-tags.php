<?php
$all_product_tags = get_terms( 'product_tag' );
?>
<div class="product-tag-item">
    <h3 class="product-tag-heading">
        All Product Tags
    </h3>
    <div class="product-tag-search flex-b flex-w active">
        
        <div class="product-search-content flex-b flex-w">
            <?php foreach( $all_product_tags as $all_product_tag ):?>
            <a class="product-tag-link" href="<?= get_term_link($all_product_tag);?>" style="">
                <?= $all_product_tag->name;?>
            </a>
            <?php endforeach;?>
        </div>
        <div class="more-tags">
            <button class="button product-tag-link">
                +
                More
            </button>
        </div>
    </div>
</div>