<?php
$post_categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
	)
);

if ( is_wp_error( $post_categories ) ) {
	$post_categories = array();
}
$active_filter = isset( $_GET['filter'] ) ? sanitize_key( wp_unslash( $_GET['filter'] ) ) : 'all';
if ( $active_filter === '' ) {
	$active_filter = 'all';
}
?>
<div class="container">
	<div class="post-tabs modern-product-filters" data-filter-shell>
		<div class="filter-sidebar">
			<p class="filter-sidebar__title"><?php echo esc_html( msrproducts_get_catalog_filter_title() ); ?></p>
			<div class="filter-controls-row">
				<button type="button" class="filter-toolbar__reset" data-filter-reset>Reset</button>
				<ul class="filter-nav" id="product-filter-tabs">
					<li class="filter-nav__item <?php echo $active_filter === 'all' ? 'active' : ''; ?>">
						<button type="button" class="filter-nav__btn" data-filter-link="all" aria-pressed="<?php echo $active_filter === 'all' ? 'true' : 'false'; ?>">All</button>
					</li>
					<?php foreach ( $post_categories as $post_category ) : ?>
						<?php if ( ! ( $post_category instanceof WP_Term ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<li class="filter-nav__item <?php echo $active_filter === $post_category->slug ? 'active' : ''; ?>">
							<button type="button" class="filter-nav__btn" data-filter-link="<?php echo esc_attr( $post_category->slug ); ?>" aria-pressed="<?php echo $active_filter === $post_category->slug ? 'true' : 'false'; ?>"><?php echo esc_html( $post_category->name ); ?></button>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="filter-toolbar__sort">
					<label for="project-sort-select">Sort</label>
					<select id="project-sort-select" data-filter-sort>
						<option value="default">Latest</option>
						<option value="az">Name: A-Z</option>
						<option value="za">Name: Z-A</option>
						<option value="price-asc">Price: Low-High</option>
						<option value="price-desc">Price: High-Low</option>
					</select>
				</div>
			</div>
		</div>

		<div class="filter-panes">
			<div class="filter-pane <?php echo $active_filter === 'all' ? 'show active' : ''; ?>" id="all">
				<?php
				$all_posts = new WP_Query(
					array(
						'post_type'      => 'product',
						'posts_per_page' => 8,
						'orderby'        => 'date',
						'order'          => 'DESC',
					)
				);
				?>
				<?php if ( $all_posts->have_posts() ) : ?>
					<div class="row msr-card-grid products-card-grid">
						<?php while ( $all_posts->have_posts() ) : $all_posts->the_post(); ?>
							<?php get_template_part( 'template-parts/cards/product-card' ); ?>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				<?php else : ?>
					<p class="listing-empty"><?php echo esc_html( msrproducts_get_catalog_empty_message() ); ?></p>
				<?php endif; ?>
			</div>

			<?php foreach ( $post_categories as $post_category ) : ?>
				<?php if ( ! ( $post_category instanceof WP_Term ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<div class="filter-pane <?php echo $active_filter === $post_category->slug ? 'show active' : ''; ?>" id="<?php echo esc_attr( $post_category->slug ); ?>">
					<?php
					$posts = new WP_Query(
						array(
							'post_type'      => 'product',
							'posts_per_page' => 8,
							'tax_query'      => array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'slug',
									'terms'    => $post_category->slug,
								),
							),
						)
					);
					?>
					<?php if ( $posts->have_posts() ) : ?>
						<div class="row msr-card-grid products-card-grid">
							<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
								<?php get_template_part( 'template-parts/cards/product-card' ); ?>
							<?php endwhile; ?>
							<?php wp_reset_postdata(); ?>
						</div>
					<?php else : ?>
						<p class="listing-empty">No projects in this category yet.</p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>