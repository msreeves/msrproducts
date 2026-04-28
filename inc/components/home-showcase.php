<?php
/**
 * Homepage modern showcase blocks.
 *
 * @package msrproducts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/?post_type=product' );
if ( ! $shop_url ) {
	$shop_url = home_url( '/?post_type=product' );
}
$contact_url = function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'contact', home_url( '/' ) ) : home_url( '/' );
if ( ! function_exists( 'msrproducts_home_field' ) ) {
	/**
	 * Read homepage ACF field with fallback when ACF is missing or empty.
	 *
	 * @param string $key     Field key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	function msrproducts_home_field( $key, $default ) {
		if ( function_exists( 'get_field' ) ) {
			$page_id = (int) get_option( 'page_on_front' );
			if ( $page_id > 0 ) {
				$value = get_field( $key, $page_id );
				if ( is_string( $value ) ) {
					$value = trim( $value );
				}
				if ( ! empty( $value ) ) {
					return $value;
				}
			}
		}
		return $default;
	}
}

$hero_eyebrow = (string) msrproducts_home_field( 'home_hero_eyebrow', 'Portfolio-first product showcase' );
$hero_title   = (string) msrproducts_home_field( 'home_hero_title', 'Modern product experiences with technical depth and measurable outcomes.' );
$hero_copy    = (string) msrproducts_home_field( 'home_hero_copy', 'Browse project work, compare approaches, and request collaboration details directly from each product page.' );

$process_title = (string) msrproducts_home_field( 'home_process_title', 'How collaboration works' );
$process_cards = array(
	array(
		'icon'  => (string) msrproducts_home_field( 'home_process_1_icon', 'fa-solid fa-magnifying-glass' ),
		'title' => (string) msrproducts_home_field( 'home_process_1_title', '1. Discover' ),
		'copy'  => (string) msrproducts_home_field( 'home_process_1_copy', 'Review portfolio projects, technical blueprints, and business impact summaries.' ),
	),
	array(
		'icon'  => (string) msrproducts_home_field( 'home_process_2_icon', 'fa-solid fa-pen-ruler' ),
		'title' => (string) msrproducts_home_field( 'home_process_2_title', '2. Define' ),
		'copy'  => (string) msrproducts_home_field( 'home_process_2_copy', 'Align scope, constraints, and timeline with a practical delivery roadmap.' ),
	),
	array(
		'icon'  => (string) msrproducts_home_field( 'home_process_3_icon', 'fa-solid fa-rocket' ),
		'title' => (string) msrproducts_home_field( 'home_process_3_title', '3. Deliver' ),
		'copy'  => (string) msrproducts_home_field( 'home_process_3_copy', 'Ship polished outcomes with reusable assets, handover notes, and support options.' ),
	),
);

$editorial_title = (string) msrproducts_home_field( 'home_editorial_title', 'Design stories and collaboration highlights' );
$editorial_cards = array(
	array(
		'image' => msrproducts_home_field( 'home_editorial_1_image', array() ),
		'title' => (string) msrproducts_home_field( 'home_editorial_1_title', 'Case Study Spotlight' ),
		'copy'  => (string) msrproducts_home_field( 'home_editorial_1_copy', 'From problem framing to implementation impact, every project page includes practical context for technical and design decisions.' ),
		'label' => (string) msrproducts_home_field( 'home_editorial_1_label', 'Read project stories' ),
		'url'   => (string) msrproducts_home_field( 'home_editorial_1_url', $shop_url ),
	),
	array(
		'image' => msrproducts_home_field( 'home_editorial_2_image', array() ),
		'title' => (string) msrproducts_home_field( 'home_editorial_2_title', 'Partnership Programs' ),
		'copy'  => (string) msrproducts_home_field( 'home_editorial_2_copy', 'Collaborate on product UX, front-end delivery, and scalable content systems with a portfolio-first workflow.' ),
		'label' => (string) msrproducts_home_field( 'home_editorial_2_label', 'Discuss partnerships' ),
		'url'   => (string) msrproducts_home_field( 'home_editorial_2_url', $contact_url ),
	),
);

$trust_cards = array(
	array(
		'icon'  => (string) msrproducts_home_field( 'home_trust_1_icon', 'fa-regular fa-clock' ),
		'stat'  => (string) msrproducts_home_field( 'home_trust_1_stat', '48h' ),
		'label' => (string) msrproducts_home_field( 'home_trust_1_label', 'Average response time' ),
	),
	array(
		'icon'  => (string) msrproducts_home_field( 'home_trust_2_icon', 'fa-solid fa-briefcase' ),
		'stat'  => (string) msrproducts_home_field( 'home_trust_2_stat', 'Portfolio' ),
		'label' => (string) msrproducts_home_field( 'home_trust_2_label', 'No checkout, collaboration-only' ),
	),
	array(
		'icon'  => (string) msrproducts_home_field( 'home_trust_3_icon', 'fa-solid fa-diagram-project' ),
		'stat'  => (string) msrproducts_home_field( 'home_trust_3_stat', 'End-to-end' ),
		'label' => (string) msrproducts_home_field( 'home_trust_3_label', 'Research, design, implementation' ),
	),
	array(
		'icon'  => (string) msrproducts_home_field( 'home_trust_4_icon', 'fa-solid fa-universal-access' ),
		'stat'  => (string) msrproducts_home_field( 'home_trust_4_stat', 'Accessible' ),
		'label' => (string) msrproducts_home_field( 'home_trust_4_label', 'Mobile-first and WCAG-focused delivery' ),
	),
);

$featured_products = new WP_Query(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$product_terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'number'     => 6,
	)
);
if ( is_wp_error( $product_terms ) ) {
	$product_terms = array();
}
?>
<section class="home-hero-banner" aria-labelledby="home-hero-title">
	<div class="container">
		<div class="home-hero-banner__content">
			<p class="eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<h2 id="home-hero-title"><?php echo esc_html( $hero_title ); ?></h2>
			<p><?php echo esc_html( $hero_copy ); ?></p>
			<div class="home-hero-banner__actions">
				<a class="button" href="<?php echo esc_url( $shop_url ); ?>">Explore projects</a>
				<a class="button button--ghost" href="<?php echo esc_url( $contact_url ); ?>">Start a conversation</a>
			</div>
		</div>
	</div>
</section>

<section class="home-image-led-grid" aria-labelledby="home-image-led-grid-title">
	<div class="container">
		<div class="home-image-led-grid__head">
			<h2 id="home-image-led-grid-title">Featured project snapshots</h2>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="home-inline-link">View all projects</a>
		</div>
		<div class="home-image-led-grid__cards">
			<?php if ( $featured_products->have_posts() ) : ?>
				<?php while ( $featured_products->have_posts() ) : $featured_products->the_post(); ?>
					<?php
					$thumb_id = get_post_thumbnail_id();
					$img_url  = '';
					$price_html = '';
					if ( $thumb_id && function_exists( 'msrproducts_attachment_file_exists' ) && msrproducts_attachment_file_exists( $thumb_id ) ) {
						$img_url = wp_get_attachment_image_url( $thumb_id, 'large' );
					}
					if ( ! $img_url ) {
						$img_url = function_exists( 'msrproducts_placeholder_image_url' ) ? msrproducts_placeholder_image_url() : '';
					}
					if ( function_exists( 'wc_get_product' ) ) {
						$product = wc_get_product( get_the_ID() );
						if ( $product instanceof WC_Product ) {
							$active_price  = $product->get_price();
							$regular_price = $product->get_regular_price();
							if ( $active_price !== '' ) {
								$price_html = wc_price( (float) $active_price );
								if ( $regular_price !== '' && $regular_price !== $active_price ) {
									$price_html = '<del>' . wc_price( (float) $regular_price ) . '</del> <ins>' . wc_price( (float) $active_price ) . '</ins>';
								}
							}
						}
					}
					?>
					<article class="home-image-card">
						<a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
							<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" decoding="async" />
							<div class="home-image-card__copy">
								<h3><?php the_title(); ?></h3>
								<?php if ( $price_html !== '' ) : ?>
									<span class="home-image-card__price"><?php echo wp_kses_post( $price_html ); ?></span>
								<?php else : ?>
									<span>Request information</span>
								<?php endif; ?>
							</div>
						</a>
					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p class="msr-empty-state">No featured projects available yet.</p>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="home-category-tiles" aria-labelledby="home-category-tiles-title">
	<div class="container">
		<div class="home-image-led-grid__head">
			<h2 id="home-category-tiles-title">Browse by category</h2>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="home-inline-link">Explore categories</a>
		</div>
		<div class="home-category-tiles__grid">
			<?php foreach ( $product_terms as $term ) : ?>
				<?php
				if ( ! ( $term instanceof WP_Term ) ) {
					continue;
				}
				$term_url = function_exists( 'msrproducts_get_term_archive_url' ) ? msrproducts_get_term_archive_url( $term ) : home_url( '/?post_type=product' );
				$term_thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
				$term_img_url  = '';
				if ( $term_thumb_id > 0 && function_exists( 'msrproducts_attachment_file_exists' ) && msrproducts_attachment_file_exists( $term_thumb_id ) ) {
					$term_img_url = (string) wp_get_attachment_image_url( $term_thumb_id, 'medium_large' );
				}
				if ( ! $term_img_url ) {
					$term_img_url = function_exists( 'msrproducts_placeholder_image_url' ) ? msrproducts_placeholder_image_url() : '';
				}
				?>
				<a class="home-category-tile" href="<?php echo esc_url( $term_url ); ?>">
					<span class="home-category-tile__image-wrap">
						<img src="<?php echo esc_url( $term_img_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy" decoding="async" />
					</span>
					<span class="home-category-tile__name"><?php echo esc_html( $term->name ); ?></span>
					<span class="home-category-tile__meta"><?php echo esc_html( (string) $term->count ); ?> projects</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="home-editorial-banners" aria-labelledby="home-editorial-banners-title">
	<div class="container">
		<h2 id="home-editorial-banners-title"><?php echo esc_html( $editorial_title ); ?></h2>
		<div class="home-editorial-banners__grid">
			<?php foreach ( $editorial_cards as $card ) : ?>
				<?php
				$image_url = '';
				$image_alt = '';
				if ( is_array( $card['image'] ) && ! empty( $card['image']['url'] ) ) {
					$image_url = (string) $card['image']['url'];
					$image_alt = ! empty( $card['image']['alt'] ) ? (string) $card['image']['alt'] : (string) $card['title'];
				}
				if ( ! $image_url && function_exists( 'msrproducts_placeholder_image_url' ) ) {
					$image_url = msrproducts_placeholder_image_url();
					$image_alt = (string) $card['title'];
				}
				?>
				<article class="home-editorial-banner">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" decoding="async" />
					<?php endif; ?>
					<h3><?php echo esc_html( $card['title'] ); ?></h3>
					<p><?php echo esc_html( $card['copy'] ); ?></p>
					<a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['label'] ); ?></a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="home-process" aria-labelledby="home-process-title">
	<div class="container">
		<h2 id="home-process-title"><?php echo esc_html( $process_title ); ?></h2>
		<div class="home-process__grid">
			<?php foreach ( $process_cards as $card ) : ?>
				<article>
					<i class="<?php echo esc_attr( $card['icon'] ); ?>" aria-hidden="true"></i>
					<h3><?php echo esc_html( $card['title'] ); ?></h3>
					<p><?php echo esc_html( $card['copy'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="home-trust-strip" aria-label="Proof and trust">
	<div class="container">
		<ul>
			<?php foreach ( $trust_cards as $card ) : ?>
				<li>
					<i class="<?php echo esc_attr( $card['icon'] ); ?>" aria-hidden="true"></i>
					<strong><?php echo esc_html( $card['stat'] ); ?></strong>
					<span><?php echo esc_html( $card['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="home-faq-preview" aria-labelledby="home-faq-preview-title">
	<div class="container">
		<h2 id="home-faq-preview-title">Common questions</h2>
		<?php echo do_shortcode( '[msrproducts_faq]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
