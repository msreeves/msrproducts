<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package msrproducts
 */

get_header();
?>

	<main id="primary" class="site-main search-results-page">

		<?php if ( have_posts() ) : ?>

			<header class="page-header search-results-header">
				<div class="container">
					<p class="search-results-header__eyebrow"><?php esc_html_e( 'Search', 'msrproducts' ); ?></p>
					<h1 class="page-title">
						<?php
						printf( esc_html__( 'Results for "%s"', 'msrproducts' ), esc_html( get_search_query() ) );
						?>
					</h1>
					<p class="search-results-header__count">
						<?php
						global $wp_query;
						$result = $wp_query->found_posts === 1 ? 'result' : 'results';
						echo esc_html( $wp_query->found_posts . ' ' . $result . ' found' );
						?>
					</p>
				</div>
			</header><!-- .page-header -->
			<div class="container">
				<?php get_template_part( 'inc/controllers/searchbar' ); ?>
				<div class="row search-results-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'search' );
			endwhile;
			?>
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

		<?php else : ?>
			<div class="container">
				<?php get_template_part( 'inc/controllers/searchbar' ); ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			</div>

		<?php endif; ?>
	</main><!-- #main -->

<?php
get_footer();
