<?php
/**
 * Template Name: Posts Template
 *
 * @package WordPress
 * @subpackage msrproducts
 * @since msrproducts 1.0
 */
get_header();
?>
<section class="products-template">
	<div class="container">
		<?php get_template_part( 'inc/controllers/searchbar' ); ?>
		<?php get_template_part( 'inc/components/filterproducts' ); ?>
	</div>
</section>
<?php
get_footer();