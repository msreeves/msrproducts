<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package msrproducts
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<section class="archive-modern">
				<div class="container">
					<div class="panel">
						<h1><?php the_archive_title(); ?></h1>
						<h3><?php the_archive_description(); ?></h3>
						<?php get_template_part( 'inc/controllers/searchbar' ); ?>
					</div>
					<div class="row msr-card-grid products-card-grid">
						<?php while ( have_posts() ) : the_post(); ?>
							<?php
							$post_type = get_post_type();
							if ( $post_type === 'product' ) {
								get_template_part( 'template-parts/cards/product-card' );
							} else {
								get_template_part( 'template-parts/content', 'search' );
							}
							?>
						<?php endwhile; ?>
					</div>
					<section class="pagination-wrap">
						<?php
						the_posts_pagination(
							array(
								'mid_size'  => 2,
								'prev_text' => __( 'Previous', 'msrproducts' ),
								'next_text' => __( 'Next', 'msrproducts' ),
							)
						);
						?>
					</section>
				</div>
			</section>

		<?php else : ?>

			get_template_part( 'template-parts/content', 'none' );

		<?php endif; ?>
		?>
	</main><!-- #main -->

<?php
get_footer();
