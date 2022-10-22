<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post,$AppDB,$product_type;
$sql = "SELECT product_type FROM crawl_products WHERE wordpress_post_id = $post->ID LIMIT 1";
$product_type = $AppDB->rawQueryValue($sql);

?>
<?php include( dirname(__FILE__).'/the-related.php' );?>
<div class="show-for-small">
    <?php include( dirname(__FILE__).'/same-design.php' );?>
	<div class="md-bought-together-wrapper">

	</div>
    <?php //include( dirname(__FILE__).'/you-may-also-like.php' );?>
</div>


<?php include( dirname(__FILE__).'/in-the-same-event.php' );?>
<?php include( dirname(__FILE__).'/explore-ongoing-events.php' );?>
<?php include( dirname(__FILE__).'/best-seller.php' );?>
<div class="product-tag-wrapper">
    <!-- <div class="product-tag-item">
        <h3 class="product-tag-heading">
            T-Shirts Tags
        </h3>
        <div class="product-tag-search flex-b flex-w active">
            <div class="product-search-content flex-b flex-w">
                <a class="product-tag-link" href="/t-shirts/policeman" style="">
                    policeman T-Shirts
                </a>
            </div>
            <div class="more-tags">
                <button class="button product-tag-link">
                    +
                    More
                </button>
            </div>
        </div>
    </div> -->
    <?php include( dirname(__FILE__).'/all-product-tags.php' );?>
    <!-- <div class="product-tag-item">
        <h3 class="product-tag-heading">
            Other Products
        </h3>
        <div class="product-tag-search flex-b flex-w active">
            <div class="product-search-content flex-b flex-w">
                <a class="product-tag-link" href="/tank-tops/policeman" style="">
                    policeman tank tops </a>
               
            </div>
            <div class="more-tags">
                <button class="button product-tag-link">
                    +
                    More
                </button>
            </div>
        </div>
    </div> -->
	<div class="site-w breadcrumb-box">
		<ul class="report-list flex-b align-c">
			<li class="report-item flex-b align-c">
				<span class="report-icon">
					<svg width="24" height="24" viewBox="0 0 42 34" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<defs>
					<path d="M16.232 9.92c.382-1.605 1.638-1.962 2.81-.788l26.25 26.248c1.17 1.17.81 2.43-.79 2.812l-34.182 8.13c-1.606.38-2.598-.618-2.217-2.22l8.13-34.18z" id="a"></path>
					<mask id="b" x="0" y="0" width="37.959" height="37.962" fill="#fff">
					<use xlink:href="#a"></use>
					</mask>
					</defs>
					<g transform="translate(-6 -1)" fill="none" fill-rule="evenodd">
					<use stroke="#8C95A5" mask="url(#b)" stroke-width="5" transform="rotate(135 27 27.42)" xlink:href="#a"></use><text font-family="ArialRoundedMTBold, Arial Rounded MT Bold" font-size="18" fill="#8C95A5">
					<tspan x="24" y="27">!</tspan>
					</text>
					</g>
					</svg>
				</span>
				<a href="<?= get_the_permalink(711);?>?id=<?= $post->ID; ?>" rel="nofollow" target="_blank" style="margin-left: 3px;">Report content</a> 
			</li>
			<li class="report-item flex-b align-c">
				<a href="<?= get_the_permalink(266);?>" rel="nofollow">Copyright infringement</a> 
			</li>
		</ul>
	</div>
</div>