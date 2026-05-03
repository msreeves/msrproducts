 <?php
/**
 * Layouts > Partners
 *
 * @package WordPress
 * @subpackage QORP
 */

?>
<?php
$args = array(
	'post_type'      => 'partner',
	'posts_per_page' => 12,
	'orderby'        => 'title',
	'order'          => 'ASC',
);

$all_partners = new WP_Query( $args );
?>
<section class="partners-showcase" aria-labelledby="partners-showcase-heading">
	<div class="container">
		<div class="partners-showcase__head">
			<h2 id="partners-showcase-heading">Trusted by brands and retailers</h2>
			<p>Selected collaborators across product, retail, and digital design programs.</p>
		</div>
		<div class="partners-showcase__grid">
			<?php if ( $all_partners->have_posts() ) : ?>
				<?php while ( $all_partners->have_posts() ) : $all_partners->the_post(); ?>
					<?php get_template_part( 'templates/partials/post-listing/listing-partner' ); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<?php
				$placeholders = array( 'Brand One', 'Retail Partner', 'Creative Studio', 'Tech Partner', 'Distribution Co', 'Agency Partner' );
				foreach ( $placeholders as $label ) :
					?>
					<article class="partner-placeholder">
						<div class="partner-placeholder__logo" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $label, 0, 2 ) ) ); ?></div>
						<h3><?php echo esc_html( $label ); ?></h3>
					</article>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>