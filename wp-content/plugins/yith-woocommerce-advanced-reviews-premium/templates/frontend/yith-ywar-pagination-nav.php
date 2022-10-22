<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package       YITH\yit-woocommerce-advanced-reviews\Templates\Frontend
 */

?>
	<div class="woocommerce">

		<nav class="woocommerce-pagination">
			<?php
			/** APPLY_FILTER: woocommerce_pagination_args
			 *
			 * Filter the default pagination arguments in tha pagination template.
			 *
			 * @param array $args Default plugin pagination arguments.
			 */
			echo wp_kses(
				paginate_links(
					apply_filters(
						'woocommerce_pagination_args',
						array(
							'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
							'format'    => '',
							'add_args'  => false,
							'current'   => max( 1, get_query_var( 'paged' ) ),
							'total'     => $max_num_pages,
							'prev_text' => '&larr;',
							'next_text' => '&rarr;',
							'type'      => 'list',
							'end_size'  => 3,
							'mid_size'  => 3,
						)
					)
				),
				'post'
			);
			?>
		</nav>

	</div>
<?php
