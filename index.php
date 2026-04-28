<?php
/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package msrproducts
 */

get_header();

?>
<main id="site-content">
<?php
$page = msrproducts_index_rich_home_post();

if ( $page instanceof WP_Post ) {
	global $post;
	$post = $page;
	setup_postdata( $post );
	?>
  <section>
    <div class="container">
      <div class="panel">
   <?php the_content(); ?>
   </div>
	<?php get_template_part( 'inc/components/home-showcase' ); ?>
    <?php get_template_part( 'inc/components/filterproducts' ); ?>
    <?php get_template_part( 'inc/components/publicationlist' ); ?>
    <?php get_template_part( 'templates/partials/leaderboard/billboard' ); ?>
    <?php get_template_part( 'inc/components/partners' ); ?>
</div>
</section>
	<?php
	wp_reset_postdata();
} elseif ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		?>
  <section>
   <div class="container">
    <div class="panel">
      <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
</div>
  </div>
</section>
		<?php
	}
}
?>
</main>
<?php
get_footer();
