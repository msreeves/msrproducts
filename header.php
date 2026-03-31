<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
		  <script type="text/javascript">
    		var wp_ajax = "<?php echo admin_url('admin-ajax.php'); ?>";
			</script>   
    <title>MSR Products</title>
        <?php wp_head ();?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<?php
	$promo_text = 'Get free shipping on orders over $50.';
	$promo_url = home_url('/shop');
	$special_offers_url = home_url('/special-offers');

	if (function_exists('wc_get_product_ids_on_sale')) {
		$sale_product_ids = wc_get_product_ids_on_sale();
		$sale_count = is_array($sale_product_ids) ? count($sale_product_ids) : 0;
		$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
		$fallback_sale_url = add_query_arg('on_sale', '1', $shop_url);
		$resolved_special_offers_url = function_exists('msrproducts_get_special_offers_url')
			? msrproducts_get_special_offers_url()
			: $fallback_sale_url;
		$promo_url = $resolved_special_offers_url;
		$special_offers_url = $resolved_special_offers_url;

		if ($sale_count > 0) {
			$promo_text = sprintf(
				/* translators: %d: Number of sale products */
				_n('Special Offer: %d product on sale now.', 'Special Offers: %d products on sale now.', $sale_count, 'msrproducts'),
				(int) $sale_count
			);
		} else {
			$promo_text = 'Shop our latest products and featured collections.';
		}
	}
	?>
	<header id="masthead" class="site-header manic-site-header">
		<div class="promo-strip" role="region" aria-label="Promotions">
			<div class="container-fluid">
				<p class="promo-strip__text">
					<a href="<?php echo esc_url($promo_url); ?>">
						<span class="promo-pill">Special Offers</span>
						<span class="promo-message"><?php echo esc_html($promo_text); ?></span>
						<span class="promo-cta">Shop now</span>
					</a>
				</p>
			</div>
		</div>

		<div class="utility-nav" role="navigation" aria-label="Utility navigation">
			<div class="container-fluid utility-nav__inner">
				<ul class="utility-nav__list">
					<li><a href="<?php echo esc_url($special_offers_url); ?>">Special Offers</a></li>
					<li><a href="<?php echo esc_url(home_url('/find-a-store')); ?>">Find a Store</a></li>
					<li><a href="<?php echo esc_url(home_url('/my-account')); ?>">My Account</a></li>
				</ul>
				<div class="utility-nav__region">
					<button class="utility-nav__region-button" type="button" aria-label="Choose region">
						United States
					</button>
				</div>
			</div>
		</div>

		<nav id="site-navigation" class="navbar navbar-expand-lg navbar-light manic-nav" role="navigation" aria-label="Primary navigation">
			<div class="container-fluid">
				<a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
					<?php echo wp_get_attachment_image(get_theme_mod('custom_logo'), 'full'); ?>
				</a>

				<div class="header-actions header-actions--desktop" aria-label="Header actions">
					<button type="button" class="header-action-btn" data-nav-search-toggle aria-expanded="false" aria-controls="site-search-panel">Search</button>
					<a class="header-action-link" href="<?php echo esc_url(home_url('/my-account')); ?>">Sign In</a>
					<a class="header-action-link" href="<?php echo esc_url(home_url('/cart')); ?>">
						Bag
						<?php if (function_exists('WC')) : ?>
							<span class="cart-count">(<?php echo (int) WC()->cart->get_cart_contents_count(); ?>)</span>
						<?php endif; ?>
					</a>
				</div>

				<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
					aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="icon-bar top-bar"></span>
					<span class="icon-bar middle-bar"></span>
					<span class="icon-bar bottom-bar"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<div class="mobile-utility" aria-label="Mobile utility actions">
						<button type="button" class="header-action-btn" data-nav-search-toggle aria-expanded="false" aria-controls="site-search-panel">Search</button>
						<a class="header-action-link" href="<?php echo esc_url(home_url('/my-account')); ?>">Account</a>
						<a class="header-action-link" href="<?php echo esc_url(home_url('/cart')); ?>">Bag</a>
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

		<div id="site-search-panel" class="site-search-panel" hidden aria-hidden="true">
			<div class="container-fluid site-search-panel__inner">
				<form role="search" method="get" class="site-search-panel__form" action="<?php echo esc_url(home_url('/')); ?>">
					<label class="screen-reader-text" for="header-search-input">Search for:</label>
					<input id="header-search-input" type="search" name="s" placeholder="Search products..." />
					<button type="submit">Search</button>
				</form>
				<div class="site-search-panel__suggested">
					<span>Suggested:</span>
					<a href="<?php echo esc_url(home_url('/?s=Foundation')); ?>">Foundation</a>
					<a href="<?php echo esc_url(home_url('/?s=Concealer')); ?>">Concealer</a>
					<a href="<?php echo esc_url(home_url('/?s=Skincare')); ?>">Skincare</a>
				</div>
			</div>
		</div>
	</header>



    
