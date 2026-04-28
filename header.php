<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
		  <script type="text/javascript">
    		var wp_ajax = "<?php echo admin_url('admin-ajax.php'); ?>";
			</script>   
        <?php wp_head ();?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<?php
	$promo_text = 'Portfolio case studies, technical specs, and collaboration-ready project pages.';
	$promo_url = home_url('/shop');

	if (function_exists('wc_get_page_permalink')) {
		$shop_url = wc_get_page_permalink('shop');
		if ($shop_url) {
			$promo_url = $shop_url;
		}
	}

	$header_search_suggestions = function_exists( 'msrproducts_search_suggestions' ) ? msrproducts_search_suggestions( 10 ) : array();
	$header_search_items = function_exists( 'msrproducts_search_suggestion_items' ) ? msrproducts_search_suggestion_items( 20 ) : array();
	?>
	<header id="masthead" class="site-header manic-site-header">
		<div class="promo-strip" role="region" aria-label="Promotions">
			<div class="container-fluid">
				<p class="promo-strip__text">
					<a href="<?php echo esc_url($promo_url); ?>">
						<span class="promo-pill">Portfolio</span>
						<span class="promo-message"><?php echo esc_html($promo_text); ?></span>
						<span class="promo-cta">Explore projects</span>
					</a>
				</p>
			</div>
		</div>

		<div class="utility-nav" role="navigation" aria-label="Utility navigation">
			<div class="container-fluid utility-nav__inner">
				<div class="utility-nav__left">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'header-utility-menu',
							'container'      => false,
							'menu_class'     => 'utility-nav__list',
							'fallback_cb'    => false,
						)
					);
					?>
				</div>
				<div class="utility-nav__right">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'header-actions-menu',
							'container'      => false,
							'menu_class'     => 'header-actions-menu header-actions-menu--utility',
							'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							'fallback_cb'    => false,
							'walker'         => class_exists( 'MSRProducts_Header_Action_Walker' ) ? new MSRProducts_Header_Action_Walker() : '',
						)
					);
					?>
				</div>
			</div>
		</div>

		<nav id="site-navigation" class="navbar navbar-expand-md navbar-light manic-nav" role="navigation" aria-label="Primary navigation">
			<div class="container-fluid">
				<a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
					<?php
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					if ( $custom_logo_id && function_exists( 'msrproducts_attachment_file_exists' ) && msrproducts_attachment_file_exists( (int) $custom_logo_id ) ) {
						echo wp_get_attachment_image( $custom_logo_id, 'full' );
					} else {
						echo '<img src="' . esc_url( function_exists( 'msrproducts_placeholder_image_url' ) ? msrproducts_placeholder_image_url() : '' ) . '" alt="' . esc_attr__( 'Site logo', 'msrproducts' ) . '" class="custom-logo" loading="lazy" decoding="async" />';
					}
					?>
				</a>

				<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
					aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="icon-bar top-bar"></span>
					<span class="icon-bar middle-bar"></span>
					<span class="icon-bar bottom-bar"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<div class="mobile-utility" aria-label="Mobile utility actions">
						<button type="button" class="header-action-btn" data-nav-search-toggle aria-expanded="false" aria-controls="site-search-panel">Search</button>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'header-actions-menu',
								'container'      => false,
								'menu_class'     => 'header-actions-menu',
								'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'fallback_cb'    => false,
								'walker'         => class_exists( 'MSRProducts_Header_Action_Walker' ) ? new MSRProducts_Header_Action_Walker() : '',
							)
						);
						?>
					</div>
					<?php
					wp_nav_menu(
						array(
							'theme_location'  => 'menu-1',
							'menu_id'         => 'primary-menu',
							'container_id'    => 'cssmenu',
							'container_class' => 'site-mega-nav',
							'menu_class'      => 'manic-menu',
							'walker'          => new CSS_Menu_Walker()
						)
					);
					?>
				</div>
			</div>
		</nav>
		<style id="msr-nav-desktop-hover-fallback">
			@media (min-width: 768px) {
				#cssmenu > ul.manic-menu > li.has-sub:hover > ul.mega-menu,
				#cssmenu > ul.manic-menu > li.has-sub:focus-within > ul.mega-menu {
					opacity: 1 !important;
					visibility: visible !important;
					pointer-events: auto !important;
					transform: translateY(0) !important;
				}
			}
			@media (max-width: 767.98px) {
				#cssmenu > ul.manic-menu > li.has-sub.expanded > ul.mega-menu {
					opacity: 1 !important;
					visibility: visible !important;
					pointer-events: auto !important;
					transform: none !important;
				}
			}
		</style>
		<script>
		(function() {
			if (typeof window === 'undefined' || typeof document === 'undefined') {
				return;
			}

			document.addEventListener('DOMContentLoaded', function() {
				var nav = document.getElementById('site-navigation');
				var collapse = document.getElementById('navbarSupportedContent');
				var cssMenu = document.getElementById('cssmenu');
				var navToggler = nav ? nav.querySelector('.navbar-toggler') : null;

				if (navToggler && collapse) {
					collapse.addEventListener('shown.bs.collapse', function() {
						navToggler.setAttribute('aria-expanded', 'true');
						navToggler.classList.remove('collapsed');
						if (nav) {
							nav.classList.add('toggled');
						}
					});

					collapse.addEventListener('hidden.bs.collapse', function() {
						navToggler.setAttribute('aria-expanded', 'false');
						navToggler.classList.add('collapsed');
						if (nav) {
							nav.classList.remove('toggled');
						}
					});
				}

				if (!cssMenu) {
					return;
				}

				var desktopItems = cssMenu.querySelectorAll('ul.manic-menu > li.has-sub');
				if (!desktopItems.length) {
					return;
				}

				var desktopHoverTimers = new WeakMap();
				function desktopHoverAllowed() {
					return window.matchMedia('(min-width: 768px) and (hover: hover) and (pointer: fine)').matches;
				}
				function clearDesktopTimer(item) {
					var timer = desktopHoverTimers.get(item);
					if (!timer) {
						return;
					}
					window.clearTimeout(timer);
					desktopHoverTimers.delete(item);
				}
				function closeDesktopItems(exceptItem) {
					desktopItems.forEach(function(node) {
						if (exceptItem && node === exceptItem) {
							return;
						}
						node.classList.remove('is-open');
						var anchor = node.querySelector(':scope > a');
						if (anchor) {
							anchor.setAttribute('aria-expanded', 'false');
						}
					});
				}

				desktopItems.forEach(function(item) {
					var submenu = item.querySelector(':scope > ul.mega-menu, :scope > ul.sub-menu, :scope > ul');
					function scheduleClose() {
						clearDesktopTimer(item);
						var timerId = window.setTimeout(function() {
							item.classList.remove('is-open');
							var anchor = item.querySelector(':scope > a');
							if (anchor) {
								anchor.setAttribute('aria-expanded', 'false');
							}
							desktopHoverTimers.delete(item);
						}, 190);
						desktopHoverTimers.set(item, timerId);
					}

					item.addEventListener('mouseenter', function() {
						if (!desktopHoverAllowed()) {
							return;
						}
						clearDesktopTimer(item);
						closeDesktopItems(item);
						item.classList.add('is-open');
						var anchor = item.querySelector(':scope > a');
						if (anchor) {
							anchor.setAttribute('aria-expanded', 'true');
						}
					});

					item.addEventListener('mouseleave', function() {
						if (!desktopHoverAllowed()) {
							return;
						}
						scheduleClose();
					});

					if (submenu) {
						submenu.addEventListener('mouseenter', function() {
							if (!desktopHoverAllowed()) {
								return;
							}
							clearDesktopTimer(item);
						});

						submenu.addEventListener('mouseleave', function() {
							if (!desktopHoverAllowed()) {
								return;
							}
							scheduleClose();
						});
					}
				});
			});
		})();
		</script>

		<div id="site-search-panel" class="site-search-panel" hidden aria-hidden="true" data-predictive-search>
			<div class="container-fluid site-search-panel__inner">
				<form role="search" method="get" class="site-search-panel__form" action="<?php echo esc_url(home_url('/')); ?>">
					<label class="screen-reader-text" for="header-search-input">Search for:</label>
					<input id="header-search-input" type="search" name="s" placeholder="Search products..." list="header-search-suggestions" autocomplete="off" data-predictive-search-input />
					<button type="submit">Search</button>
				</form>
				<datalist id="header-search-suggestions">
					<?php foreach ( $header_search_suggestions as $suggestion ) : ?>
						<option value="<?php echo esc_attr( $suggestion ); ?>"></option>
					<?php endforeach; ?>
				</datalist>
				<div id="header-search-autocomplete" class="site-search-autocomplete" data-predictive-search-box hidden>
					<p class="site-search-autocomplete__title">Suggestions</p>
					<ul class="site-search-autocomplete__list" data-predictive-search-list></ul>
				</div>
				<script type="application/json" id="header-search-items-json" data-search-items-json><?php echo wp_json_encode( $header_search_items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
				<div class="site-search-panel__suggested">
					<span>Suggested:</span>
					<a href="<?php echo esc_url(home_url('/?s=Foundation')); ?>">Foundation</a>
					<a href="<?php echo esc_url(home_url('/?s=Concealer')); ?>">Concealer</a>
					<a href="<?php echo esc_url(home_url('/?s=Skincare')); ?>">Skincare</a>
				</div>
			</div>
		</div>
	</header>



    
