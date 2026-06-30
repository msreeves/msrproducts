<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package msrproducts
 */

?>

	<?php
	$compare_url = function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'compare-projects', home_url( '/' ) ) : home_url( '/' );
	$cookie_url  = function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'cookie-policy', home_url( '/' ) ) : home_url( '/' );
	?>
	<footer id="colophon" class="site-footer modern-footer">
		<div class="container-fluid modern-footer__grid">
			<div class="modern-footer__col">
				<h2>Explore</h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-explore-menu',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
			<div class="modern-footer__col">
				<h2>Services</h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-services-menu',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
			<div class="modern-footer__col">
				<h2>Legal</h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-legal-menu',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
			<div class="modern-footer__col">
				<h2>Follow</h2>
				<div class="modern-footer__social">
					<?php
					$menu_locations = get_nav_menu_locations();
					if ( isset( $menu_locations['social'] ) ) {
						$social_menu = wp_get_nav_menu_items( $menu_locations['social'] );
						if ( is_array( $social_menu ) ) {
							foreach ( $social_menu as $item ) {
								$title = sanitize_title( $item->title );
								echo '<a href="' . esc_url( $item->url ) . '" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-' . esc_attr( $title ) . '" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html( $item->title ) . '</span></a>';
							}
						}
					}
					?>
				</div>
				<?php if ( msrproducts_show_footer_showcase_note() ) : ?>
					<p class="modern-footer__note"><?php echo esc_html( msrproducts_get_footer_showcase_note() ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</footer><!-- #colophon -->
	<div id="compare-preview-modal" class="compare-preview-modal" hidden aria-hidden="true" aria-labelledby="compare-preview-title">
		<div class="compare-preview-modal__dialog">
			<h2 id="compare-preview-title"><?php echo esc_html( msrproducts_get_compare_modal_title() ); ?></h2>
			<p><?php echo esc_html( msrproducts_get_compare_modal_lead() ); ?></p>
			<a class="button" id="compare-preview-link" href="<?php echo esc_url( $compare_url ); ?>"><?php echo esc_html( msrproducts_get_compare_modal_cta() ); ?></a>
			<button type="button" class="button button--ghost" id="compare-preview-close">Close</button>
		</div>
	</div>
	<div id="cookie-consent-banner" class="cookie-consent-banner" hidden aria-live="polite">
		<p><?php echo esc_html( msrproducts_get_cookie_consent_message() ); ?> <a href="<?php echo esc_url( $cookie_url ); ?>">Read cookie policy</a>.</p>
		<button type="button" class="button" id="cookie-consent-accept">Accept</button>
	</div>
<?php wp_footer(); ?>
</body>
</html>