<?php
/**
 * Catalog / archive search bar.
 *
 * @package msrproducts
 */
?>
<div class="p-5 msr-site-search">
	<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Site search', 'msrproducts' ); ?>">
		<div class="input-group flex-wrap flex-md-nowrap">
			<label class="screen-reader-text" for="s"><?php echo esc_html_x( 'Search for:', 'label', 'msrproducts' ); ?></label>
			<input class="form-control" type="search" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" id="s" placeholder="<?php esc_attr_e( 'Search…', 'msrproducts' ); ?>" autocomplete="off" />
			<input class="btn btn-primary" type="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'msrproducts' ); ?>" />
		</div>
	</form>
</div>
