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
<main id="site-content" class="msr-home">
<?php
$page = msrproducts_index_rich_home_post();

if ( $page instanceof WP_Post ) {
	global $post;
	$post = $page;
	setup_postdata( $post );
	get_template_part( 'template-parts/sections/home-showcase' );
	get_template_part( 'template-parts/sections/partners' );
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
